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
     * @var ORM_Table
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
        $filter = new ORM_Query_Filter(
            $this->table->getName(),
            $this->table->getPrimaryKey(),
            ORM_Query_Filter::TYPE_EQUAL
        );
        $filter->setActive(true);
        $filter->setValue($key);
        return $this->loadOneWithFilters($filter);
    }

    /**
     * Load data from database using condition
     *
     * @param ORM_Query_Filter|ORM_Query_Filter[] $filters
     * @return ORM_Persistence_Base
     */
    public function loadOneWithFilters($filters) {
        return ORM::load($this->table->getName(), true, $filters, null, null);
    }

    /**
     * Load data from database using keys (id's) with/without setted order
     *
     * @param array $keys [1,3,5,6,7]
     * @param ORM_Query_Sorter|ORM_Query_Sorter[]|null
     * @return ORM_Persistence_Base[]
     */
    public function loadUsingKeys(array $keys, $sorters = null) {
        $filter = new ORM_Query_Filter($this->table->getName(), $this->table->getPrimaryKey(), ORM_Query_Filter::TYPE_IN);
        $filter->setValue($keys);
        $filter->setActive(true);
        return ORM::load($this->table->getName(), true, $filter, $sorters, null);
    }


    /**
     * **
     * Load all data with/without setted condition
     *
     * @param ORM_Query_Filter|ORM_Query_Filter[]|null $filters
     * @param ORM_Query_Sorter|ORM_Query_Sorter[]|null $sorters
     * @param ORM_Query_Paginator|null $pager
     * @return ORM_Persistence_Base[]
     */
    public function loadAll($filters = null, $sorters = null, $pager = null) {
        return ORM::load($this->table->getName(), false, $filters, $sorters, $pager);
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
     * @return ORM_Table
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