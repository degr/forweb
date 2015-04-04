<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 04.04.2015
 * Time: 8:53
 */

class ORM_Query_Filter extends ORM_Query_AbstractItem{

    const TYPE_EQUAL = "equal";
    const TYPE_LIKE = "like";
    const TYPE_LIKE_START_WITH = "like_start";
    const TYPE_LIKE_END_WITH = "like_end";
    const TYPE_IN = "in";
    const TYPE_NOT_EQUAL = "not_equal";

    /**
     * filter type
     * @var string
     */
    protected $type;

    /**
     * Type field getter
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function __construct($table, $field, $type = null, $autoActivateRequestMethod = null, $autoActivateRequestKey = null){
        parent::__construct($table, $field, $autoActivateRequestMethod, $autoActivateRequestKey);
        if($type != null ) {
            $this->setType($type);
        }
    }

    /**
     * Return command query command name,
     * to each this item must be applied
     * Command can be like - WHERE, ORDER, GROUP, LIMIT, etc.
     * @return int
     */
    public function getQueryCommand()
    {
        return ORM_Commands::WHERE;
    }

    /**
     * set filter compare type. Allowed values: [equal, like, in].
     * Better use type constants: ORM_Query_Filter::TYPE_EQUAL, TYPE_LIKE, TYPE_IN
     * @param string $type
     * @throws FwException
     */
    public function setType($type) {
        if($type == self::TYPE_EQUAL || $type == self::TYPE_LIKE || $type == self::TYPE_IN){
            $this->type = $type;
        } else {
            throw new FwException("Invalid type for filter: ".$type);
        }
    }

    /**
     * @Override
     * Return query separator type:
     * ',', 'AND', 'OR', etc.
     * @return string
     */
    public function getQuerySeparator()
    {
        return ORM_Query_IItem::SEPARATOR_AND;
    }

    /**
     * @Override
     * Build query string for this item
     * @return string
     */
    public function buildQueryString()
    {
        $table = $this->table.".".$this->field;
        switch($this->type) {
            case self::TYPE_NOT_EQUAL:
                $operator = '<>';
                break;
            case self::TYPE_IN;
                $operator = " IN ";
                break;
            default:
                $operator = "=";
        }
        $value = $this->getValue();
        if($this->type === self::TYPE_IN && is_array($value)){
            $params = array();
            foreach($value as $key) {
                $params[] = DB::escape($key);
            }
            $param = "(".implode("', '", $params).")";
        } else {
            $param = DB::escape($this->getValue());
            if($this->type === self::TYPE_IN) {
                $param = "'".$param."'";
            }
        }
        $param = DB::escape($this->getValue());
        if($this->getType() == self::TYPE_LIKE) {
            $param = "%".$param."%";
        } elseif($this->getType() == self::TYPE_LIKE_START_WITH) {
            $param .= "%";
        }elseif($this->getType() == self::TYPE_LIKE_END_WITH) {
            $param = "%".$param;
        }
        return $table.$operator.$param;
    }

}