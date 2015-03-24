<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 23.03.2015
 * Time: 20:57
 */
class User_Gui_Forms{
    public function getAuthorizationForm(UI $ui){
        $ui->setLayout("user/forms/authorization.tpl");
    }

}