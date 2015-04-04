<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 04.04.2015
 * Time: 18:58
 */
class ORM_Query_CustomFilter extends  ORM_Query_AbstractItem{

    /**
     * current filter string in format
     * name = 'vasia' AND (role = 'admin' OR role = 'supervisor')
     * @var string
     */
    protected $query;

    public function __construct($table, $field, $query, $active){
        parent::__construct($table, $field);
        $this->setActive($active);
        $this->query = $query;
    }
    /**
     * Return command query command name,
     * to each this item must be applied
     * Command's is integer, not string,
     * because, each command define it's position in query.
     * @see ORM_QueryBuilder::applyQueryItems()
     * @return int
     */
    public function getQueryCommand()
    {
        return ORM_Commands::WHERE;
    }

    /**
     * Return query separator type:
     * ',', 'AND', 'OR', etc.
     * @return string
     */
    public function getQuerySeparator()
    {
        ORM_Query_IItem::SEPARATOR_COMMA;
    }

    /**
     * Build query string for this item
     * @return int
     */
    public function buildQueryString()
    {
        return $this->query;
    }

}