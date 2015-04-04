<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 04.04.2015
 * Time: 8:55
 */

interface ORM_Query_IItem {


    const SEPARATOR_COMMA = ",";
    const SEPARATOR_AND = "AND";
    const SEPARATOR_OR = "OR";



    /**
     * Return command query command name,
     * to each this item must be applied
     * Command's is integer, not string,
     * because, each command define it's position in query.
     * @see ORM_QueryBuilder::applyQueryItems()
     * @return int
     */
    public function getQueryCommand();

    /**
     * Return query separator type:
     * ',', 'AND', 'OR', etc.
     * @return string
     */
    public function getQuerySeparator();

    /**
     * Build query string for this item
     * @return int
     */
    public function buildQueryString();

    /**
     * check, if this item must be used
     * in current SQL query for this http request
     * @return boolean
     */
    public function isActive();
}
