<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 03.05.2015
 * Time: 0:02
 */
class ModuleEventHandler {
    protected $method;
    protected $event;
    public function __construct($event, $method){
        $this->method = $method;
        $this->event = $event;
    }

    public function getEvent(){
        return $this->event;
    }

    public function getMethod(){
        return $this->method;
    }
}