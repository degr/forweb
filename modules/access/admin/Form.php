<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 21.03.2015
 * Time: 18:42
 */
class Access_Admin_Form{

    public function getForm(){
        $table = DB::getTable("select * from access");
        /* @var $access Access */
        $access = Core::getModule("access");
        $accessGroups = $access->getAccessGroups();
        array_unshift($accessGroups, 'action');
        $accessGroups = array_combine($accessGroups, $accessGroups);
        $out = UI::getOverviewTable($table, $accessGroups, array('id'));
        return $out;
    }

    public function switchAccess()
    {
        $id = intval($_POST['id']);
        if(empty($id)) {
            return '0';
        }
        $action = DB::escape($_POST['action']);
        if(empty($action)) {
            return '0';
        }
        $group = DB::escape($_POST['group']);
        if(empty($group)) {
            return '0';
        }
        /* @var $access Access */
        $access = Core::getModule("Access");
        if(!in_array($group, $access->getAccessGroups())) {
            return '0';
        }
        $can = Access::can($action, $group);
        $query = "UPDATE access SET ".$group."='".($can ? '0' : '1')."' WHERE id='".$id."'";
        DB::query($query);
        return '1';
     }

    public function editActionName()
    {
        $id = intval($_POST['id']);
        if(empty($id)) {
            return '0';
        }
        $action = DB::escape($_POST['action']);
        if(empty($action)) {
            return '0';
        }
        $query = "UPDATE access SET action='".$action."' WHERE id='".$id."'";
        DB::query($query);
        return '1';
    }

    public function deleteAction()
    {
        $id = intval($_POST['id']);
        if(empty($id)) {
            return '0';
        }
        $query = "DELETE FROM access WHERE id = ".$id;
        DB::query($query);
        Access::rewriteAccessFile();
        return 1;
    }

    public function createAccessAction()
    {
        $action = $_POST['action'];
        if(empty($action)) {
            return '0';
        }
        $query = "SELECT id FROM access WHERE action = '".DB::escape($action)."'";
        $id = DB::getCell($query);
        if(!empty($id)) {
            return '0';
        }
        $query = "INSERT INTO access(action) VALUES('".DB::escape($action)."')";
        DB::query($query);
        Access::rewriteAccessFile();
        return 1;
    }
}