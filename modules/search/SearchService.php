<?php

/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 8/5/2015
 * Time: 12:14 PM
 */
class SearchService
{

    const RESULTS_ON_PAGE = 20;
    const SEARCH_BODY = "select w.value, l.locale, i.page from word as w inner join languages as l on l.id = w.language inner join word_modules as m on w.module = m.id inner join includes as i on i.id = w.name inner join pages as p on i.page = p.id where m.module = 'sys_includes' and i.page <> 0 and p.active = 1 ";
    const MAX_TEXT_LENGTH = 117;
    const TEXT_SHIFT = 40;

    private $encoding;
    
    public function __construct(){
        $this->encoding = DB::getEncoding();
    }
    
    public function onPageSearch($search, $page)
    {
        $language = Word::getLanguage();
        $rest = self::RESULTS_ON_PAGE;

        $equal = $this->onEqualSearch($search, $page, self::RESULTS_ON_PAGE, $language);
        $count = array('equal' => count($equal));
        if($count['equal'] == $rest) {
            return $equal;
        }
        $rest -= $count['equal'];

        $like = $this->onLikeSearch($search, $page, $rest, $language);
        $count['like'] = count($like);
        if($count['like'] == $rest) {
            return array_merge($equal, $like);
        }
        $rest -= $count['like'];

        $multyLangEqual = $this->onEqualSearch($search, $page, $rest, null);
        $count['multyLangEqual'] = count($multyLangEqual);
        if($count['multyLangEqual'] == $rest) {
            return array_merge($equal, $like, $multyLangEqual);
        }
        $rest -= $count['multyLangEqual'];

        $multyLangLike = $this->onEqualSearch($search, $page, $rest, null);
        $count['multyLangLik'] = count($multyLangEqual);
        if($count['multyLangLik'] == $rest) {
            return array_merge($equal, $like, $multyLangEqual, $multyLangLike);
        }
        $rest -= $count['multyLangLik'];


        $parts = preg_split("/(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])|(_{1,})|( {1,})/", $search);
        $compatible = $this->onCompatibleSearch($parts, $page, $rest);
        return array_merge($equal, $like, $multyLangEqual, $multyLangLike, $compatible);
    }

    private function onEqualSearch($search, $page, $count, $language)
    {
        $query = self::SEARCH_BODY." and value = '".DB::escape($search)."'"
            .($language != null ? " and language = ".intval($language) : "");
        return $this->handleSearchResult($query, $page, $count, array($search));
    }

    private function onLikeSearch($search, $page, $count, $language)
    {
        $query = self::SEARCH_BODY." and value like '%".DB::escape($search)."%'"
            .($language != null ? " and language = ".intval($language) : "");
        return $this->handleSearchResult($query, $page, $count, array($search));
    }

    private function onCompatibleSearch($parts, $page, $count)
    {
        foreach($parts as &$part) {
            $part = " value like '%".DB::escape($part)."%'";
        }
        $query = self::SEARCH_BODY." and (".implode(" or ", $parts).") ";
        return $this->handleSearchResult($query, $page, $count, $parts);
    }

    private function handleSearchResult($query, $page, $count, $highlightArray)
    {
        /** @var $pages PersistPages[] */
        static $pages;
        $limitsQuery = $query.(empty($pages) ? '' : ' and page not in ('.implode(',', array_keys($pages)).')')
            ." limit ".$page.', '.$count;
        /** @var $pageService PageService */
        $pageService = Core::getModule('Page')->getService();
        $result = DB::getTable($limitsQuery);
        foreach($result as &$item) {
            if(empty($pages[$item['page']])) {
                $pages[$item['page']] = $pageService->load($item['page']);
            }
            if($pages[$item['page']] == null) {
                $item['url'] = 'not defined';
                continue;
            }
            $item['value'] = $this->onHighLight($item['value'], $highlightArray);
            $item['url'] = CoreConfig::getUrl().$pageService->getPagePath($pages[$item['page']]);
            $item['name'] = $pages[$item['page']]->getName();
        }
        unset($item);
        return $result;
    }

    private function onHighLight($value, $highlightArray)
    {
        $value = strip_tags($value);
        $minPosition = -1;
        $lowered = mb_strtolower($value, $this->encoding);
        foreach($highlightArray as &$item) {
            $item = mb_strtolower($item, $this->encoding);
            $position = mb_strpos($lowered, $item, null, $this->encoding);
            if($position !== false && ($minPosition == -1 || $minPosition > $position)) {
                $minPosition = $position;
            }
        }
        $length = mb_strlen($value, $this->encoding);
        if($length <= self::MAX_TEXT_LENGTH) {
            $startPosition = 0;
        } else if($minPosition + self::MAX_TEXT_LENGTH + self::TEXT_SHIFT >= $length) {
            $startPosition = $length - self::MAX_TEXT_LENGTH - self::TEXT_SHIFT;
            if($startPosition < 0) {
                $startPosition = 0;
            }
        } else {
            $startPosition = $minPosition - self::TEXT_SHIFT > 0 ? $minPosition - self::TEXT_SHIFT : 0;
        }
        $realStartPosition = mb_strpos($value, " ", $startPosition, $this->encoding);
        if($realStartPosition !== false && $realStartPosition < $minPosition) {
            $startPosition = $realStartPosition;
        }
        $lastOffset = $startPosition + self::MAX_TEXT_LENGTH;
        if($lastOffset < $length) {
            $substringLength = mb_strpos($value, " ", $lastOffset, $this->encoding);
            if($substringLength === false) {
                $substringLength = $length;
            }
        } else {
            $substringLength = $length;
        }
        $substringLength = $substringLength - $startPosition;

        $value = mb_substr($value, $startPosition, $substringLength, $this->encoding);
        foreach($highlightArray as $item) {
            $value = str_ireplace($item, "<b>".$item."</b>", $value);
        }

        return $value.'...';
    }
}