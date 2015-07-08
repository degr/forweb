<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 24.03.2015
 * Time: 20:03
 */
class User_Actions{
    public function authorization($email, $password){
        $out = array(
            'success' => null,
            'errors' => array(),
            'user' => null
        );
        if(empty($email)) {
            $out['errors'][] = array('name'=>'email', 'code' => UI_Validation::REQUIRED);
        }
        if(empty($password)) {
            $out['errors'][] = array('name'=>'password', 'code' => UI_Validation::REQUIRED);
        }

        $userController = Core::getModule("User");

        $emailFilter = new ORM_Query_Filter('user', 'email', ORM_Query_Filter::TYPE_EQUAL);
        $emailFilter->setValue($email);
        $passwordFilter =  new ORM_Query_Filter('password', 'email', ORM_Query_Filter::TYPE_EQUAL);
        $passwordFilter->setValue($password);
        $usersList = $userController->getService()->loadAll(array($emailFilter, $passwordFilter), null, null);

        /* @var $user PersistUser */
        foreach($usersList as $user) {
            if($user->getPassword() === $password) {
                $out['user'] = $user;
                $out['success'] = true;
                break;
            }
        }
        if($out['user'] == null) {
            $out['errors'][] = array('name'=>'email', 'code' => UI_Validation::CUSTOM);
            $out['success'] = false;
        }
        return $out;
    }
}