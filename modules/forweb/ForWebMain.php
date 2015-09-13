<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 24.03.2015
 * Time: 22:04
 */
class ForWebMain{
    public function onHeaderMenu(UI $ui){
        $ui->setLayout("forweb/common/headerMenu.tpl");
        /* @var $pageModule Page */
        $pageModule = Core::getModule("Page");
        /* @var $service PageService */
        $service =  $pageModule->getService();
        $ui->addVariable("pages", $this->generateMenu(1, true, true));
        $ui->addVariable("pageId", $service->getCurrentPage()->getId());
    }

    public function onSidebarSubmenu(UI $ui){
        $ui->setLayout("forweb/common/sidebarSubmenu.tpl");
        /* @var $pageModule Page */
        $pageModule = Core::getModule("Page");
        $param = Core::getPathParam(0);
        /* @var $service Page_Service */
        $service =  $pageModule->getService();
        if(empty($param)) {
            $pageId = $service->getCurrentPage()->getId();
        } else {
            $urlFilter = new ORM_Query_Filter($service->getTable()->getName(), "url", ORM_Query_Filter::TYPE_EQUAL);
            $urlFilter->setActive(true);
            $urlFilter->setValue($param);
            $parentFilter = new ORM_Query_Filter($service->getTable()->getName(), "parent", ORM_Query_Filter::TYPE_EQUAL);
            $parentFilter->setActive(true);
            $parentFilter->setValue(1);
            $page = $service->loadOneWithFilters(array($parentFilter, $urlFilter));
            $pageId = $page->getPrimaryKey();
        }
		//var_dump($pageId);exit;
        $ui->addVariable("pages", $this->generateMenu($pageId, false, false));
        $ui->addVariable("pageId", $pageId);
    }

    private function generateMenu($parent, $includeHome, $addInfo){
        $out = array();
        //@TODO add order by position
        /* @var $pageModule Page */
        $pageModule = Core::getModule("Page");
        /* @var $service Page_Service */
        $service =  $pageModule->getService();
        $customFilter = new ORM_Query_CustomFilter("  (".($includeHome ? "pages.id=1 OR " : "")." pages.parent = ".$parent.") AND in_menu = 1 ", true);
        $pages = $service->loadAll($customFilter);
        /* @var $page PersistPages */
        foreach($pages as &$page) {
            $pageUrl = $service->getPagePath($page);
            $json = $page->toJson();
            $json['active'] = $service->getCurrentPage()->getId() == $page->getId() ? "active" : "";
            if($addInfo) {
                $json['info'] = Word::get("info", "page_info_" . $page->getId());
            }
            $json['url'] = $pageUrl;
            $out[] = $json;
        }
        return $out;
    }
}