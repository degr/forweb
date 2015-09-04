<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 22.03.2015
 * Time: 10:47
 */
class AccessAdminGroup{
    public function deleteAccessGroup(){
        $group = $_POST['group'];
        /* @var $access Access */
        $access = Core::getModule("Access");
        $groups = $access->getAccessGroups();
        if(!in_array($group, $groups)){
            return '0';
        }
        $query = 'ALTER TABLE access DROP COLUMN '.$group;
        DB::query($query);
        Access::rewriteAccessFile();
        return 1;
    }

    public function createAccessGroup()
    {
        $group = $_POST['group'];
        /* @var $access Access */
        $access = Core::getModule("Access");
        $groups = $access->getAccessGroups();
        if(in_array($group, $groups)){
            return '0';
        }
        $query = 'ALTER TABLE access ADD COLUMN '.$group.'  TINYINT(1) DEFAULT 0';
        DB::query($query);
        Access::rewriteAccessFile();
        return 1;
    }
}