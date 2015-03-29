<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 14.03.2015
 * Time: 20:25
 */
class Service{
    /**
     * Service main table
     * @var ORM_Objects_Table
     */
    protected $table;

    /**
     * Class constructor.
     * @param string $tableName
     */
    public function __construct($tableName){
        if(!empty($tableName)) {
            $this->table = ORM::getTable($tableName);
        }
    }

    /**
     * Save or update current object in database
     * @param $object ORM_Persistence_Base
     * @throws Exception is this->table still undefined
     */
    public function save(ORM_Persistence_Base $object){
        if($this->table == null){
            throw new Exception("There is no persistance model!");
        }
        if(is_array($object)){
            ORM::saveArray($this->getTable(), $object);
            return;
        }
        if(!is_subclass_of ($object, 'PersistBase')){
            throw new Exception("This is not persistence object!");
        }
        ORM::saveData($this->getTable(), $object);
    }

    /**
     *
     * @return ORM_Persistence_Base class object without joins
     * @param $key integer object id value
     * @return ORM_Persistence_Base
     * @throws Exception if result set contain more than one row
     */
    public function load($key) {
        $tail = " WHERE ".$this->table->getPrimaryKey()."='".DB::escape($key)."'";
        return $this->loadWithCondition($tail);
    }

    /**
     * Load data from database using condition
     *
     * @param $tail string as example: 'where id=10 order by date'
     * @return ORM_Persistence_Base
     * @throws Exception if result set contain more than one row
     */
    public function loadWithCondition($tail) {
        $out = ORM::load($this->table->getName(), $tail, false, true);
        $count = count($out);
        if( $count > 1){
            throw new Exception('query return more than one row');
        }elseif ($count === 0){
            return null;
        }else{
            return $out;
        }
    }

    /**
     * Load data from database using keys (id's) with/without setted order
     *
     * @param array $keys [1,3,5,6,7]
     * @param string $order 'ORDER BY date'
     * @return ORM_Persistence_Base[]
     */
    public function loadUsingKeys(array $keys, $order="") {
        foreach($keys as &$key){
            $key = DB::escape($key);
        }
        unset($key);
        $tail = "WHERE ".$this->table->getPrimaryKey()." IN ('".implode("','",$keys)."') ".$order;
        return $this->loadAll($tail);
    }

    /**
     * Load data with joins
     * @param $tail string
     * @return ORM_Persistence_Base[]
     * @throws Exception if result set contain more than one row
     */
    public function loadJoined($tail) {
        $out = ORM::load($this->table, $tail, true, true);
        $count = count($out[$this->table->getName()]);
        if( $count > 1){
            throw new Exception('query return more than one row');
        }elseif ($count === 0){
            return null;
        }else{
            $out[$this->table->getName()] = $out[$this->table->getName()][0];
            return $out;
        }
    }

    /**
     * Load all data with/without setted condition
     * @param $tail 'WHERE name like 'a%' ORDER BY birthday DESC'
     * @return ORM_Persistence_Base[]
     */
    public function loadAll($tail) {
        return ORM::load($this->table->getName(), $tail, true, false);
    }

    /**
     * Delete current object from database.
     * Object must contain primary key field != null
     * @param $object ORM_Persistence_Base
     * @return bool
     */
    public function delete($object){
        return ORM::delete($this->table, $object);
    }

    /**
     * Get this module main table
     * @return ORM_Objects_Table
     */
    public function getTable(){
        return $this->table;
    }

    /**
     * show current table
     */
    public function showPersistObject(){
        if($this->table === null){
            debug(null);
        }
        $model = array();
        $model['table name'] = $this->table->getName();
        $model['table fields'] = $this->table->getFieldsToArray();

        foreach($this->table->getBinds() as $bind){
            $binded = array();
            $table = $bind->getRightTable();
            $binded['binded on'] = $this->table->getName()
                .".".$bind->getLeftKey()." -> "
                .$table->getName().".".$bind->getRightKey();
            $binded['binded type'] = $bind->getType();
            $binded['table fields'] = $table->getFieldsToArray();
            $model['binded tables'][] = $binded;
        }
        debug($model);
    }

    public function deleteById($id)
    {
        $object = $this->load($id);
        if($object != null) {
            return $this->delete($object);
        } else {
            return null;
        }
    }
}