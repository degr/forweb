<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 23.03.2015
 * Time: 20:57
 */
class UserGuiForms{
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
        if(empty($_GET)) {
            $url = $_SERVER['REQUEST_URI']."?logout=1";
        } else {
            $isLogout = false;
            $query = $_GET;
            $query['logout'] = 1;
            foreach($query as $key => $value) {
                $query[$key] = $key . '=' . $value;
            }
            $url = $_SERVER['REQUEST_URI']."?".implode("&", $query);
        }
        $ui->addVariable('url', $url);
        $ui->setLayout("user/forms/logout.tpl");
    }

}