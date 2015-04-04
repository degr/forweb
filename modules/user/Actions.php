<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 24.03.2015
 * Time: 20:03
 */
class User_Actions{
    public function authorization(FormHandler $handler){
        if(empty($_POST['email'])) {
            $handler->addError('email', UI_Validation::REQUIRED);
        }
        if(empty($_POST['password'])) {
            $handler->addError('password', UI_Validation::REQUIRED);
        }
        $email = $_POST['email'];
        $password = $_POST['password'];
        $userController = Core::getModule("User");

        $emailFilter = new ORM_Query_Filter('user', 'email', ORM_Query_Filter::TYPE_EQUAL);
        $emailFilter->setValue($email);
        $passwordFilter =  new ORM_Query_Filter('password', 'email', ORM_Query_Filter::TYPE_EQUAL);
        $passwordFilter->setValue($password);
        $usersList = $userController->getService()->loadAll(array($emailFilter, $passwordFilter), null, null);

        $out = null;
        /* @var $user PersistUser */
        foreach($usersList as $user) {
            if($user->getPassword() === $password) {
                $out = $user;
                break;
            }
        }
        if($out == null) {
            $handler->addError('email', UI_Validation::CUSTOM);
            return null;
        } else {
            return $out;
        }
    }
}