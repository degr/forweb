<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 23.03.2015
 * Time: 20:57
 */
class User_Gui_Forms{
    public function getAuthorizationForm(UI $ui, $errors){
        $ui->setLayout("user/forms/authorization.tpl");
        $textErrors = array(
            'email' => array(),
            'password' => array()
        );
        foreach($errors as $error) {
            $textErrors[$errors['name']][] = Word::get('user', 'auth_error_'.$error['code']);
        }
        $ui->addVariable('errors', $textErrors);
    }

    public function getLogOutForm(UI $ui)
    {
        $ui->setLayout("user/forms/logout.tpl");
    }

}