<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 9/15/2015
 * Time: 4:03 PM
 */
class UiPluginPageurl{

    public function execute($params)
    {
        $pageId = intval($params['pageId']);
        if(empty($pageId)) {
            throw new FwException("Can't create url without page id");
        }
        $local = !empty($params['local']);
        if(!$local) {
            $absolute = !empty($params['absolute']);
        } else {
            $absolute = false;
        }
        /** @var $pageService PageService */
        $pageService = Core::getModule("Page")->getService();
        /** @var $page PersistPages*/
        $page = $pageService->load($pageId);
        $out = $local ? CoreConfig::getLocalUrl() : ($absolute ? CoreConfig::getUrl() : '');
        if(empty($page)) {
            return $out;
        }
        return $out.$pageService->getPagePath($page);
    }
}