<?php

/**
 * Class ORM
 * Main class for work with objective datasource model.
 */
class ORM{
    const EXTEND = "Ext";
    /**
     * links collection to tables
     * ORM_Objects_Table[]
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

    public static function loadBinded($tableName, $keyValue, $leftKey, $rightKey, $type){
        $mainTable = ORM::getTable($tableName);
        $tail = ORM_QueryBuilder::buildQueryForBind($mainTable, $keyValue, $leftKey);
        if($type == ORM_Objects_Table::ONE_TO_ONE || $type == ORM_Objects_Table::MANY_TO_ONE){
            $one = true;
        } else {
            $one = false;
        }
        return ORM_Utils::load($mainTable, $tail, false, $one);
    }


    /**
     * Load data from persistence storage
     * @param string $tableName - name of main table for object
     * @param string $tail - 'where', 'order', etc
     * @param boolean $binds - inlude all binded data
     * @param boolean $one - expected one object or null
     * @return mixed
     */
    public static function load($tableName, $tail, $binds, $one){

        $mainTable = ORM::getTable($tableName);
        return ORM_Utils::load($mainTable, $tail, $binds, $one);

    }

    /**
     * Save one multy leveled data array. Structure element - ORM_Objects_PersistBase class object
     *
     * @param ORM_Objects_Table $mainTable
     * @param $object
     */
    public static function saveArray (ORM_Objects_Table $mainTable, $object){
        ORM_Utils::saveArray($mainTable, $object);
    }

    /**
     * Save or update ORM_Objects_PersistBase object
     */
    public static function saveData (ORM_Objects_Table $table, ORM_Objects_PersistBase $object){
        ORM_Utils::saveData($table, $object);
    }

    /**
     * Delete object from data base
     */
    public static function delete(ORM_Objects_Table $table, ORM_Objects_PersistBase $object){
        return ORM_Utils::delete($table, $object);
    }

    /**
     * Build object from json data
     * @param $table ORM_Objects_Table
     * @param $data array[id=1, name='Serg', birthdate='1986-02-21']
     * @return array[0=>ORM_Objects_PersistBase, 1=>errors[]]
     */
    public static function buildObject($table, $data)
    {
        $objectClass = $table->getPersistClassName();
        $object = new $objectClass;
        $errors = array();

        foreach($table->getFields() as $field) {
            $name = $field->getName();
            if (!isset($data[$name])) {
                $data[$name] = null;
            }
            if ($field->validateValue($data[$name], $errors)) {
                $setter = "set" . ucfirst($name);
                $object->$setter($data[$name]);
            }
        }
        return array($object, $errors);
    }

    public static function createTable (ORM_Objects_Table $table){
        ORM_Install::createDBTable($table);
        ORM_Install::serializeTable($table);
        ORM_Install::createPersistanceClass($table);
    }

    /**
     * Get table object using it's name
     * @param $tableName
     * @return ORM_Objects_Table
     */
    public static function getTable($tableName){

        if(gettype($tableName) == 'object'){
            echo '<pre>';print_r(debug_backtrace(1)).'</pre>';
        }
        $tableName = strtolower($tableName);
        if(empty(ORM::$registeredTables[$tableName])){
            ORM::registerTablesChain($tableName);
        }
        return ORM::$registeredTables[$tableName];
    }

    public static function registerTablesChain($tableName){
        if(empty(ORM::$registeredTables[$tableName])){
            ORM::registerTable($tableName);
        }
        /* @var $bind ORM_Objects_Bind */
        foreach(ORM::$registeredTables[$tableName]->getBinds() as $bind){
            if(!$bind->getLazyLoad()){
                $table = $bind->getRightTable();
                ORM::registerTable($table->getName());
            }
        }
    }

    /**
     * @param $tableName string
     * @return ORM_Objects_Table
     * @throws Exception in case, when table can't be founded
     */
    public static function registerTable($tableName){
        $folder = ORM::getTablesFolder();
        $data = file_get_contents($folder.$tableName.".data");
        if(empty($data)){
            throw new Exception("Can't found table with name: " .$tableName);
        }
        $table = unserialize($data);
        ORM::$registeredTables[$tableName] = $table;
        $persist = $table->getPersistClassName();
        require_once ORM::getPersistObjectsFolder().$persist.".php";
        return $table;
    }

    /**
     * Register table in runtime.
     * Required on site initialization
     * @param ORM_Objects_Table $table
     * @throws FwException
     */
    public static function registerTableOnFly (ORM_Objects_Table $table)
    {
        if (empty(ORM::$registeredTables[$table->getName()])) {
            ORM::$registeredTables[$table->getName()] = $table;
        } else {
            throw new FwException("Can't register table ".$table->getName()." on the fly, because it already exist.");
        }
    }
}