<?
class Cms implements IModule{

    protected static $ajaxHandlers;
    public static function ajaxHandler($moduleName, $handlerName){
        $moduleName = ucfirst($moduleName);
        if(Core::isModuleExist($moduleName)) {
            /* @var $obj IModule */
            $obj = Core::getModule($moduleName);
            $handlers = $obj->getAjaxHandlers();
            if(!empty($handlers[$handlerName])) {
                $handler = $handlers[$handlerName];
            } else {
                $handler = null;
            }
            /* @var $handler AjaxHandler */
            if($handler != null) {
                $function = $handler->getMethod();
                $out = $obj->$function();
                if (isset($out)) {
                    CMS::sendHeaders($handler->getResponse());
                    if ($handler->getResponse() === AjaxHandler::JSON) {
                        echo json_encode($out);
                    } else {
                        echo $out;
                    }
                }
                exit;
            }
        }
        echo "unknown ajax handler";
        exit;
    }
    public static function sendHeaders($response) {
        if($response === AjaxHandler::JSON) {
            header('Content-Type: application/json; charset=utf-8');
        } else {
            header('Content-Type: text/html; charset=utf-8');
        }
        header("Pragma: no-cache");
        header("Cache-control: private, must-revalidate");
        header("Content-disposition: inline");
    }
    /**
     * Get admin panel's javascript files
     * @param UI $ui
     * @throws Exception
     */
    public function getAdminPanel(UI $ui) {
        if(Access::can("can_see_admin_panel")) {
            $ui->addVariable(
                'admin_translations',
                addslashes(json_encode(Word::get('admin')))
            );
            $ui->addVariable("isMultipleLanguages", Core::MULTIPLE_LANGUAGES);
            $ui->addVariable('adminIncludeOptions', addslashes(json_encode(Page::getIncludeTypesList())));
            $ui->addVariable('url', Config::get("url"));
            $ui->setLayout('page/admin/main.tpl');
        }
    }
    /**
     * Get module ajax handlers
     * @return AjaxHandler[]
     */
    public function getAjaxHandlers()
    {
        // TODO: Implement getAjaxHandlers() method.
    }
    /**
     * Get module ajax handler with selected name
     * @param string $name
     * @return AjaxHandler
     */
    public function getAjaxHandler($name)
    {
        // TODO: Implement getAjaxHandler() method.
    }
    /**
     * Generate site header menu
     * @param UI $ui
     */
    public function onHeaderMenu(UI $ui){
        $provider = new CMS_Menu();
        $provider->onHeaderMenu($ui);
    }
    /**
     * Generate sidebar submenu
     * @param UI $ui
     */
    public function onSidebarSubmenu(UI $ui){
        $provider = new CMS_Menu();
        $provider->onSidebarSubmenu($ui);
    }
    /**
     * Get module event handlers
     * @return EventHandler[]
     */
    public function getEventHandlers()
    {
        // TODO: Implement getEventHandlers() method.
    }


    public function onSitemapDisplay(UI $ui){
        $provider = new Cms_Sitemap();
        $ui->addVariable('links', $provider->getLinks());
        $ui->setLayout(Cms_Sitemap::LAYOUT);
        echo $ui->getLayout();
        exit;
    }
}