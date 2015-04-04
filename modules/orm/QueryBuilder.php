<?php

class ORM_QueryBuilder {

    public static function getSelectFields(ORM_Table $table) {
        $out = array();
        foreach($table->getFields() as $field){
            $out[] = $table->getName().".".$field->getName()." AS ".$table->getName()."_".$field->getName();
        }
        return implode(",",$out);
    }

    public static function getJoin(ORM_Table_Bind $bind) {
        if($bind->getType() == ORM_Table::ONE_TO_ONE || $bind->getType() == ORM_Table::MANY_TO_ONE) {
            $prefix = " INNER ";
        } else {
            $prefix = " LEFT ";
        }
        return $prefix." JOIN ".$bind->getRightTable()->getName()
        ." AS ".$bind->getRightTable()->getName()
        ." ON (".$bind->getRightTable()->getName().".".$bind->getRightTable()->getPrimaryKey()."="
        .$bind->getLeftTable()->getName().".".$bind->getLeftKey().") ";
    }

    public static function getMainTable(ORM_Table $table){
        return $table->getName()." AS ".$table->getName();
    }

    /**
     * @param $mainTable ORM_Table
     * @param ORM_Query_Filter|ORM_Query_Filter[] $filters
     * @param ORM_Query_Sorter|ORM_Query_Sorter[] $sorters
     * @param ORM_Query_Paginator $paginator
     * @return string
     */
    public static function getLoadQuery($mainTable, $filters, $sorters, $paginator){
        // select $select FROM $tables WHERE $tail
        $select = array();
        $tables = array();

        $select[] = ORM_QueryBuilder::getSelectFields($mainTable);
        $tables[] = ORM_QueryBuilder::getMainTable($mainTable);

        foreach($mainTable->getBinds() as $bind) {
            if(!$bind->getLazyLoad()) {
                $tables[] = ORM_QueryBuilder::getJoin($bind);
                $select[] = ORM_QueryBuilder::getSelectFields($bind->getRightTable());
            }
        }
        $out = "SELECT ".implode(",", $select)." FROM ".implode(" ", $tables);

        if($filters != null) {
            if(is_array($filters)) {
                $out .= ORM_QueryBuilder::getItemsPackCondition($filters);
            } else {
                $out .= ORM_QueryBuilder::getSingleItemCondition($filters);
            }
        }

        if($sorters != null) {
            if(is_array($sorters)) {
                $out .= ORM_QueryBuilder::getItemsPackCondition($sorters);
            } else {
                $out .= ORM_QueryBuilder::getSingleItemCondition($sorters);
            }
        }
        if($paginator != null) {
            $out .= $paginator->getValue();
        }
        return $out;
    }

    private static function getItemsPackCondition($items){
        $out = array();
        $condition = null;
        $separator = null;
        /* @var $item ORM_Query_Filter */
        foreach($items as $item) {
            if($item->isActive()) {
                if($condition == null) {
                    $separator = $item->getQuerySeparator();
                    $condition = ORM_Commands::get($item->getQueryCommand());
                }
                $out[] = $item->buildQueryString();
            }
        }
        if(count($out) > 0) {
            return " ".$condition.implode($separator, $out);
        } else {
            return "";
        }
    }

    private static function getSingleItemCondition($item){
        $out = null;
        $condition = null;
        /* @var $item ORM_Query_Filter */
        if($item->isActive()) {
            $condition = ORM_Commands::get($item->getQueryCommand());
            $out = $item->buildQueryString();
        }
        if($out != null) {
            return " ".$condition." ".$out;
        } else {
            return "";
        }
    }
}