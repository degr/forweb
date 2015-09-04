<?php

/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/10/2015
 * Time: 2:50 PM
 */
class ValidationRuleUnique implements ValidationRule
{
    private $table;
    private $column;
    private $key;


    public function __construct($table, $column, $key)
    {
        if(empty($table) || empty($column)) {
            throw new FwException("Malformed unique validation rule. Table and column is required.");
        }
        $this->table = $table;
        $this->column = $column;
        $this->key = $key;
    }

    public function validate($data, $key = 0)
    {
        $query = "SELECT count(1) FROM ".$this->table." WHERE ".$this->column." = '".DB::escape($data)."'";
        if(!empty($this->key)) {
            $query .= " AND ".$this->key." <> '".DB::escape($key)."'";
        }
        $count = DB::getCell($query);
        return intval($count) === 0;
    }
}