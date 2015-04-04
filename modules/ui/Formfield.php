<?
class UI_Formfield{

	protected $tag;
	public function getTag(){return $this->tag;}
	public function setTag($tag){$this->tag = $tag;}
	protected $name;
	public function getName(){return $this->name;}
	public function setName($name){$this->name = $name;}
	protected $value;
	public function getValue(){return $this->value;}
	public function setValue($value){$this->value = $value;}
	protected $options;
	public function getOptions(){return $this->options;}
	public function setOptions(array $options){$this->options = $options;}
	public function setOption($key, $option){$this->options[$key] = $option;}
	
	protected $attributes;
	public function getAttributes(){return $this->attributes;}
	public function setAttributes(array $attributes){$this->attributes = $attributes;}
	public function setAttribute($key, $attribute){$this->attributes[$key] = $attribute;}
	
	protected $class;
	public function getClass(){return $this->class;}
	public function setClass($class){$this->class = $class;}
	protected $id;
	public function getId(){return $this->id;}
	public function setId($id){$this->id = $id;}
	protected $title;
	public function getTitle(){return $this->title;}
	public function setTitle($title){$this->title = $title;}
	protected $description;
	public function getDescription(){return $this->description;}
	public function setDescription($description){$this->description = $description;}
	protected $error;
	public function getError(){return $this->error;}
	public function setError($error){$this->error = $error;}
	protected $validation;
	public function getValidation(){return $this->validation;}
	public function setValidation($validation){$this->validation = $validation;}
	protected $layout;
	public function getLayout(){return $this->layout;}
	public function setLayout($layout){$this->layout = $layout;}

	public function useField(ORM_Table_Field $field, $tableName){
		$tag = $this->defineType($field->getType());
		$this->setTag($tag);
		if($tag == UI::TAG_SELECT){
			$options = array_combine($field->getEnumValues(), $field->getEnumValues());
			$this->setOptions($options);
			$this->setValue(reset($options));
		}elseif($tag == UI::TAG_INPUT){
			$this->setAttribute('type', 'text');
		}
		
		$this->setId(ORM_Table::PERSIST_CLASS_PREFIX."_".$tableName."_".$field->getName());
		$this->setTitle($field->getName());
		$this->setName($field->getName());
		//$this->defineValidation($field);
	}



	protected function defineType($type){
		switch($type){
			case "enum":
				return UI::TAG_SELECT;
			case "text":
				return UI::TAG_TEXTAREA;
			default:
				return UI::TAG_INPUT;
		}
	}
	
	public function toJSON(){
		return get_object_vars($this);
	}
}