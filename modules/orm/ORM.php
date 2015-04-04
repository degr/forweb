<?php

/**
 * Class ORM
 * Main class for work with objective datasource model.
 */
class ORM{
    const EXTEND = "Ext";
    /**
     * links collection to tables
     * ORM_Table[]
     */
    protected static $registeredTables;

    /**
     * get folder, where serialized tables stored
     * @return string
     */
    public static function getTablesFolder(){
        return "Cache/ORM/Tables/";
    }

    public static function getPersistObjectsFolder(){
        return "ORM/Persistance/";
    }

    public static function getPersistExtendedObjectsFolder(){
        return ORM::getPersistObjectsFolder()."extend/";
    }

    /**
     * Load binded data for object. Use on lazy load
     * @param $tableName string
     * @param $keyValue
     * @param $leftKey
     * @param $rightKey
     * @param $type
     * @return ORM_Persistence_Base|ORM_Persistence_Base[]
     */
    public static function loadBinded($tableName, $keyValue, $leftKey, $rightKey, $type) {
        $mainTable = ORM::getTable($tableName);
        $filter = new ORM_Query_Filter($mainTable->getName(), $rightKey, ORM_Query_Filter::TYPE_EQUAL);
        $one = $type == ORM_Table::ONE_TO_ONE || $type == ORM_Table::MANY_TO_ONE;
        return ORM_Utils::load($mainTable, $one, array($filter), null, null);
    }


    /**
     * Load data from persistence storage
     * @param string $tableName - name of main table for object
     * @param string $tail - 'where', 'order', etc
     * @param boolean $binds - inlude all binded data
     * @param boolean $one - expected one object or null
     * @return mixed
     */
    public static function load($tableName, $one, $filters, $sorters, $pager) {
        $mainTable = ORM::getTable($tableName);
        return ORM_Utils::load($mainTable, $one, $filters, $sorters, $pager);
    }

    /**
     * Save one multy leveled data array. Structure element - ORM_Persistence_Base class object
     *
     * @param ORM_Table $mainTable
     * @param $object
     */
    public static function saveArray (ORM_Table $mainTable, $object) {
        ORM_Utils::saveArray($mainTable, $object);
    }

    /**
     * Save or update ORM_Persistence_Base object
     */
    public static function saveData (ORM_Table $table, ORM_Persistence_Base $object) {
        ORM_Utils::saveData($table, $object);
    }

    /**
     * Delete object from data base
     */
    public static function delete(ORM_Table $table, ORM_Persistence_Base $object) {
        return ORM_Utils::delete($table, $object);
    }

    /**
     * Build object from json data
     * @param $table ORM_Table
     * @param $data array[id=1, name='Serg', birthdate='1986-02-21']
     * @return array[0=>ORM_Persistence_Base, 1=>errors[]]
     */
    public static function buildObject($table, $data) {
        return ORM_Utils::buildObject($table, $data);
    }

    public static function createTable (ORM_Table $table) {
        ORM_Register::createTable($table);
    }

    /**
     * Get table object using it's name
     * @param $tableName
     * @return ORM_Table
     */
    public static function getTable($tableName) {
        return ORM_Register::getTable($tableName, ORM::$registeredTables);
    }

    public static function registerTablesChain($tableName) {
        ORM_Register::registerTablesChain($tableName, ORM::$registeredTables);
    }

    /**
     * @param $tableName string
     * @return ORM_Table
     * @throws Exception in case, when table can't be founded
     */
    public static function registerTable($tableName) {
        return ORM_Register::registerTable($tableName, ORM::$registeredTables);
    }

    /**
     * Register table in runtime.
     * Required on site initialization
     * @param ORM_Table $table
     * @throws FwException
     */
    public static function registerTableOnFly (ORM_Table $table) {
        ORM_Register::registerTableOnFly($table, ORM::$registeredTables);
    }
}