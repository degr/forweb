<?php

/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 04.04.2015
 * Time: 8:53
 */

class OrmQueryFilter extends OrmQueryAbstractItem{

    const TYPE_EQUAL = "equal";
    const TYPE_LIKE = "like";
    const TYPE_LIKE_START_WITH = "like_start";
    const TYPE_LIKE_END_WITH = "like_end";
    const TYPE_IN = "in";
    const TYPE_NOT_EQUAL = "not_equal";

    const VALUE_TYPE_STRING = "string";
    const VALUE_TYPE_INTEGER = "integer";
    const VALUE_TYPE_BOOLEAN = "boolean";
    /**
     * filter type. Can be one of the ORM_Query_Filter constant with prefix 'TYPE_'
     * @var string
     */
    protected $type;

    /**
     * filter value type. Can be string, integer, boolean.
     * @var string
     */
    protected $valueType;



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
        return OrmCommands::WHERE;
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
        return OrmQueryItem::SEPARATOR_AND;
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
        if(is_array($value)) {
            foreach($value as &$v) {
                $v = $this->prepareValue($v);
            }
        } else {
            $value = $this->prepareValue($value);
        }

        if($this->type === self::TYPE_IN){
            if(is_array($value)){
                $param = "('".implode("', '", $value)."')";
            } else {
                $param = "'" . DB::escape($value) . "'";
            }
        } elseif($this->getType() == self::TYPE_LIKE) {
            $param = "'%".$value."%'";
        } elseif($this->getType() == self::TYPE_LIKE_START_WITH) {
            $param = "'".$value."%'";
        }elseif($this->getType() == self::TYPE_LIKE_END_WITH) {
            $param = "'%".$value."'";
        }else{
            if(empty($this->valueType) || $this->valueType == self::VALUE_TYPE_STRING){
                $param = "'".$value."'";
            } else {
                $param = $value;
            }
        }
        return $table.$operator.$param;
    }

    /**
     * @return string
     */
    public function getValueType() {
        return $this->valueType;
    }

    /**
     * @param string $valueType
     */
    public function setValueType($valueType) {
        $this->valueType = $valueType;
    }

    private function prepareValue($v)
    {
        $type = $this->getValueType();
        if(empty($type)) {
            $type = self::VALUE_TYPE_STRING;
        }
        if(empty($type) || $type == self::VALUE_TYPE_STRING) {
            return DB::escape($v);
        } elseif($type == self::VALUE_TYPE_INTEGER) {
            return intval($v);
        }elseif($type == self::VALUE_TYPE_BOOLEAN) {
            return intval($v) == 1 ? 1 : 0;
        }else{
            throw new FwException("Undefined filter value type: ".$type);
        }


    }

}