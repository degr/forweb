<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 21.03.2015
 * Time: 8:40
 */

class FwException extends Exception{

    // custom string representation of object
    final public function __toString() {
        echo "<pre>";
        $this->message = "\n".$this->message;
        return parent::__toString();
    }
}