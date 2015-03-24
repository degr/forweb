<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 21.03.2015
 * Time: 16:00
 */
class User extends Module{

    /**
     * @var PersistUser
     */
    protected static $user;

    const USER_ID = "user_id";

    /**
     * Get current user id
     * @return int
     */
    private static function getUserId()
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
    {   echo "user id setted: ".$id;
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
                'authorization' => new FormHandler('authorization')

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

    public function authorization(FormHandler $handler){
        if(empty($_POST['email'])) {
            $handler->addError('email', UI_Validation::REQUIRED);
        }
        if(empty($_POST['password'])) {
            $handler->addError('password', UI_Validation::REQUIRED);
        }
        $email = $_POST['email'];
        $password = $_POST['password'];
        $usersList = $this->getService()->loadAll(" WHERE user.email='".DB::escape($email)
            ."' AND user.password='".DB::escape($password)."'");


        /* @var $user PersistUser */
        foreach($usersList as $user) {
            if($user->getPassword() === $password) {
                User::$user = $user;
                User::setUserId($user->getId());
                break;
            }
        }
        if(User::getUser() == null) {
            $handler->addError('email', UI_Validation::CUSTOM);
        }
    }



    public function getAuthorizationForm(UI $ui){
        if(User::getUser() == null) {
            $provider = new User_Gui_Forms();
            $provider->getAuthorizationForm($ui);
        }
    }


}