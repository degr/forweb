<?
class ORM_Objects_Bind{

	protected $leftTable;
	protected $leftKey;
	
	protected $rightTable;
	protected $rightKey;
	
	protected $type;

	protected $customLeftField;
	protected $customRightField;

	protected $lazyLoad;
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
		$tail = " WHERE ".$this->getRightTable()->getName().".".$this->getRightField()." = '".DB::escape($id)."'";
		if($this->type === ORM_Objects_Table::ONE_TO_ONE || ORM_Objects_Table::MANY_TO_ONE === $this->type) {
			$one = true;
		} elseif($this->type === ORM_Objects_Table::ONE_TO_MANY || ORM_Objects_Table::MANY_TO_MANY === $this->type) {
			$one = false;
		} else{
			throw new Exception('undefined bind type: '.$this->type);
		}
		
		$result = ORM::load($this->getRightTable()->getName(), $tail, false, $one);
		return $result;
	}

	/**
	 * Get bind right table
	 * @return ORM_Objects_Table
	 */
	public function getRightTable(){
		return ORM::getTable($this->rightTable);
	}

	/**
	 * Get bind left table
	 * @return ORM_Objects_Table
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
		debug(array($this->getLeftTable()->getName(), $this->getType(), $otherSide, $customLeftField));
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

	public function setCustomRightField($customRightField){
		$this->customRightField = $customRightField;
		//$this->applyCustomNameForOtherBind($customRightField, true);
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
}