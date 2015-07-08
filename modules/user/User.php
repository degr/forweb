<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 21.03.2015
 * Time: 16:00
 */
class User extends Module{
    const EVENT_AUTHORIZATION = "authorization_event";
    const EVENT_LOGOUT = 'logout_event';
    /**
     * @var PersistUser
     */
    protected static $user;

    const USER_ID = "user_id";

    /**
     * Get current user id
     * @return int
     */
    public static function getUserId()
    {
        if(!empty($_SESSION[User::USER_ID])) {
            return $_SESSION[User::USER_ID];
        } else {
            return 0;
        }
    }

    /**
     * Set current user id
     * @param $id int
     */
    private static function setUserId($id)
    {
        $_SESSION[User::USER_ID] = $id;
    }


    public function getAjaxHandlers()
    {
        if($this->ajaxHandlers == null) {
            $this->ajaxHandlers = array(
                'authorization' => new AjaxHandler('onAjaxAuthorization', AjaxHandler::JSON),
                'logout' => new AjaxHandler('onAjaxLogout', AjaxHandler::JSON)
            );
        }
        return $this->ajaxHandlers;
    }


    /**
     * Get current user
     * @return PersistUser
     */
    public static function getUser(){
        if(User::$user === null && User::getUserId() !== 0) {
            $service = Core::getModule("User")->getService();
            $id = User::getUserId();
            User::$user = $service->load($id);
        }
        return User::$user;
    }

    /**
     * User authorization form controller
     *
     */
    public function onAjaxAuthorization(){
        $provider = new User_Actions();
        $dto = $provider->authorization($_POST['email'], $_POST['password']);
        if($dto['success']) {
            User::$user = $dto['user'];
            User::setUserId(User::$user->getId());
            Core::triggerEvent(User::EVENT_AUTHORIZATION, array('id'=>User::$user->getId()));
            $dto['user'] = User::$user->toJson();
        }
        return $dto;
    }


    /**
     * User authorization form UI controller.
     * Prepare data for form rendering
     * @param UI $ui
     */
    public function getAuthorizationForm(UI $ui){
        if(User::getUser() == null) {
            $auth = null;
            if(isset($_POST['email']) && isset($_POST['password'])) {
                $auth = $this->onAjaxAuthorization();
            }
            $provider = new User_Gui_Forms();
            $provider->getAuthorizationForm($ui, $auth['errors']);
        }
    }

    /**
     * User authorization form UI controller.
     * Prepare data for form rendering
     * @param UI $ui
     */
    public function getLogOutForm(UI $ui){
        if(User::getUser() != null) {
            $provider = new User_Gui_Forms();
            $provider->getLogOutForm($ui);
        }
    }

    /**
     * User authorization form UI controller.
     * Prepare data for form rendering
     */
    public function onAjaxLogout(){
        if(User::getUser() != null) {
            $userId = User::getUserId();
            User::setUserId(0);
            User::$user = null;
            Core::triggerEvent(User::EVENT_LOGOUT, array('userId'=>$userId));
        }
        Core_Utils::redirectToHome();
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