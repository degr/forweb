<?
class ORM_Table{

	/**
	 * @var ORM_Table_Field[]
	 */
	protected $fields;
	protected $name;
	protected $shortName;
	/**
	 * @var ORM_Table_Bind[]
	 */
	protected $binds;
	protected $persistClassName;
	
	protected $primaryKey;
	
	const ONE_TO_ONE = 'oneToOne';
	const ONE_TO_MANY = 'oneToMany';
	const MANY_TO_ONE = 'manyToOne';
	const MANY_TO_MANY = 'manyToMany';
	const PERSIST_FOLDER = 'Persistent/';
	const PERSIST_CLASS_PREFIX = 'Persist';
	
	public function __construct($name){
		$this->name = $name;
	}
	
	public function getName(){
		return $this->name;
	}

	/**
	 * get this table binds array
	 * @return ORM_Table_Bind[]
	 */
	public function getBinds(){
		if(count($this->binds) > 0){
			return $this->binds;
		}
		return array();
	}

	/**
	 * @param $name
	 * @return ORM_Table_Bind
	 * @throws Exception
	 */
	public function getBind($name){
		if(!empty($this->binds[$name])) {
			return $this->binds[$name];
		} else {
			throw new Exception("Undefined bind name $name");
		}
	}
	public function addField(ORM_Table_Field $field){
		$primaryKey = $this->getPrimaryKey();
		if($field->getPrimary() && !empty($primaryKey)){
			throw new Exception("Declaring table with two or more primary key denied. We are sorry...");
		}elseif($field->getPrimary()){
			$this->primaryKey = $field->getName();
		}
		$this->fields[$field->getName()] = $field;
	}
	
	public function getPrimaryKey(){
		return $this->primaryKey;
	}

	/**
	 * @param $name
	 * @return ORM_Table_Field
	 * @throws Exception
	 */
	public function getField($name){
		if(!empty($this->fields[$name])){
			return $this->fields[$name];
		} else {
			throw new Exception("can't get field with name: $name!");
		}
	}

	/**
	 * @return ORM_Table_Field[]
	 */
	public function getFields(){
		return $this->fields;
	}

	/**
	 * @param $thisKey
	 * @param $thatKey
	 * @param string $thatTable
	 * @param String $type
	 * @param bool $twoSides
	 * @param $lazy
	 * @return ORM_Table_Bind
	 * @throws Exception
	 */
	public function bindTable($thisKey, $thatKey, $thatTable, $type, $twoSides, $lazy){
		if(empty($thisKey) || empty($thatKey) || empty($thatTable) || empty($type)) {
			throw new Exception("Invalid parameters was passed in bind method.");
		}
		if(!empty($this->binds[$thatTable])){
			if($this->binds[$thatTable]->getType() != $type) {
				throw new Exception("Table ".$thatTable." was binded with another bind type. Old type - "
					.$this->binds[$thatTable]->getType().", new type - ".$type);
			} else {
				
			}
		}
		$this->binds[$thatTable] = new ORM_Table_Bind($this->getName(), $thisKey, $type, $thatTable, $thatKey, $lazy);
		$out = $this->binds[$thatTable];
		if(!$twoSides){
			return $out;
		}
		switch($type) {
			case ORM_Table::ONE_TO_ONE:
			case ORM_Table::MANY_TO_MANY:
				$newType = $type;
				break;
			case ORM_Table::ONE_TO_MANY:
				$newType = ORM_Table::MANY_TO_ONE;
				break;
			case ORM_Table::MANY_TO_ONE:
				$newType = ORM_Table::ONE_TO_MANY;
				break;
			default:
				throw new FwException("Invalid bind type - ".$type);
		}
		ORM::getTable($thatTable)->bindTable($thatKey, $thisKey, $this->getName(), $newType, false, $lazy);
		return $out;
	}
	
	public function isBinded($name){
		return !empty($this->binds[$name]);
	}

	/**
	 * @return string
	 */
	public function getPersistClassName(){
		if($this->persistClassName === null) {
			$this->persistClassName = ORM_Table::PERSIST_CLASS_PREFIX.ucfirst($this->getName());
			if(is_file(ORM::getPersistExtendedObjectsFolder().ORM::EXTEND.$this->persistClassName.".php")){
				$this->persistClassName = ORM::EXTEND.$this->persistClassName;
			}
		}

		return $this->persistClassName;
	}

	/**
	 * @param $name string
	 * @return $this
	 */
	public function setPersistClassName($name){
		$this->persistClassName = $name;
		return $this;
	}
	
	
	public function getFieldsToArray(){
		$out = array();
		foreach($this->getFields() as $field){
			$row['field'] = $field->getName()." (".$field->getType()." ".$field->getLength().")";
			$row['canBeNull'] = $field->getCanBeNull() ? "true" : "false";
			$row['properties'] = ($field->getAutoIncrement() ? "autoincrement, " : "")." ".($field->getUnique() ? "unique, " : "");
			$out[] = $row;
		}
		return $out;
	}
}