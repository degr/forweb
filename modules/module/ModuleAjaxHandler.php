<?php

/**
 * Class AjaxHandler
 * contain information about ajax handler
 * Created for secure interest,
 * to prevent any controller's method invocation from ajax
 */
class ModuleAjaxHandler {

    const JSON = "json";
    const TEXT = "text";

    public function __construct($method, $response){
        $this->method = $method;
        if($response != self::JSON) {
            if($response != self::TEXT){
                throw new Exception("Unknown response type: ".$response." in ajax handler with method: ".$method);
            }
        }
        $this->response = $response;
    }

    /**
     * @var string
     */
    protected $method;
    /**
     * @var string
     */
    protected $response;

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }


    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}