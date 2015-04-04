<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 04.04.2015
 * Time: 10:41
 */
abstract class ORM_Query_AbstractItem implements ORM_Query_IItem{
    const REQUEST_REQUEST = 'request';
    const REQUEST_GET = 'get';
    const REQUEST_POST = 'post';
    const REQUEST_PUT = 'put';
    /**
     * @var string $autoActivateRequestMethod if filter can be auto-activated, define request method ('request', 'get', 'post', 'put')
     */
    protected $autoActivateRequestMethod;
    /**
     * @var string if filter can be auto-activated, define request method query name ($_GET['name'])
     */
    protected $autoActivateRequestKey;

    /**
     * table name
     * @var string
     */
    protected $table;
    /**
     * field name
     * @var string
     */
    protected $field;
    /**
     * key that define is active current filter or not.
     * Have more weight, than auto-activated properties
     * @var boolean
     */
    protected $active;
    /**
     * filter value
     * @var string
     */
    protected $value;

    /**
     * Class constructor
     * @param string $autoActivateRequestMethod
     * @param string $autoActivateRequestKey )
     */
    /**
     * Class constructor
     * @param string $table alias for query
     * @param string $field alias for query
     * @param null $autoActivateRequestMethod if filter can be auto-activated, define request method ('request', 'get', 'post')
     * @param null $autoActivateRequestKey if filter can be auto-activated, define request query key ($_GET[$key])
     */
    public function __construct($table, $field, $autoActivateRequestMethod = null, $autoActivateRequestKey = null){
        $this->autoActivateRequestMethod = $autoActivateRequestMethod;
        $this->autoActivateRequestKey = $autoActivateRequestKey;
        $this->table = $table;
        $this->field = $field;
    }

    /**
     * Active field setter.
     * If true, that mean item will be active,
     * if false, will be inactive
     * if null, autoactivate check will be invoked (default)
     * @param boolean $active
     */
    public function setActive($active){
        $this->active = $active;
    }

    /**
     * check, if this item must be used
     * in current SQL query for this http request
     * @return boolean
     * @throws FwException
     */
    public function isActive()
    {
        if($this->active !== null) {
            return $this->active;
        } else {
            return $this->isAutoActivate();
        }
    }

    /**
     * Value field getter. If object can be auto activated, return auto-activate value from request
     * @return string
     * @throws FwException
     */
    public function getValue()
    {
        if($this->value !== null) {
            return $this->value;
        }
        if($this->isAutoActivate()) {
            switch($this->autoActivateRequestMethod) {
                case self::REQUEST_REQUEST:
                    return $_REQUEST[$this->autoActivateRequestKey];
                case self::REQUEST_GET:
                    return $_GET[$this->autoActivateRequestKey];
                case self::REQUEST_POST:
                    return $_POST[$this->autoActivateRequestKey];
                default:
                    throw new FwException("Unknown HTTP method in ORM query item.");
            }
        }
        return "";
    }

    /**
     * Value setter.
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return boolean
     * @throws FwException
     */
    private function isAutoActivate(){
        if($this->autoActivateRequestMethod != null && $this->autoActivateRequestKey != null) {
            switch($this->autoActivateRequestMethod) {
                case self::REQUEST_REQUEST:
                    return isset($_REQUEST) && isset($_REQUEST[$this->autoActivateRequestKey]);
                case self::REQUEST_GET:
                    return isset($_GET) && isset($_GET[$this->autoActivateRequestKey]);
                case self::REQUEST_POST:
                    return isset($_POST) && isset($_POST[$this->autoActivateRequestKey]);
                default:
                    throw new FwException("Unknown HTTP method in ORM query item.");
            }
        } elseif($this->autoActivateRequestMethod != null) {
            throw new FwException("Query item contain property for autoActivateRequestMethod, but does not contain key.");
        } elseif($this->autoActivateRequestKey != null) {
            throw new FwException("Query item contain property for autoActivateRequestKey, but does not contain method.");
        }else {
            return false;
        }
    }
}