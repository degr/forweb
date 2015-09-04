<?php
class OrmUtils{
    const BIND_PREFIX = 'Id';
    const DATE_FORMAT = 'Y-m-d';
    const DATETIME_FORMAT = 'Y-m-d H:i:s';
    /**
     * Load data from persistence storage
     * @param OrmTable $mainTable main query table.
     * @param $useBinds boolean - use binds on query, or not
     * @param $one - return array, or first row only
     * @param $filters OrmQueryFilter[]
     * @param $sorters OrmQuerySorter[]
     * @param $paginator OrmQueryPaginator
     * @return OrmPersistenceBase[]|OrmPersistenceBase
     */
    public static function load(OrmTable $mainTable, $one, $filters, $sorters, $paginator){
        $query = OrmQueryBuilder::getLoadQuery($mainTable, $filters, $sorters, $paginator);
        $result = DB::getTable($query);
        return OrmUtils::resultToArray($mainTable, $result, $one);
    }
    /**
     * Transform assoc array into objects array
     * @param OrmTable $mainTable
     * @param $result array data of previous query [[person_id=>1, person_name=>2, address_id=1, address_name='Lenina st 45']]
     * @param $one boolean - if true, will be returned one object, of false, array of objects
     * @return OrmPersistenceBase|OrmPersistenceBase[]
     * @throws Exception
     */
    protected static function resultToArray(OrmTable $mainTable, $result, $one){
        $tables = array($mainTable);
        foreach($mainTable->getBinds() as $bind) {
            if(!$bind->getLazyLoad()) {
                $tables[] = $bind->getRightTable();
            }
        }
        $orderedObject = OrmUtils::buildOrderedObject($mainTable, $result, $tables);
        /* @var $table OrmTable */
        foreach($tables as $table) {
            /* @var $bind OrmTableBind */
            foreach ($table->getBinds() as $bind) {
                if (empty($orderedObject[$bind->getRightTable()->getName()])) {
                    continue;
                }
                $postfix = $bind->getLeftField() === $table->getField($bind->getLeftKey())->getName() ? OrmUtils::BIND_PREFIX : "";
                $lKeyMethod = 'get' . ucfirst($bind->getLeftKey()).$postfix;
                $lKeySetter = 'set' . ucfirst($bind->getLeftField());
                $rKeyMethod = 'get' . ucfirst($bind->getRightKey());
                foreach ($orderedObject[$table->getName()] as $entity) {
                    $setValue = null;
                    /** @var $binded OrmPersistenceBase*/
                    foreach ($orderedObject[$bind->getRightTable()->getName()] as $binded) {
                        if($table->getField($bind->getLeftKey())->getLazyLoad()){
                            continue;
                        }
                        if($bind->getLazyLoad()){
                            continue;
                        }
                        if ($bind->getType() == OrmTable::ONE_TO_ONE || $bind->getType() == OrmTable::MANY_TO_ONE) {
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
    protected static function fromArrayToObject(OrmTable $table, $row){
        $class = $table->getPersistClassName();
        $out = new $class();
        $binds = $table->getBinds();
        foreach ($table->getFields() as $field) {
            $prefix = "";
            foreach($binds as $bind) {
                if($bind->getLeftKey() == $field->getName() && $bind->getLeftField() == $field->getName()) {
                    $prefix = OrmUtils::BIND_PREFIX;
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
     * @param OrmTable $mainTable
     * @param $object OrmPersistenceBase[]
     * @throws Exception
     */
    public static function saveArray(OrmTable $mainTable, $object){
        foreach($object as $value){
            if(is_array($value)){
                OrmUtils::saveArray($mainTable, $value);
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
                OrmUtils::saveData($workTable, $value);
            }
        }
    }
    /**
     * Save or update PersistBase object
     * @param OrmTable $table
     * @param OrmPersistenceBase $object
     */
    public static function saveData(OrmTable $table, OrmPersistenceBaseImpl $object){
        $valuesToInsert = array();
        $keysToInsert = array();
        $pairsToUpdate = array();
        foreach($table->getFields() as $field){
            $prefix = '';
            foreach ($table->getBinds() as $bind) {
                if($bind->getLeftKey() == $field->getName() && $bind->getLeftField() == $field->getName()) {
                    $prefix = OrmUtils::BIND_PREFIX;
                }
            }
            $method = "get".ucfirst($field->getName()).$prefix;
            $currentData = $object->$method();
            if(empty($currentData) && $field->getAutoIncrement()){
                continue;
            }
            $value = OrmUtils::getValueForField($field, $currentData);
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
        OrmUtils::fixId($table, $object);
    }
    /**
     * @param $field OrmTableField
     * @param $value
     * @return int|string
     */
    public static function getValueForField($field, $value) {
        $dv = $field->getDefaultValue();
        if(!isset($value) && isset($dv)) {
            $value = $dv;
        }
        switch($field->getType()) {
            case 'bit':
            case 'boolean':
                if($value === true || $value === 1 || $value === 'true' || $value === 'on'){
                    return 1;
                } elseif(empty($value)){
                    return 0;
                } else {
                    $value = intval($value) === 0 ? 0 : 1;
                }
            // no break
            case 'integer':
            case 'tinyint':
                return intval($value);
            case 'date':
                if(is_numeric($value)) {
                    $value = date(OrmUtils::DATE_FORMAT, $value);
                }
            // no break
            case 'datetime':
                if(is_numeric($value)) {
                    $value = date(OrmUtils::DATETIME_FORMAT, $value);
                }
            // no break
            default:
                return "'".DB::escape($value)."'";
        }
    }
    /**
     * Set id to inserted object, if necessary
     * @param OrmTable $table
     * @param $object
     */
    protected static function fixId(OrmTable $table, $object){
        $pkField = OrmUtils::getPrimaryKeyField($table);
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
     * @param OrmTable $table
     * @param OrmPersistenceBase $object
     * @return bool
     */
    public static function delete(OrmTable $table, OrmPersistenceBaseImpl $object){
        $pkField = OrmUtils::getPrimaryKeyField($table);
        $name = $pkField->getName();
        $getter = "get".ucfirst($name);
        $pkValue = $object->$getter();
        if(!empty($pkValue)) {
            $allBinds = array();
            foreach ($table->getBinds() as $bind) {
                if ($bind->getType() == OrmTable::ONE_TO_ONE
                ) {
                    OrmUtils::collectJoins($allBinds, $bind->getLeftTable());
                }
                if ($bind->getType() == OrmTable::ONE_TO_MANY) {
                    foreach ($bind->loadBindedData($object->getPrimaryKey()) as $joined) {
                        OrmUtils::delete($bind->getRightTable(), $joined);
                    }
                }
            }
            $query = "DELETE ".$table->getName()." FROM ".$table->getName().' AS '.$table->getName();
            /* @var $bind OrmTableBind */
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
     * @param OrmTable $table
     * @return OrmTableField
     */
    public function getPrimaryKeyField(OrmTable $table){
        foreach($table->getFields() as $field){
            if($field->getPrimary()){
                return $field;
            }
        }
        return null;
    }
    /**
     * Build ordered object before data transformation into Persist objects
     * @param OrmTable $mainTable
     * @param $result
     * @param $tables OrmTable[]
     * @return array
     */
    private static function buildOrderedObject(OrmTable $mainTable, $result, $tables)
    {
        $out = array();
        $out[$mainTable->getName()] = array();
        foreach($result as $row){
            foreach($tables as $table) {
                /** @var $obj OrmPersistenceBase */
                $obj = OrmUtils::fromArrayToObject($table, $row);
                if($obj == null) {
                    continue;
                }
                $out[$table->getName()][$obj->getPrimaryKey()] = $obj;
            }
        }
        return $out;
    }
    /**
     * @param $allBinds OrmTableBind[]
     * @param $table OrmTable
     */
    private static function collectJoins(&$allBinds, $table)
    {
        foreach ($table->getBinds() as $localBind) {
            if($localBind->getType() != OrmTable::ONE_TO_ONE) {
                continue;
            }
            $check = false;
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
                OrmUtils::collectJoins($allBinds, $localBind->getLeftTable());
            }
        }
    }
    /**
     * Build object from json data
     * @param $table OrmTable
     * @param $data array[id=1, name='Serg', birthdate='1986-02-21']
     * @return array[0=>OrmPersistenceBase, 1=>errors[]]
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
            $postfix = '';
            foreach($table->getBinds() as $bind) {
                if($bind->getLeftField() === $field->getName()){
                    $postfix = $bind->getLeftField() === $field->getName() ? OrmUtils::BIND_PREFIX : "";
                    break;
                }
            }
            $setter = "set" . ucfirst($name).$postfix;
            $object->$setter($data[$name]);
        }
        return array($object, $errors);
    }
}