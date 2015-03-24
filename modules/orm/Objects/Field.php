<?
class ORM_Objects_Field{
	protected $name;
	protected $type;
	protected $canBeNull;
	protected $autoIncrement;
	protected $unique;
	protected $primary;
	protected $defaultValue;
	protected $length;
	protected $enumValues;

	protected $lazyLoad;
	protected $index;

	public function __construct($name, $type){
		$this->name = $name;
		$this->type = $type;
		
		$this->canBeNull = false;
		$this->autoIncrement = false;
		$this->unique = false;
		$this->primary = false;
		$this->defaultValue = "";
		$this->length = 0;
	}

	public function setIndex($index){
		$this->index = $index;
		return $this;
	}
	public function getIndex(){
		return $this->index;
	}

	public function setEnumValues($values){
		if(!is_array($values)) {
			var_dump($values);
			throw new Exception('enum values must be array instance');
		}
		$this->enumValues = $values;
	}
	
	public function getEnumValues(){
		return $this->enumValues;
	}
	
	public function setCanBeNull(){
		$this->canBeNull = true;
		return $this;
	}
	public function getCanBeNull(){
		return $this->canBeNull;
	}
	
	public function setAutoIncrement(){
		$this->autoIncrement = true;
		return $this;
	}
	public function getAutoIncrement(){
		return $this->autoIncrement;
	}
	
	public function setUnique(){
		$this->unique = true;
		return $this;
	}
	public function getUnique(){
		return $this->unique;
	}
	
	public function setPrimary(){
		$this->primary = true;
		return $this;
	}
	public function getPrimary(){
		return $this->primary;
	}
	public function setDefaultValue($defaultValue){
		$this->defaultValue = $defaultValue;
		return $this;
	}
	public function getDefaultValue(){
		return $this->defaultValue;
	}
	public function setLength($length){
		$this->length = $length;
		return $this;
	}
	public function getLength(){
		return $this->length;
	}
	
	
	public function getName(){
		return $this->name;
	}
	public function getType(){
		return $this->type;
	}


	public function setLazyLoad($lazyLoad){
		$this->lazyLoad = $lazyLoad;
		return $this;
	}

	/**
	 * internal function for tables data binding
	 * @return boolean
	 */
	public function getLazyLoad(){
		return $this->lazyLoad;
	}

	public function validateValue($value, &$errors)
	{
		//@TODO validate
		return true;
	}
}