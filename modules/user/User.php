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
        // TODO: Implement getAjaxHandlers() method.
    }
    /**
     * Get module form handlers
     * @return FormHandler[]
     */
    public function getFormHandlers()
    {
        if($this->formHandlers == null) {
            $this->formHandlers = array(
                'authorization' => new FormHandler('authorization'),
                'logout' => new FormHandler('logout')
            );
        }
        return $this->formHandlers;
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
     * @param FormHandler $handler
     */
    public function authorization(FormHandler $handler){
        $provider = new User_Actions();
        $user = $provider->authorization($handler);
        if($user != null) {
            User::$user = $user;
            User::setUserId($user->getId());
            Core::triggerEvent(User::EVENT_AUTHORIZATION, array('id'=>$user->getId()));
        }
    }


    /**
     * User authorization form UI controller.
     * Prepare data for form rendering
     * @param UI $ui
     */
    public function getAuthorizationForm(UI $ui){
        if(User::getUser() == null) {
            $provider = new User_Gui_Forms();
            $provider->getAuthorizationForm($ui);
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
     * @param FormHandler $handler
     */
    public function logout(FormHandler $handler){
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