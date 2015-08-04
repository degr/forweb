<?php

/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/14/2015
 * Time: 10:42 AM
 */
class Cms_Sitemap
{

    const LAYOUT = 'cms/sitemap.tpl';

    public function getLinks($includeUrl = true)
    {
        /** @var $pageService Page_Service */
        $pageService = Core::getModule("Page")->getService();
        $pages = $pageService->loadAll();
        $url = $includeUrl ? Config::getUrl() : '';
        $out = array();
        /** @var $page PersistPages */
        foreach($pages as $page) {
            $out[$page->getId()] = $url.$pageService->getPagePath($page);
        }
        return $out;
    }
}