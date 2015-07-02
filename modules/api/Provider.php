<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/2/2015
 * Time: 1:00 PM
 */

/**
 * Class Api_Provider
 *
 */
class Api_Provider implements  Api_IPrivoder{

    private $table;
    public function __construct($table){
        $this->table = $table;
    }

    public function getAllowedFields()
    {
        return Api::OPEN_API ? array("*") : array();
    }

    public function getDeniedFields()
    {
        return Api::OPEN_API ? array() : array("*");
    }


    public function getCell($cell, $filter)
    {
        if (empty($filter)) {
            throw new FwException("Can't return cell, because filter is undefined.");
        }
        $result = $this->getColumn($cell, $filter);
        return count($result) > 0 ? reset($result) : null;
    }

    public function getRow($filter)
    {
        if (empty($filter)) {
            throw new FwException("Can't return cell, because filter is undefined.");
        }
        $result = $this->getList($filter);
        return count($result) > 0 ? reset($result) : null;
    }

    public function getColumn($field, $filter)
    {
        if(!$this->isAllowedField($field)) {
            throw new FwException("This field is forbidden, or not exist.");
        }
        return DB::getColumn("SELECT " . $field . " FROM " . $this->table . " WHERE " . $filter);
    }

    public function getAssoc($key, $value, $filter)
    {
        if(!$this->isAllowedField($key) || !$this->isAllowedField($value)) {
            throw new FwException("This fields is forbidden, or not exist.");
        }
        return DB::getAssoc("SELECT " . $key . ", " . $value . " FROM " . $this->table . (!empty($filter) ? " WHERE " . $filter : ""), $key, $value);
    }

    public function getList($filter)
    {
        $allowed = $this->getAllowedFields();
        $denied = $this->getDeniedFields();
        if(count($allowed) == 0 || in_array("*", $denied)) {
            return null;
        }
        $table = DB::getTable("SELECT ".implode(", ", $allowed)." FROM " . $this->table . (!empty($filter) ? " WHERE " . $filter : ""));
        if(count($denied) > 0) {
            foreach($table as &$row) {
                foreach($denied as $deniedField) {
                    if(isset($row[$deniedField])) {
                        unset($row[$deniedField]);
                    }
                }
            }
        }
        return $table;
    }

    private function isAllowedField($field){
        $allowed = $this->getAllowedFields();
        $denied = $this->getDeniedFields();
        if(in_array("*", $allowed) || in_array($field, $allowed)) {
            return !in_array("*", $denied) && !in_array($field, $denied);
        } else {
            return false;
        }
    }
}