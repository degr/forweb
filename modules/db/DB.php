<?php

/**
 * Class DB
 * base class, that implement main database interactions methods
 */
class DB implements DB_IDB{

    /**
     * @var DB_IEngine $instance
     */
    protected static $instance;
    /**
     * @var integer count of executed queries
     */
    protected static $queriesCount;


    private static $errorMessage = '<h3 class = "red">Data base connection error!</h3>';

    public static function init(DB_Manager $manager) {
        DB::$instance = $manager->getInstance();
        DB::$queriesCount = 0;
    }

    public static function setEncoding($encoding){
        DB::$instance->setEncoding($encoding);
    }

    /**
     * @param string $table
     * @param array $data
     * @return mixed
     */
    public static function insert($table, $data) {
        $fields = array();
        $values = array();
        foreach ($data as $k=>$v){
            $fields[] = DB::escape($k);
            $values[] = DB::escape($v);
        }
        $query = "INSERT INTO `".$table."` (`".implode("`, `", $fields)."`) values ('".implode("', '", $values)."')";
        return DB::query($query);
    }

    /**
     * @param string $table
     * @param array $fields
     * @param array $values
     * @return mixed
     */
    public static function insertMulty($table, $fields, $values){
        $valuesParam = array();
        foreach ($fields as &$v){
            $v = DB::escape($v);
        }
        unset($v);

        foreach ($values as $value){
            foreach ($value as &$f){
                $f = DB::escape($f);
            }
            $valuesParam[] = "('".implode("', '", $value)."')";
        }
        $query = "INSERT INTO `".$table."` (`".implode("`, `", $fields)."`) values ".implode(", ", $valuesParam);
        return DB::query($query);
    }


    /**
     * @return mixed
     * @throws FwException
     */
    public static function getAffectedRows(){
        if(empty(DB::$instance)){
            throw new FwException("DB not initted");
        }
        return DB::$instance->affected_rows;
    }

    /**
     * @return int
     */
    public static function getQueriesCount() {
        return DB::$queriesCount;
    }



    public static function getColumns($table) {
        return DB::getInstance()->getColumns($table);
    }

    /**
     * @param string $query
     * @param string $key
     * @return mixed
     */
    public static function getTable($query, $key = "") {
        return DB::getInstance()->getTable($query, $key);
    }

    /**
     * @param string $query
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public static function getAssoc($query, $key, $value) {
        return DB::getInstance()->getAssoc($query, $key, $value);
    }

    /**
     * @param string $query
     * @return mixed
     */
    public static function getRow($query) {
        return DB::getInstance()->getRow($query);
    }

    /**
     * @param string $query
     * @return mixed
     */
    public static function getColumn($query) {
        return DB::getInstance()->getColumn($query);
    }

    /**
     * @param string $query
     * @return mixed
     */
    public static function getCell($query) {
        return DB::getInstance()->getCell($query);
    }

    /**
     * @param string $query
     * @return mixed
     */
    public static function query($query) {
        return DB::getInstance()->query($query);
    }

    /**
     * @return mixed
     */
    public static function getLastInsertedId(){
        return DB::$instance->insert_id;
    }

    /**
     * @return DB_IEngine
     */
    public static function getInstance(){
        return DB::$instance;
    }

    /**
     * @param string $value
     * @return mixed
     */
    public static function escape($value){
        return DB::getInstance()->escape($value);
    }

    /**
     * Close this connection
     */
    public static function close(){
        DB::getInstance()->close();
    }

    /**
     *
     */
    public static function incrementQueries(){
        DB::$queriesCount++;
    }
}