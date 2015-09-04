<?php

/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/14/2015
 * Time: 10:42 AM
 */
class CmsSitemap
{

    const LAYOUT = 'cms/sitemap.tpl';

    public function getLinks($includeUrl = true)
    {
        /** @var $pageService PageService */
        $pageService = Core::getModule("Page")->getService();
        $filter = new OrmQueryFilter('pages', 'active', OrmQueryFilter::TYPE_EQUAL);
        $filter->setActive(true);
        $filter->setValue(1);
        $pages = $pageService->loadAll($filter);
        $url = $includeUrl ? Config::getUrl() : '';
        $out = array();
        $languages = Word::getLanguages();
        /** @var $page PersistPages */
        foreach($pages as $page) {
            if(Core::MULTIPLE_LANGUAGES && Core::LANGUAGE_IN_URL) {
                foreach($languages as $language) {
                    $item = $this->buildPageItem($url .$language['locale'] .'/'. $pageService->getPagePath($page), $page);
                    $item['alternate'] = $this->buildAlternate($page, $languages, $language, $url);
                    $out[] = $item;
                }
            } else {
                $out[] = $this->buildPageItem($url . $pageService->getPagePath($page), $page);
            }
        }
        return $out;
    }

    private function buildPageItem($url, $page) {
        return array(
            'url' => $url,
            'lastmod' => null,
            'changefreq' => null,
            'priority' => null
        );
    }

    private function buildAlternate($page, $languages, $language, $url)
    {
        $out = array();
        /** @var $pageService PageService */
        $pageService = Core::getModule("Page")->getService();
        foreach($languages as $alternateLanguage) {
            if($alternateLanguage['id'] === $language['id']) {
                continue;
            }
            $out[] = array(
                'language' => $alternateLanguage['locale'],
                'url' => $url .$alternateLanguage['locale'].'/'. $pageService->getPagePath($page), $page
            );
        }
        return $out;
    }
}