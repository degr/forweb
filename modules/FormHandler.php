<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 24.03.2015
 * Time: 12:05
 */
class FormHandler{
    /**
     * Form handler method name.
     * Can't be changed in runtime.
     * @var string
     */
    protected $method;
    /**
     * Url to page, that will be opened after form post handling
     * can be changed in runtime.
     * @var string
     */
    protected $url;
    /**
     * Form errors. Use with UI::getFormForTable();
     * After sending form to specified url, if form contain errors,
     * this errors will be stored into session, and than system will invoke
     * redirect to $_SERVER['HTTP_REFERER'] or $this->url, if it specified.
     * @see CMS::processForm
     * @see UI::getFormForTable
     * @see UI::setFormErrors
     * @see UI::clearErrors
     * @see Core::process
     * [
     *    name:[1,2],
     *    pass:[2,5]
     * ]
     * @var array
     */
    protected $errors;
    public function __construct($method){
        $this->method = $method;
        $this->errors = array();
    }

    public function getMethod(){
        return $this->method;
    }
    public function getUrl(){
        return $this->url;
    }

    public function setUrl($url){
        $this->url = $url;
    }

    public function getErrors(){
        return $this->errors;
    }

    public function addError($fieldName, $error){
        $this->errors[$fieldName][] = $error;
    }
}