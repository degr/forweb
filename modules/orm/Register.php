<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 04.04.2015
 * Time: 17:39
 */
class ORM_Register{
    /**
     * @param $tableName string
     * @return ORM_Table
     * @throws Exception in case, when table can't be founded
     */
    public static function registerTable($tableName, &$registeredTables){
        $folder = ORM::getTablesFolder();
        $data = file_get_contents($folder.$tableName.".data");
        if(empty($data)){
            throw new Exception("Can't found table with name: " .$tableName);
        }
        $table = unserialize($data);
        $registeredTables[$tableName] = $table;
        $persist = $table->getPersistClassName();
        require_once ORM::getPersistObjectsFolder().$persist.".php";
        return $table;
    }

    /**
     * Register table in runtime.
     * Required on site initialization
     * @param ORM_Table $table
     * @throws FwException
     */
    public static function registerTableOnFly (ORM_Table $table, &$registeredTables)
    {
        if (empty($registeredTables[$table->getName()])) {
            $registeredTables[$table->getName()] = $table;
        } else {
            throw new FwException("Can't register table ".$table->getName()." on the fly, because it already exist.");
        }
    }
    public static function createTable (ORM_Table $table){
        ORM_Install::createDBTable($table);
        ORM_Install::serializeTable($table);
        ORM_Install::createPersistanceClass($table);
    }

    /**
     * Get table object using it's name
     * @param $tableName
     * @return ORM_Table
     */
    public static function getTable($tableName, &$registeredTables){
        if(gettype($tableName) == 'object'){
            echo '<pre>';print_r(debug_backtrace(1)).'</pre>';
        }
        $tableName = strtolower($tableName);
        if(empty($registeredTables[$tableName])){
            ORM::registerTablesChain($tableName);
        }
        return $registeredTables[$tableName];
    }

    public static function registerTablesChain($tableName, &$registeredTables){
        if(empty($registeredTables[$tableName])){
            ORM::registerTable($tableName);
        }
        /* @var $bind ORM_Table_Bind */
        foreach($registeredTables[$tableName]->getBinds() as $bind){
            if(!$bind->getLazyLoad()){
                $table = $bind->getRightTable();
                ORM::registerTable($table->getName());
            }
        }
    }
}