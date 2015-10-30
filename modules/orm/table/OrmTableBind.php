<?
class OrmTableBind{

	protected $leftTable;
	protected $leftKey;
	
	protected $rightTable;
	protected $rightKey;
	
	protected $type;

	protected $customLeftField;
	protected $customRightField;

	protected $lazyLoad;
	
	protected $manyToManyBindingName;

	
	public function getType(){return $this->type;}
	public function __construct($leftTable, $leftKey, $type, $rightTable, $rightKey, $lazyLoad){
		if(gettype($leftTable) != 'string'){
			throw new FwException("Object was passed as left table, not table name");
		}
		if(gettype($rightTable) != 'string'){
			throw new FwException("Object was passed as right table, not table name");
		}
		$this->leftTable = $leftTable;
		$this->leftKey = $leftKey;
		$this->rightTable = $rightTable;
		$this->rightKey = $rightKey;
		
		$this->type = $type;
		$this->lazyLoad = $lazyLoad;

		$this->customLeftField = null;
		$this->customRightField = null;
	}

	/**
	 * Check for lazy load
	 * @return boolean
	 */
	public function getLazyLoad(){
		return $this->lazyLoad;
	}
	
	public function loadBindedData($id){
		$customFilter = new OrmQueryCustomFilter(
			$this->getRightTable()->getName().".".$this->getRightField()." = '".DB::escape($id)."'",
			true
		);
		if($this->type === OrmTable::ONE_TO_ONE || OrmTable::MANY_TO_ONE === $this->type) {
			$one = true;
		} elseif($this->type === OrmTable::ONE_TO_MANY || OrmTable::MANY_TO_MANY === $this->type) {
			$one = false;
		} else{
			throw new Exception('undefined bind type: '.$this->type);
		}
		
		$result = ORM::load($this->getRightTable()->getName(), $one, $customFilter, null, null);
		return $result;
	}

	/**
	 * Get bind right table
	 * @return OrmTable
	 */
	public function getRightTable(){
		return ORM::getTable($this->rightTable);
	}

	/**
	 * Get bind left table
	 * @return OrmTable
	 */
	public function getLeftTable(){
		return ORM::getTable($this->leftTable);
	}

	/**
	 * Get bind right key
	 * @return string
	 */
	public function getRightKey(){
		return $this->rightKey;
	}

	/**
	 * Get bind left key
	 * @return string
	 */
	public function getLeftKey(){
		return $this->leftKey;
	}

	/**
	 * get class field name for binded table.
	 * Warning! Method can return custom field name, if it defined.
	 * @return string
	 */
	public function getLeftField()
	{
		if ($this->customLeftField != null) {
			return $this->customLeftField;
		} else {
			return $this->leftKey;
		}
	}

	public function setCustomLeftField($customLeftField, $otherSide = true)
	{
		$this->customLeftField = $customLeftField;
		if ($otherSide) {
			//$this->applyCustomNameForOtherBind($customLeftField, false);
		}
	}

	public function getRightField()
	{
		if ($this->customRightField != null) {
			return $this->customRightField;
		} else {
			return $this->rightKey;
		}
	}

	public function setCustomRightField($customRightField, $otherSide = true){
		$this->customRightField = $customRightField;
		if($otherSide) {
			//$this->applyCustomNameForOtherBind($customRightField, true);
		}
	}

	protected function applyCustomNameForOtherBind($name, $left){
		$t1 = $left ? $this->getLeftTable() : $this->getRightTable();
		$t2 = !$left ? $this->getLeftTable() : $this->getRightTable();

		$bind = $t1->getBind($t2->getName());
		if($left) {
			$bind->setCustomLeftField($name, false);
		} else {
			$bind->setCustomRightField($name, false);
		}
	}

	/**
	 * @return null
	 */
	public function getCustomLeftField() {
		return $this->customLeftField;
	}

	/**
	 * @return null
	 */
	public function getCustomRightField() {
		return $this->customRightField;
	}

	/**
	 * @return string
	 */
	public function getManyToManyBindingName() {
		return $this->manyToManyBindingName;
	}
	/**
	 * @param string
	 */
	public function setManyToManyBindingName($manyToManyBindingName) {
		if($this->manyToManyBindingName === $manyToManyBindingName) {
			return;
		}
		$this->manyToManyBindingName = $manyToManyBindingName;
		$assymetricBind = $this->getRightTable()->getBind($this->getLeftTable()->getName());
		$assymetricBind->setManyToManyBindingName($manyToManyBindingName);
	}
}