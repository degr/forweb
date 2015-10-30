<?php

/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 10/30/2015
 * Time: 1:13 PM
 */
class OrmUtilsDatabinder
{
    /**
     * @var OrmTable
     */
    private $mainTable;
    /**
     * @var stdClass[]
     */
    private $metadata;
    /**
     * @var mysqli_result
     */
    private $resultSet;

    /**
     * @var OrmTable[]
     */
    private $tables;

    /**
     * @var array keys - table name, value - metadata id key position
     */
    private $ids;

    /**
     * @var array(key - table name, value - array(key - object id, value - object )) 
     */
    private $objectsStack;
    
    public function __construct(OrmTable $mainTable, $resultSet, $metadata){
        $this->mainTable = $mainTable;
        $this->metadata = $metadata;
        $this->resultSet = $resultSet;
    }
    
    public function buildTables()
    {
        $this->tables = array($this->mainTable->getName() => $this->mainTable);
        $this->objectsStack = array($this->mainTable->getName() => array());
        foreach($this->mainTable->getBinds() as $bind) {
            if(!$bind->getLazyLoad()) {
                $rightTable = $bind->getRightTable();
                $tables[$rightTable->getName()] = $rightTable;
                $this->objectsStack[$rightTable->getName()] = array();
                unset($rightTable);
            }
        }

        $this->ids = array();
        $count = count($this->metadata);
        /** @var $table OrmTable */
        foreach($this->tables as $name => $table) {
            for($i = 0; $i < $count; $i++) {
                if($this->metadata[$i]->orgtable === $name && $table->getPrimaryKey() === $this->metadata[$i]->orgname) {
                    $this->ids[$name] = $i;
                    break;
                }
            }
        }
    }

    public function addDataToStack()
    {
        $count = count($this->metadata);
        while($row = $this->resultSet->fetch_array(MYSQLI_NUM)) {
            for($i = 0; $i < $count; $i++) {
                /** @var $workTable OrmTable*/
                $workTable = $this->tables[$this->metadata[$i]->orgtable];
                $currentObjectId = $row[$this->ids[$workTable->getName()]];
                /** @var $object OrmPersistenceBase */
                if(empty($this->objectsStack[$workTable->getName()][$currentObjectId])){
                    $class = $workTable->getPersistClassName();
                    $object = new $class();
                    $this->objectsStack[$workTable->getName()][$currentObjectId] = $object;
                    for($j = $i; $j < $count; $j++) {
                        if($this->metadata[$i]->orgtable !== $workTable->getName()) {
                            break;
                        }
                        $i = $j;
                        $fieldName = $this->metadata[$j]->orgname;
                        $fieldValue = $row[$j];
                        $prefix = "";
                        $field = $workTable->getField($fieldName);
                        foreach($workTable->getBinds() as $bind) {
                            if($bind->getLeftKey() == $field->getName() && $bind->getLeftField() == $field->getName()) {
                                $prefix = OrmUtils::BIND_PREFIX;
                                break;
                            }
                        }
                        $setter = "set".ucfirst($fieldName.$prefix);
                        $object->$setter(OrmUtils::getValueForField($field, $fieldValue, false));
                    }
                }
            }
        }
    }

    public function bindData()
    {
        foreach($this->tables as $table) {
            /* @var $bind OrmTableBind */
            foreach ($table->getBinds() as $bind) {
                if (empty($this->objectsStack[$bind->getRightTable()->getName()])) {
                    continue;
                }
                $postfix = $bind->getLeftField() === $table->getField($bind->getLeftKey())->getName() ? OrmUtils::BIND_PREFIX : "";
                $lKeyMethod = 'get' . ucfirst($bind->getLeftKey()).$postfix;
                $lKeySetter = 'set' . ucfirst($bind->getLeftField());
                $rKeyMethod = 'get' . ucfirst($bind->getRightKey());
                foreach ($this->objectsStack[$table->getName()] as $entity) {
                    $setValue = null;
                    /** @var $binded OrmPersistenceBase*/
                    foreach ($this->objectsStack[$bind->getRightTable()->getName()] as $binded) {
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
    }

    public function getResult($one)
    {
        if($one) {
            $out = reset($this->objectsStack[$this->mainTable->getName()]);
            return !empty($out)? $out : null;
        } else {
            return $this->objectsStack[$this->mainTable->getName()];
        }
    }
}