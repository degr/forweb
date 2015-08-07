<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 21.03.2015
 * Time: 16:13
 */

class Access_Install implements Module_IInstall {

    /**
     * Make all necessary preparations for this module
     * as PersistTables deploy, cache files creating etc..
     * @return void
     */
    public function install()
    {
        $table = new ORM_Table("access");

        $id = new ORM_Table_Field("id", "integer");
        $id->setAutoIncrement(true)->setPrimary(true);
        $table->addField($id);

        $action = new ORM_Table_Field("action", "varchar");
        $action->setLength(100);
        $table->addField($action);

        $anonymous = new ORM_Table_Field(Access::GROUP_ANONIMOUS, "tinyint");
        $anonymous->setLength(1);
        $anonymous->setDefaultValue(0);
        $table->addField($anonymous);

        $authorized = new ORM_Table_Field(Access::GROUP_AUTHORIZED, "tinyint");
        $authorized->setLength(1);
        $authorized->setDefaultValue(0);
        $table->addField($authorized);

        $administrator = new ORM_Table_Field(Access::GROUP_ADMINISTRATOR, "tinyint");
        $administrator->setLength(1);
        $administrator->setDefaultValue(0);
        $table->addField($administrator);

        ORM_Install::createDBTable($table);

        $query = "select id from access";
        $column = DB::getColumn($query);
        if(empty($column)) {
            $query = "show columns from access";
            $columns = DB::getColumn($query);
            $condition1 = false;
            $condition2 = false;
            $condition3 = false;
            $condition4 = true;

            if(count($columns) > 2) {
                for($i=2;$i<count($columns); $i++) {
                    $col = $columns[$i];
                    echo $col;
                    if($col == Access::GROUP_ANONIMOUS){
                        $condition1 = true;
                        continue;
                    }
                    if($col == Access::GROUP_AUTHORIZED){
                        $condition2 = true;
                        continue;
                    }
                    if($col == Access::GROUP_ADMINISTRATOR) {
                        $condition3 = true;
                        continue;
                    }
                    $condition4 = false;
                    break;
                }
            }
            if($condition1 && $condition2 && $condition3 && $condition4) {
                $query = "INSERT INTO access (id, action, " . Access::GROUP_ANONIMOUS . ", " . Access::GROUP_AUTHORIZED . ", " . Access::GROUP_ADMINISTRATOR . ")"
                    . "VALUES"
                    . "(1, 'can_edit_pages', 0, 0, 1),"
                    . "(2, 'can_edit_templates', 0, 0, 1),"
                    . "(3, 'can_see_admin_panel', 0, 0, 1),"

                    // users
                    . "(4, 'can_edit_users', 0, 0, 1),"

                    //access
                    . "(5, 'can_edit_user_groups', 0, 0, 1),"
                    . "(6, 'can_edit_access_actions', 0, 0, 1),"

                    . "(7, 'can_edit_config', 0, 0, 1),"
                    . "(8, 'can_edit_languages', 0, 0, 1),"
                    . "(9, 'can_edit_terms', 0, 0, 1)";

                DB::query($query);
            } else {
                echo '<h1 style="color: red">CAN\'T INSERT DEFAULT DATA TO ACCESS TABLE. Default access groups are not equal to current access groups.</h1>';
            }
        }


    }

    /**
     * Return information about this module
     * @return string
     */
    public function getInfo()
    {
        ob_start();
        echo '<h3>ForWeb framework Access package</h3>';
        echo "<p>Contain functions for user access. Main module terms - 'user group' and 'action'."
            ." Each user can belong to only one user group, by default system contain three groups:</p>";
        echo "<ul>";
        echo "<li>anonymous</li>";
        echo "<li>authorized</li>";
        echo "<li>administrator</li>";
        echo "</ul>";
        echo "<p>You can add any other user group, or delete this groups. All groups have equals 'actions', but"
            ." different permissions. This module have code-generic functionality, and you must know what are you doing,"
            ." when delete any permissions. Core package does not use generic-code, but any custom module can use some"
            ." of them. So, if module contain use generic-access function, you will get an exception.</p>";
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
        return null;
    }
}