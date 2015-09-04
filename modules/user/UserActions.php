<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 24.03.2015
 * Time: 20:03
 */
class UserActions{
    public function authorization($email, $password){
        $out = array(
            'success' => null,
            'errors' => array(),
            'user' => null
        );
        if(empty($email)) {
            $out['errors'][] = array('name'=>'email', 'code' => ValidationCode::REQUIRED);
        }
        if(empty($password)) {
            $out['errors'][] = array('name'=>'password', 'code' => ValidationCode::REQUIRED);
        }

        $userController = Core::getModule("User");

        $emailFilter = new OrmQueryFilter('user', 'email', OrmQueryFilter::TYPE_EQUAL);
        $emailFilter->setValue($email);
        $passwordFilter =  new OrmQueryFilter('password', 'email', OrmQueryFilter::TYPE_EQUAL);
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
            $out['errors'][] = array('name'=>'email', 'code' => ValidationCode::CUSTOM);
            $out['success'] = false;
        }
        return $out;
    }
}