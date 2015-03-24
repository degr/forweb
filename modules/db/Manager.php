<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 25.12.2014
 * Time: 14:04
 */

class DB_Manager {
    const BASE_FOLDER = "Persistent";
    const DB_FOLDER = "DB";
    const ENGINE_FOLDER = "Engine";
    const ORM_FOLDER = "ORM";

    protected $basePath;
    protected $engine;

    private $host;
    private $user;
    private $password;
    private $database;

    public function __construct($basePath, $engine){
        $this->basePath = $basePath;
        $pass = true;
        switch($engine){
            case "MySQL":
                break;
            default:
                $pass = false;
        }
        if(!$pass){
            throw new FwException("Not supported database engine: ".$engine);
        }
        $this->engine = "DB_Engine_".$engine;
    }

    public function getEngine(){
        return $this->engine;
    }
    public function getBasePath(){
        return $this->basePath;
    }

    public function setCredentials($host, $user, $password, $database){
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
    }

    /**
     * @param $instance
     */
    public function getInstance(){
        return new $this->engine($this->host, $this->user, $this->password, $this->database);
    }
}