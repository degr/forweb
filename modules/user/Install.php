<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 21.03.2015
 * Time: 16:00
 */
class User_Install implements Module_IInstall{

    /**
     * Make all necessary preparations for this module
     * as PersistTables deploy, cache files creating etc..
     * @return void
     */
    public function install()
    {
        $userTable = new ORM_Table('user');

        $id = new ORM_Table_Field('id', 'integer');
        $id->setAutoIncrement(true)->setPrimary(true);
        $userTable->addField($id);

        $name = new ORM_Table_Field('name', 'varchar');
        $name->setLength(50);
        $userTable->addField($name);

        $password = new ORM_Table_Field('password', 'varchar');
        $password->setLength(50);
        $userTable->addField($password);

        $email = new ORM_Table_Field('email', 'varchar');
        $email->setLength(255);
        $userTable->addField($email);

        $accessGroup = new ORM_Table_Field('access', 'varchar');
        $accessGroup->setLength(50);
        $userTable->addField($accessGroup);

        ORM::createTable($userTable);

        $ids = DB::getColumn("SELECT id FROM user");
        if(empty($ids)){
            $query = "INSERT INTO user (id, name, password, email, access)".
                "VALUES (1, 'admin', 'admin', 'admin@admin.admin', 'administrator')";
            DB::query($query);
        }

    }

    /**
     * Return information about this module
     * @return string
     */
    public function getInfo()
    {
        ob_start();
        echo '<h3>ForWeb framework User package</h3>';
        echo '<p>Contain functions for authorisation and some user actions. Contain DB "user" table.</p>';
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    /**
     * get module dependencies for deploy
     * @return Module_Dependency[]
     */
    public function getDependencies()
    {
        return array(new Module_Dependency('access'));
    }
}