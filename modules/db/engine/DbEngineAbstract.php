<?php
abstract class DbEngineAbstract implements DbEngine{


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