<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 14.03.2015
 * Time: 21:15
 */

class PageService extends ModuleService {

    /**
     * Current page for request
     * @var PersistPages
     */
    protected $page;

    /**
     * Current template for request
     * @var PersistTemplates
     */
    protected $template;

    public function __construct(){
        parent::__construct("pages");
    }

    public function getCurrentPage(){
        return $this->page;
    }
    public function setCurrentPage($page){
        return $this->page = $page;
    }

    /**
     * @return PersistTemplates
     */
    public function getTemplate(){
        if($this->template === null) {
            $this->template = $this->page->getTemplate();
        }
        return $this->template;
    }

    /**
     * @param $params
     * @return array('page' => PersistPages, 'position'=>integer)
     */
    public function findPage($params){
        $filterQuery = " pages.id=1 OR pages.url IN ('".implode("','", $params)."') ORDER BY pages.id";
        $filter = new OrmQueryCustomFilter($filterQuery, true);
        $pages = $this->loadAll($filter);

        $detectedPage = reset($pages);
        $position = 0;
        foreach($params as $param) {
            $empty = true;
            /* @var $page PersistPages */
            foreach($pages as $page){
                if($page->getId() == 1){
                    $empty = false;
                    continue;
                }
                if($page->getUrl() === $param && $detectedPage->getId() === $page->getParent()) {
                    $empty = false;
                    $detectedPage = $page;
                    $position++;
                    break;
                }
            }
            if($empty) {
                break;
            }
        }
        return array('page' => $detectedPage, 'position' => $position);
    }

    /**
     * Get full path for current page
     * as an exmple - input page is 'forum' and parent for this page - 'home'
     * 'home/forum' will be returned
     * @param PersistPages $page
     * @return string
     */
    public function getPagePath(PersistPages $page){
        static $cache;
        //home page hack
        if($page->getParent() === '0') {
            return "";
        }
        if(empty($cache[$page->getId()])){
            $cache[$page->getId()] = $this->getPagePathRecoursivly($page->getParent(), $page->getUrl(), "");
        }
        return $cache[$page->getId()];
    }

    /**
     * Build path for input page
     * @param $parent
     * @param $url
     * @param $path
     * @return mixed
     */
    protected function getPagePathRecoursivly($parent, $url, $path) {
        static $cache;
        if($parent == 0) {
            $path = preg_replace('/^\//', '', $path);
            if(empty($path)){
                $path = $url;
            }
            return $path;
        } else {
            static $urlStorage;
            if(empty($urlStorage[$parent])){
                if(empty($cache[$parent])) {
                    $cache[$parent] = $this->load($parent);
                }
                /* @var $parentPage PersistPages */
                $parentPage =  $cache[$parent];
                $urlStorage[$parent]['parent'] = $parentPage->getParent();
                $urlStorage[$parent]['url'] = $parentPage->getUrl();

            }
            return $this->getPagePathRecoursivly(
                $urlStorage[$parent]['parent'],
                $urlStorage[$parent]['url'],
                "/".$url.$path
            );
        }
    }


    public function parseUrlForParams($url){
        $dispatcher = new PageDispatcher($url);
        $dispatcher->handleRequest();
        $params = $dispatcher->getParams();
        if(Core::MULTIPLE_LANGUAGES && Core::LANGUAGE_IN_URL && count($params) > 0) {
            array_shift($params);
        }
        return $params;
    }

    /**
     * Override parent method. Cache result
     * @param int $key
     * @return PersistPages
     */
    public function load($key) {
        static $cache;
        if(empty($cache[$key])) {
            $cache[$key] = parent::load($key);
        }
        return $cache[$key];
    }
}