<?php
interface DB_IEngine{
    /**
     * connect to target datasource
     * @return boolean
     */
    public function connect();

    /**
     * disconnect from target datasource
     * @return mixed
     */
    public function disconnect();

    /**
     * Return result set on 'select' statement
     * @param string $query
     * @return object result set
     */
    public function getResultSet($query);

    /**
     * Execute query for 'insert', 'update' and 'delete' statements
     * @param string $query
     * @return boolean
     */
    public function query($query);

    /**
     * Return last inserted id
     * @return mixed
     */
    public function getLastId();

    /**
     * {@inheritdoc}
     */
    public function getColumns($table);

    public function setEncoding($encoding);

    public function getTable($query, $key = "");

    public function getAssoc($query, $key, $value);

    public function getRow($query);

    public function getColumn($query);

    public function getCell($query);
}