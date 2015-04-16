<?php
class ORM_Utils{
    const BIND_PREFIX = 'Id';
    const DATE_FORMAT = 'Y-m-d';
    const DATETIME_FORMAT = 'Y-m-d H:i:s';
    /**
     * Load data from persistence storage
     * @param ORM_Table $mainTable main query table.
     * @param $useBinds boolean - use binds on query, or not
     * @param $one - return array, or first row only
     * @param $filters ORM_Query_Filter[]
     * @param $sorters ORM_Query_Sorter[]
     * @param $paginator ORM_Query_Paginator
     * @return ORM_Persistence_Base[]|ORM_Persistence_Base
     */
    public static function load(ORM_Table $mainTable, $one, $filters, $sorters, $paginator){
        $query = ORM_QueryBuilder::getLoadQuery($mainTable, $filters, $sorters, $paginator);
        $result = DB::getTable($query);
        return ORM_Utils::resultToArray($mainTable, $result, $one);
    }

    /**
     * Transform assoc array into objects array
     * @param ORM_Table $mainTable
     * @param $result array data of previous query [[person_id=>1, person_name=>2, address_id=1, address_name='Lenina st 45']]
     * @param $one boolean - if true, will be returned one object, of false, array of objects
     * @return ORM_Persistence_Base|ORM_Persistence_Base[]
     * @throws Exception
     */
    protected static function resultToArray(ORM_Table $mainTable, $result, $one){

        $tables = array($mainTable);
        foreach($mainTable->getBinds() as $bind) {
            if(!$bind->getLazyLoad()) {
                $tables[] = $bind->getRightTable();
            }
        }
        $orderedObject = ORM_Utils::buildOrderedObject($mainTable, $result, $tables);


        /* @var $table ORM_Table */
        foreach($tables as $table) {
            /* @var $bind ORM_Table_Bind */
            foreach ($table->getBinds() as $bind) {
                if (empty($orderedObject[$bind->getRightTable()->getName()])) {
                    continue;
                }
                $lKeyMethod = 'get' . ucfirst($bind->getLeftField());
                $lKeySetter = 'set' . ucfirst($bind->getLeftField());

                $rKeyMethod = 'get' . ucfirst($bind->getRightKey());
                foreach ($orderedObject[$table->getName()] as $entity) {
                    $setValue = null;
                    foreach ($orderedObject[$bind->getRightTable()->getName()] as $binded) {
                        if($table->getField($bind->getLeftKey())->getLazyLoad()){
                            continue;
                        }
                        if($bind->getLazyLoad()){
                            continue;
                        }
                        if ($bind->getType() == ORM_Table::ONE_TO_ONE || $bind->getType() == ORM_Table::MANY_TO_ONE) {
                            if ($entity->$lKeyMethod() == $binded->$rKeyMethod()) {
                                $entity->$lKeySetter($binded);
                                break;
                            }
                        } else {
                            if ($setValue == null) {
                                $setValue = array();
                            }
                            $setValue[$binded->getPrimaryKey()] = $binded;
                        }
                    }
                    if (is_array($setValue)) {
                        $entity->$lKeySetter($setValue);
                    }
                }
            }
        }
        if($one) {
            $out = reset($orderedObject[$mainTable->getName()]);
            return !empty($out)? $out : null;
        } else {
            return $orderedObject[$mainTable->getName()];
        }

    }

    protected static function fromArrayToObject(ORM_Table $table, $row){
        $class = $table->getPersistClassName();
        $out = new $class();

        $binds = $table->getBinds();
        foreach ($table->getFields() as $field) {
            $prefix = "";
            foreach($binds as $bind) {
                if($bind->getLeftKey() == $field->getName() && $bind->getLeftField() == $field->getName()) {
                    $prefix = ORM_Utils::BIND_PREFIX;
                    break;
                }
            }
            $method = "set".ucfirst($field->getName().$prefix);
            $out->$method($row[$table->getName()."_".$field->getName()]);
        }
        return $out;
    }

    /**
     * Save one multy leveled data array. Structure element - PersistBase class object
     * @param ORM_Table $mainTable
     * @param $object ORM_Persistence_Base[]
     * @throws Exception
     */
    public static function saveArray(ORM_Table $mainTable, $object){
        foreach($object as $value){
            if(is_array($value)){
                ORM_Utils::saveArray($mainTable, $value);
            }else{
                $class = get_class($object);
                $workTable = null;
                if($mainTable->getPersistClassName() == $class){
                    $workTable = $mainTable;
                } else {
                    foreach($mainTable->getBinds() as $bind) {
                        if($bind->getRightTable()->getPersistClassName() == $class){
                            $workTable = $bind->getRightTable();
                            break;
                        }
                    }
                }

                if($workTable === null){
                    throw new Exception("Unknown data type ".$class);
                }
                ORM_Utils::saveData($workTable, $value);
            }
        }
    }

    /**
     * Save or update PersistBase object
     * @param ORM_Table $table
     * @param ORM_Persistence_Base $object
     */
    public static function saveData(ORM_Table $table, ORM_Persistence_Base $object){
        $valuesToInsert = array();
        $keysToInsert = array();
        $pairsToUpdate = array();

        foreach($table->getFields() as $field){
            $method = "get".ucfirst($field->getName());
            $currentData = $object->$method();
            if(empty($currentData) && $field->getAutoIncrement()){
                continue;
            }
            $value = ORM_Utils::getValueForField($field, $currentData);
            $valuesToInsert[] = $value;
            $keysToInsert[] = $field->getName();

            if(!$field->getPrimary()){
                $pairsToUpdate[] = $field->getName()."=".$value;
            }

        }
        $query = "INSERT INTO ".$table->getName()
            ."(".implode(",", $keysToInsert).") VALUES "
            ."(".implode(",", $valuesToInsert).") "
            ." ON DUPLICATE KEY UPDATE "
            .implode(",", $pairsToUpdate);
        DB::query($query);
        ORM_Utils::fixId($table, $object);
    }

    public static function getValueForField($field, $value) {
        switch($field->getType()) {
            case 'bit':
            case 'boolean':
                if($value === true || $value === 1 || $value === 'true' || $value === 'on'){
                    return 1;
                } elseif(empty($value)){
                    return 0;
                } else {
                    intval($value) === 0 ? 0 : 1;
                }
            case 'integer':
            case 'tinyint':
                return intval($value);
            case 'date':
                if(is_numeric($value)) {
                    $value = date(ORM_Utils::DATE_FORMAT, $value);
                }
            case 'datetime':
                if(is_numeric($value)) {
                    $value = date(ORM_Utils::DATETIME_FORMAT, $value);
                }
            default:
                return "'".DB::escape($value)."'";
        }
    }

    /**
     * Set id to inserted object, if necessary
     * @param ORM_Table $table
     * @param $object
     */
    protected static function fixId(ORM_Table $table, $object){
        $pkField = ORM_Utils::getPrimaryKeyField($table);
        $name = $pkField->getName();
        $getter = "get".ucfirst($name);
        $pk = $object->$getter();
        if(empty($pk)){
            $pk = DB::getLastInsertedId();
            $setter = "set".ucfirst($name);
            $object->$setter($pk);
        }
    }

    /**
     * Delete object from data base
     * @param ORM_Table $table
     * @param ORM_Persistence_Base $object
     * @return bool
     */
    public static function delete(ORM_Table $table, ORM_Persistence_Base $object){
        $pkField = ORM_Utils::getPrimaryKeyField($table);
        $name = $pkField->getName();
        $getter = "get".ucfirst($name);
        $pkValue = $object->$getter();
        if(!empty($pkValue)) {

            $allBinds = array();

            foreach ($table->getBinds() as $bind) {
                if ($bind->getType() == ORM_Table::ONE_TO_ONE
                ) {
                    ORM_Utils::collectJoins($allBinds, $bind->getLeftTable());
                }
                if ($bind->getType() == ORM_Table::ONE_TO_MANY) {
                    foreach ($bind->loadBindedData($object->getPrimaryKey()) as $joined) {
                        ORM_Utils::delete($bind->getRightTable(), $joined);
                    }
                }
            }
            $query = "DELETE ".$table->getName()." FROM ".$table->getName().' AS '.$table->getName();
            /* @var $bind ORM_Table_Bind */
            foreach($allBinds as $bind) {
                if($bind->getLeftTable()->getName() == $bind->getRightTable()->getName()){
                    //parent bind type
                    continue;
                }
                if($bind->getLeftTable()->getName() != $table->getName()) {
                    $bindedTable1 = $bind->getLeftTable()->getName();
                    $bindedTableKey1 = $bind->getLeftKey();
                    $bindedTable2 = $bind->getRightTable()->getName();
                    $bindedTableKey2 = $bind->getRightKey();
                } else {
                    $bindedTable1 = $bind->getRightTable()->getName();
                    $bindedTableKey1 = $bind->getRightKey();
                    $bindedTable2 = $bind->getLeftTable()->getName();
                    $bindedTableKey2 = $bind->getLeftKey();
                }
                $query .= " INNER JOIN ".$bindedTable1." ON ("
                    .$bindedTable1.".".$bindedTableKey1."=".$bindedTable2.".".$bindedTableKey2.")";
            }
            $query .= " WHERE ".$table->getName()."." . $name . "='" . DB::escape($pkValue) . "'";
            $setter = "set".ucfirst($name);
            $object->$setter(null);
            return DB::query($query);
        } else {
            return false;
        }
    }

    /**
     * Get primary key field from ORM_Table object
     * @param ORM_Table $table
     * @return ORM_Table_Field
     */
    public function getPrimaryKeyField(ORM_Table $table){
        foreach($table->getFields() as $field){
            if($field->getPrimary()){
                return $field;
            }
        }
        return null;
    }

    /**
     * Build ordered object before data transformation into Persist objects
     * @param ORM_Table $mainTable
     * @param $result
     * @param $tables ORM_Table[]
     * @return array
     */
    private static function buildOrderedObject(ORM_Table $mainTable, $result, $tables)
    {
        $out = array();
        $out[$mainTable->getName()] = array();
        foreach($result as $row){
            foreach($tables as $table) {
                $index = $row[$table->getName()."_".$table->getPrimaryKey()];

                /*@TODO wtf?
                if(!empty($out[$table->getName()][$index])){
                    continue;
                }
                */
                $obj = ORM_Utils::fromArrayToObject($table, $row, $index);
                if($obj == null) {
                    continue;
                }
                $out[$table->getName()][$obj->getPrimaryKey()] = $obj;
            }
        }
        return $out;
    }

    /**
     * @param $allBinds ORM_Table_Bind[]
     * @param $table ORM_Table
     */
    private static function collectJoins(&$allBinds, $table)
    {
        foreach ($table->getBinds() as $localBind) {
            if($localBind->getType() != ORM_Table::ONE_TO_ONE) {
                continue;
            }
            foreach($allBinds as $storedBind) {
                if($storedBind->getLeftTable()->getName() == $localBind->getLeftTable()->getName()
                    &&
                    $storedBind->getRightTable()->getName() == $localBind->getRightTable()->getName()
                    &&
                    $storedBind->getLeftKey() == $localBind->getLeftKey()
                    &&
                    $storedBind->getRightKey() == $localBind->getRightKey()
                ){
                    //this binds are equal
                    $check = false;
                    break;
                }
                if($storedBind->getLeftTable()->getName() == $localBind->getRightTable()->getName()
                    &&
                    $storedBind->getRightTable()->getName() == $localBind->getLeftTable()->getName()
                    &&
                    $storedBind->getLeftKey() == $localBind->getRightKey()
                    &&
                    $storedBind->getRightKey() == $localBind->getLeftKey()
                ){
                    //this binds are mirrors
                    $check = false;
                    break;
                }
            }
            if($check) {
                $allBinds[] = $localBind;
                ORM_Utils::collectJoins($allBinds, $localBind->getLeftTable());
            }
        }
    }

    /**
     * Build object from json data
     * @param $table ORM_Table
     * @param $data array[id=1, name='Serg', birthdate='1986-02-21']
     * @return array[0=>ORM_Persistence_Base, 1=>errors[]]
     */
    public static function buildObject($table, $data)
    {
        $objectClass = $table->getPersistClassName();
        $object = new $objectClass;
        $errors = array();

        foreach($table->getFields() as $field) {
            $name = $field->getName();
            if (!isset($data[$name])) {
                $data[$name] = null;
            }
            if ($field->validateValue($data[$name], $errors)) {
                $setter = "set" . ucfirst($name);
                $object->$setter($data[$name]);
            }
        }
        return array($object, $errors);
    }

}