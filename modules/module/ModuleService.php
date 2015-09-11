<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 14.03.2015
 * Time: 20:25
 */
class ModuleService{
    /**
     * Service main table
     * @var OrmTable
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
     * @param $object OrmPersistenceBase
     * @throws Exception is this->table still undefined
     */
    public function save(OrmPersistenceBaseImpl $object){
        if($this->table == null){
            throw new Exception("There is no persistance model!");
        }
        if(is_array($object)){
            ORM::saveArray($this->getTable(), $object);
            return;
        }
        if(!is_subclass_of ($object, 'OrmPersistenceBaseImpl')){
            throw new Exception("This is not persistence object!");
        }
        ORM::saveData($this->getTable(), $object);
    }

    /**
     *
     * @return OrmPersistenceBase class object without joins
     * @param $key integer object id value
     * @return OrmPersistenceBase
     * @throws Exception if result set contain more than one row
     */
    public function load($key) {
        $filter = new OrmQueryFilter(
            $this->table->getName(),
            $this->table->getPrimaryKey(),
            OrmQueryFilter::TYPE_EQUAL
        );
        $filter->setActive(true);
        $filter->setValue($key);
        return $this->loadOneWithFilters($filter);
    }

    /**
     * Load data from database using condition
     *
     * @param OrmQueryFilter|OrmQueryFilter[] $filters
     * @return OrmPersistenceBase
     */
    public function loadOneWithFilters($filters) {
        return ORM::load($this->table->getName(), true, $filters, null, null);
    }

    /**
     * Load data from database using keys (id's) with/without setted order
     *
     * @param array $keys [1,3,5,6,7]
     * @param ORM_Query_Sorter|ORM_Query_Sorter[]|null
     * @return OrmPersistenceBase[]
     */
    public function loadUsingKeys(array $keys, $sorters = null) {
        $filter = new OrmQueryFilter($this->table->getName(), $this->table->getPrimaryKey(), OrmQueryFilter::TYPE_IN);
        $filter->setValue($keys);
        $filter->setActive(true);
        return ORM::load($this->table->getName(), false, $filter, $sorters, null);
    }


    /**
     * **
     * Load all data with/without setted condition
     *
     * @param OrmQueryFilter|OrmQueryFilter[]|null $filters
     * @param OrmQuerySorter|OrmQuerySorter[]|null $sorters
     * @param OrmQueryPaginator|null $pager
     * @return OrmPersistenceBase[]
     */
    public function loadAll($filters = null, $sorters = null, $pager = null) {
        return ORM::load($this->table->getName(), false, $filters, $sorters, $pager);
    }

    /**
     * Delete current object from database.
     * Object must contain primary key field != null
     * @param $object OrmPersistenceBase
     * @return bool
     */
    public function delete($object){
        return ORM::delete($this->table, $object);
    }

    /**
     * Get this module main table
     * @return OrmTable
     */
    public function getTable(){
        return $this->table;
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