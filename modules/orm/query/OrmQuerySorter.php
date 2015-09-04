<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 04.04.2015
 * Time: 8:53
 */

class OrmQuerySorter extends OrmQueryAbstractItem {

    const DIRECTION_ASC = "ASC";
    const DIRECTION_DESC = "DESC";

    /**
     * Sort direction.
     * Can be null, 'ASC' and 'DESC'. If direction == null, it will be interpret as 'ASC'.
     * @var string
     */
    protected $direction;


    /**
     * Return command query command name,
     * to each this item must be applied
     * Command can be like - WHERE, ORDER, GROUP, LIMIT, etc.
     * @return string
     */
    public function getQueryCommand()
    {
        return OrmCommands::ORDER;
    }

    public function __construct($table, $field, $direction = null, $autoActivateRequestMethod = null, $autoActivateRequestKey = null){
        parent::__construct($table, $field, $autoActivateRequestMethod, $autoActivateRequestKey);
        if($direction != null ) {
            $this->setDirection($direction);
        }
    }

    /**
     * Return query separator type:
     * ',', 'AND', 'OR', etc.
     * @return string
     */
    public function getQuerySeparator()
    {
        return OrmQueryItem::SEPARATOR_COMMA;
    }

    /**
     * Build query string for this item
     * @return string
     */
    public function buildQueryString()
    {
        return $this->table.".".$this->field." ".$this->getDirection();
    }

    /**
     * Direction field setter
     * @param string $direction
     * @throws FwException
     */
    private function setDirection($direction)
    {
        if($direction === null || $direction == self::DIRECTION_ASC || $direction == self::DIRECTION_DESC) {
            $this->direction = $direction;
        } else {
            throw new FwException("Invalid direction type in ORM query sorter: '".$direction."'");
        }
    }

    private function getDirection()
    {
        if($this->direction == null) {
            return self::DIRECTION_ASC;
        } else {
            return $this->direction;
        }
    }

    /**
     * @Override
     * Sorter has no value.
     * @return null
     */
    public function getValue(){
        return null;
    }
    /**
     * @Override
     * Sorter has no value
     * @var string
     * @return null
     */
    public function setValue($value){
    }
}