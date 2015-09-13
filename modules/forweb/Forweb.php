<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 24.03.2015
 * Time: 22:03
 */
class ForWeb extends Module{

    /**
     * Get module ajax handlers
     * @return AjaxHandler[]
     */
    public function getAjaxHandlers()
    {
        // TODO: Implement getAjaxHandlers() method.
    }

	
    /**
     * Get module form handlers
     * @return FormHandler[]
     */
    public function getEventHandlers()
    {
        // TODO: Implement getFormHandlers() method.
    }

    /**
     * Generate site header menu
     * @param UI $ui
     */
    public function onHeaderMenu(UI $ui){
        $provider = new ForWebMain();
        $provider->onHeaderMenu($ui);
    }

    /**
     * Generate sidebar submenu
     * @param UI $ui
     */
    public function onSidebarSubmenu(UI $ui){
        $provider = new ForWebMain();
        $provider->onSidebarSubmenu($ui);
    }
}