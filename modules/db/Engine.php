<?php
class DB_Engine implements DB_IEngine{

    public function connect()
    {
        throw new Exception("Method `".__FUNCTION__. "` was not implemented in this DB source. Sorry...");
    }

    public function disconnect()
    {
        throw new Exception("Method `".__FUNCTION__. "` was not implemented in this DB source. Sorry...");
    }

    /**
     * Return result set on 'select' statement
     * @param string $query
     * @return result set
     */
    public function getResultSet($query)
    {
        throw new Exception("Method `".__FUNCTION__. "` was not implemented in this DB source. Sorry...");
    }

    /**
     * Return last inserted id
     * @return mixed
     */
    public function getLastId(){
        throw new Exception("Method `".__FUNCTION__. "` was not implemented in this DB source. Sorry...");
    }


    /**
     * Execute query for 'insert', 'update' and 'delete' statements
     * @param string $query
     * @return boolean
     */
    public function query($query)
    {
        throw new Exception("Method `".__FUNCTION__. "` was not implemented in this DB source. Sorry...");
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns($table) {
        throw new Exception("Method `".__FUNCTION__. "` was not implemented in this DB source. Sorry...");
    }


    public function setEncoding($encoding){
        $query = 'SET NAMES '.$encoding;
        $this->query ( $query );
    }

    public function getTable($query, $key = "") {
        $result = $this->getResultSet( $query );
        $answer = array ();

        if (! empty ( $result ) && $result !== - 1) {
            while ( $row = $result->fetch_assoc () ) {
                if ($key === "")
                    $answer [] = $row;
                else
                    $answer [$row [$key]] = $row;
            }
        }
        return $answer;
    }

    public function getAssoc($query, $key, $value) {
        $result = $this->getResultSet( $query );
        $answer = array ();

        if (! empty ( $result ) && $result !== - 1) {
            while ( $row = $result->fetch_assoc () ) {
                $answer [$row [$key]] = $row [$value];
            }
        }
        return $answer;
    }


    public function getRow($query) {
        $result = $this->getResultSet( $query );
        $out = array ();
        if (! empty ( $result ) && $result !== - 1) {
            while ( $row = $result->fetch_assoc () ) {
                $out = $row;
                break;
            }
        }
        return $out;
    }

    public function getColumn($query) {
        $result = $this->getResultSet ( $query );
        $out = array ();
        if (! empty ( $result ) && $result !== - 1) {
            while ( $row = $result->fetch_array () ) {
                $out [] = $row [0];
            }
        }
        return $out;
    }

    public function getCell($query) {
        $result = $this->getResultSet( $query );
        if (! empty ( $result ) && $result !== - 1) {
            $row = $result->fetch_assoc ();
            if (! empty ( $row )) {
                foreach ( $row as $out ) {
                    return $out;
                }
            }
        }
        return "";
    }

    public function escape($value){
        return addslashes($value);
    }


    public function close(){
        throw new Exception("function can't be implemented with no defined engine.");
    }
}