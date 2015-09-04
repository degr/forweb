<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 21.03.2015
 * Time: 16:12
 */
class Access extends Module{
    protected $access;
    /**
     * Names of available access groups
     * @var array
     */
    protected $accessGroups;

    /**
     * Rewrite access file if access groups or access actions was changed.
     * $this->access variable contain this class instance
     */
    public static function rewriteAccessFile()
    {
        //@TODO generate access file in modules root (UserAccess)
    }


    public function getAccess(){
        if($this->access == null) {
            $this->access = DB::getTable("SELECT * FROM access", 'action');
        }
        return $this->access;
    }

    const GROUP_ANONIMOUS = "anonymous";
    const GROUP_AUTHORIZED = "authorized";
    const GROUP_ADMINISTRATOR = "administrator";


    /**
     * @param string|string[] $action
     * @param boolean $strict
     */
    public static function denied($action, $strict = false)
    {
        if(!is_array($action)) {
            $action = array($action);
        }
        $can = null;
        foreach($action as $act) {
            $temp = Access::can($act);
            if($strict) {
                if(!$temp) {
                    $can = false;
                    break;
                } else {
                    $can = true;
                }
            } else {
                if($can === true && !$temp) {
                    continue;
                } else {
                    $can = $temp;
                }
            }
        }
        if (!$can) {
            /* @var $userModule User */
            $userModule = Core::getModule('User');
            /* @var $user PersistUser */
            $user = $userModule->getUser();
            if ($user == null) {
                header("HTTP/1.1 403 Unauthorized");
            } else {
                header("HTTP/1.0 403 Forbidden");
            }
            exit;
        }
    }


    public function getAjaxHandlers()
    {
        if($this->ajaxHandlers == null) {
            $this->ajaxHandlers = array();
            $this->ajaxHandlers['getAccessForm'] = new AjaxHandler("getAccessForm", AjaxHandler::JSON);
            $this->ajaxHandlers['switchAccess'] = new AjaxHandler("switchAccess", AjaxHandler::JSON);
            $this->ajaxHandlers['editActionName'] = new AjaxHandler("editActionName", AjaxHandler::JSON);
            $this->ajaxHandlers['deleteAction'] = new AjaxHandler("deleteAction", AjaxHandler::JSON);
            $this->ajaxHandlers['deleteAccessGroup'] = new AjaxHandler("deleteAccessGroup", AjaxHandler::JSON);
            $this->ajaxHandlers['createAccessGroup'] = new AjaxHandler("createAccessGroup", AjaxHandler::JSON);
            $this->ajaxHandlers['createAccessAction'] = new AjaxHandler("createAccessAction", AjaxHandler::JSON);
        }
        return $this->ajaxHandlers;
    }

    public function getAccessForm(){
        $provider = new AccessAdminForm();
        return $provider->getForm();
    }

    public function switchAccess(){
        $provider = new AccessAdminForm();
        return $provider->switchAccess();
    }

    /**
     * Get access groups as array with names
     * @return array
     */
    public function getAccessGroups(){
        if($this->accessGroups == null) {
            $this->accessGroups = DB::getColumn("SHOW COLUMNS FROM access");
            array_shift($this->accessGroups);
            array_shift($this->accessGroups);
        }
        return $this->accessGroups;
    }

    public function editActionName(){
        $provider = new AccessAdminForm();
        return $provider->editActionName();
    }
    public function deleteAction(){
        $provider = new AccessAdminForm();
        return $provider->deleteAction();
    }
    public function deleteAccessGroup(){
        $provider = new AccessAdminGroup();
        return $provider->deleteAccessGroup();
    }

    public function createAccessAction(){
        $provider = new AccessAdminForm();
        return $provider->createAccessAction();
    }

    public function createAccessGroup(){
        $provider = new AccessAdminGroup();
        return $provider->createAccessGroup();
    }

    public static function can($action, $group = ''){
        if(empty($group)) {
            /* @var $userModule User */
            $userModule = Core::getModule('User');
            /* @var $user PersistUser */
            $user = User::getUser();
            if($user == null) {
                $group = Access::GROUP_ANONIMOUS;
            } else {
                $group = $user->getAccess();
            }
        }

        /* @var $access Access */
        $access = Core::getModule("Access");

        if(!in_array($group, $access->getAccessGroups())) {
            return false;
        }
        $accessObject = $access->getAccess();
        return !empty($accessObject[$action][$group]);
    }

    /**
     * Get module event handlers
     * @return EventHandler[]
     */
    public function getEventHandlers()
    {
        // TODO: Implement getEventHandlers() method.
    }
}