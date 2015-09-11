<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 04.09.2015
 * Time: 21:55
 */
abstract class ModuleInputTable implements ModuleInput
{
    protected $tableName;
    protected $fieldName;
    protected $whereCondition;
    public function __construct($tableName, $fieldName, $whereCondition){
        $this->tableName = $tableName;
        $this->fieldName = $tableName;
        $this->whereCondition = $whereCondition;
    }

    /**
     * Process user response
     * @return boolean
     */
    public function process($userInput)
    {
        if(empty($userInput['field'])) {
            throw new FwException("Field must be populated");
        }
        $query = "select * from ".$this->tableName." where ".$this->whereCondition;
        $res = DB::getTable($query);
        if(empty($res)) {
            $query = "insert into " . $this->tableName . " (" . $this->fieldName . ") values ('"
                . DB::escape($userInput['field']) . "')";
        } else {
            $query = "update ".$this->tableName." set ".$this->fieldName." = '".DB::escape($userInput['field'])
                . "' where ".$this->whereCondition;
        }
        DB::query($query);
        return true;
    }
}