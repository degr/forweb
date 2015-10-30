<?php

class OrmQueryBuilder {

    public static function getSelectFields(OrmTable $table) {
        $out = array();
        foreach($table->getFields() as $field){
            $out[] = $table->getName().".".$field->getName()." AS ".$table->getName()."_".$field->getName();
        }
        return implode(",",$out);
    }

    public static function getJoin(OrmTableBind $bind) {
        if($bind->getType() == OrmTable::ONE_TO_ONE || $bind->getType() == OrmTable::MANY_TO_ONE) {
            $prefix = " INNER ";
        } else {
            $prefix = " LEFT ";
        }
        return $prefix." JOIN ".$bind->getRightTable()->getName()
        ." AS ".$bind->getRightTable()->getName()
        ." ON (".$bind->getRightTable()->getName().".".$bind->getRightTable()->getPrimaryKey()."="
        .$bind->getLeftTable()->getName().".".$bind->getLeftKey().") ";
    }

    public static function getMainTable(OrmTable $table){
        return $table->getName()." AS ".$table->getName();
    }

    /**
     * @param $mainTable OrmTable
     * @param OrmQueryFilter|OrmQueryFilter[] $filters
     * @param OrmQuerySorter|OrmQuerySorter[] $sorters
     * @param OrmQueryPaginator $paginator
     * @return string
     */
    public static function getLoadQuery($mainTable, $filters, $sorters, $paginator){
        // select $select FROM $tables WHERE $tail
        $select = array();
        $tables = array();

        $select[] = OrmQueryBuilder::getSelectFields($mainTable);
        $tables[] = OrmQueryBuilder::getMainTable($mainTable);

        foreach($mainTable->getBinds() as $bind) {
            if(!$bind->getLazyLoad()) {
                $tables[] = OrmQueryBuilder::getJoin($bind);
                $select[] = OrmQueryBuilder::getSelectFields($bind->getRightTable());
            }
        }
        $out = "SELECT ".implode(",", $select)." FROM ".implode(" ", $tables);
        $out = "SELECT * FROM ".implode(" ", $tables);

        if($filters != null) {
            if(is_array($filters)) {
                $out .= OrmQueryBuilder::getItemsPackCondition($filters);
            } else {
                $out .= OrmQueryBuilder::getSingleItemCondition($filters);
            }
        }

        if($sorters != null) {
            if(is_array($sorters)) {
                $out .= OrmQueryBuilder::getItemsPackCondition($sorters);
            } else {
                $out .= OrmQueryBuilder::getSingleItemCondition($sorters);
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
        /* @var $item OrmQueryFilter */
        foreach($items as $item) {
            if($item->isActive()) {
                if($condition == null) {
                    $separator = $item->getQuerySeparator();
                    $condition = OrmCommands::get($item->getQueryCommand());
                }
                $out[] = $item->buildQueryString();
            }
        }
        if(count($out) > 0) {
            return " ".$condition." ".implode($separator, $out);
        } else {
            return "";
        }
    }

    private static function getSingleItemCondition($item){
        $out = null;
        $condition = null;
        /* @var $item OrmQueryFilter */
        if($item->isActive()) {
            $condition = OrmCommands::get($item->getQueryCommand());
            $out = $item->buildQueryString();
        }
        if($out != null) {
            return " ".$condition." ".$out;
        } else {
            return "";
        }
    }
}