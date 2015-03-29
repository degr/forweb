<?php

/**
 * Interface IDB
 * for main database manipulations
 * Provide functions scope for static class 'DB'
 */
interface  DB_IDB{

    /**
     * Execute query
     * Main DB function
     * @param string $query
     * @return [result set, boolean, affected rows count]
     */
    public static function query($query);

    /**
     * Insert data in table (data = assoc array('field'=>'value')
     * (only one row)
     * @param string $table
     * @param array $data
     */
    public static function insert($table, $data);
    /**
     * Insert multy rows in one query
     * @param string $table
     * @param array $fields (one leveled)
     * @param array $values (two leveled)
     */
    public static function insertMulty($table, $fields, $values);

    /**
     * return affected rows of last query
     * @return int
     */
    public static function getAffectedRows();
    /**
     * Get query count of current script execution time
     * return integer
     */
    public static function getQueriesCount();

    /**
     * Get columns of selected table
     * @param String $table
     * @return array
     */
    public static function getColumns($table);

    /**
     * Get two-leveled data array.
     * If key defined, it will be assoc array,
     * using selected field as keys
     * DB::getTable('select * from table');
     * [
     *  [id: 1, name: 'aaa'],
     *  [id: 4, name: 'bbb'],
     * ]
     *   OR
     *  DB::getTable('select * from table', 'id')
     * {
     *  '1': [id: 1, name: 'aaa'],
     *  '4': [id: 4, name: 'bbb'],
     * }
     * @param string $query
     * @param string $key
     * @return array
     */
    public static function getTable($query, $key = "");

    /**
     * Return one-leveled assoc array in format key=>value
     * DB::getAssoc('select * from config', 'key', 'value');
     * {
     *   url: 'http://site.ru/',
     *   maxItemsOnPage: '20',
     *   defaultLanguage: 'ru'
     * }
     * @param string $query
     * @param string $key
     * @param string $value
     * @return array(key=>value)
     */
    public static function getAssoc($query, $key, $value);

    /**
     * Return first row of result set (one-leveled assoc array)
     * @param string $query
     * @return array(key=>value)
     */
    public static function getRow($query);

    /**
     * return first column of result set
     * @param string $query
     * @return array(value)
     */
    public static function getColumn($query);
    /**
     * Return first cell of first column.
     * @param string $query
     * @return string
     */
    public static function getCell($query);

    /**
     *	Return last inserted autoincrement id
     *  @return Integer|String
     */
    public static function getLastInsertedId();

    /**
     * Escape input string for query
     * @param string $value
     * @return string mixed
     */
    public static function escape($value);
}