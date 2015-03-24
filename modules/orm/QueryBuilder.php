<?php

class ORM_QueryBuilder {

    public static function getSelectFields(ORM_Objects_Table $table) {
        $out = array();
        foreach($table->getFields() as $field){
            $out[] = $table->getName().".".$field->getName()." AS ".$table->getName()."_".$field->getName();
        }
        return implode(",",$out);
    }

    public static function getJoin(ORM_Objects_Bind $bind) {
        if($bind->getType() == ORM_Objects_Table::ONE_TO_ONE || $bind->getType() == ORM_Objects_Table::MANY_TO_ONE) {
            $prefix = " INNER ";
        } else {
            $prefix = " LEFT ";
        }
        return $prefix." JOIN ".$bind->getRightTable()->getName()
        ." AS ".$bind->getRightTable()->getName()
        ." ON (".$bind->getRightTable()->getName().".".$bind->getRightTable()->getPrimaryKey()."="
        .$bind->getLeftTable()->getName().".".$bind->getLeftKey().") ";
    }

    public static function getMainTable(ORM_Objects_Table $table){
        return $table->getName()." AS ".$table->getName();
    }

    /**
     * @param $mainTable ORM_Objects_Table
     * @param $tail string
     * @param $binds boolean
     * @param $one boolean
     * @return string
     */
    public static function getLoadQuery($mainTable, $tail, $binds, $one){
        // select $select FROM $tables WHERE $tail
        $select = array();
        $tables = array();

        $select[] = ORM_QueryBuilder::getSelectFields($mainTable);
        $tables[] = ORM_QueryBuilder::getMainTable($mainTable);

        if($binds) {
            foreach($mainTable->getBinds() as $bind) {
                if(!$bind->getLazyLoad()) {
                    $tables[] = ORM_QueryBuilder::getJoin($bind);
                    $select[] = ORM_QueryBuilder::getSelectFields($bind->getRightTable());
                }
            }
        }



        return "SELECT ".implode(",", $select)
            ." FROM ".implode(" ", $tables)
            ." ".$tail;
    }

    public static function buildQueryForBind(ORM_Objects_Table $mainTable, $primaryKeyValue, $leftKey){
        return " WHERE ".$mainTable->getName().".".$leftKey."='".DB::escape($primaryKeyValue)."'";
    }

}