<?

class UI{
	protected $layout;
	const TAG_SELECT = 'select';

	const TAG_MULTYLANGUAGE = 'multylanguage';
	const TAG_INPUT = 'input';
	const TAG_TEXTAREA = 'textarea';
	const TAG_FIELDSET = 'fieldset';

	const INPUT_RADIO = 'radio';
	const INPUT_CHECKBOX = 'checkbox';
	const INPUT_CHECKBOX_GROUP = 'checkbox_group';

	const TEMPLATES_DIR = 'templates/';
	/**
	 * layout for formfield.
	 * Put all formfield elements in one container.
	 */
	const LAYOUT_BLOCK = "block";

	/**
	 * Layout for formfield
	 * render formfield as grid [top, center, bottom, left, right]
	 */
	const LAYOUT_GRID = "grid";
	/**
	 * Layout for formfield.
	 * render form forfield as row of table
	 */
	const LAYOUT_TABLE = "table";
	/**
	 * Layout for form.
	 * Render formfield as row of overview table
	 */
	const LAYOUT_OVERVIEW = "overview";


	const TYPE_TABLE = "table";
	const TYPE_LIST = "list";
	const TYPE_FORM = "form";
	const TYPE_FIELDSET = "fieldset";
	const CONFIG_NAME = "c";
	
	protected static $templateEngine;

	public function __construct(){
		$this->layout = '';
	}

	/**
	 * Get overview table json representation
	 * @param $data array|OrmPersistenceBase[]
	 * @param $headers array
	 * @param $hiddenFields array
	 * @return array
	 */
	public static function getOverviewTable($data, $headers, $hiddenFields)
	{
		$out = array(
			'type' => 'table',
			'headers' => $headers,
			'hiddenFields' => $hiddenFields,
			'data' => null
		);

		foreach($data as $row) {
			if(is_array($row)) {
				$object = $row;
			} else {
				$object = $row->toJson();
			}
			$thisData = array();
			foreach($object as $key => $value) {
				$thisData['fields'][$key] = $value;
			}
			if(count($thisData) > 0) {
				$out['data'][] = $thisData;
			} else {
				$out['data'] = array();
			}
		}
		return $out;
	}


	public function addVariable($key, $value){
		if($key == UI::CONFIG_NAME){
			throw new Exception("Illegal variable key: ".$key.". This key defined for config data.");
		}

		$engine = UI::getEngine();
		$engine->assign($key, $value);
	}
	public function removeVariable($key){
		$engine = UI::getEngine();
		$engine->clearAssign($key);
	}
	
	public function setLayout($layout){
		$this->layout = $layout;
	}
	public function getLayout(){
		return $this->layout;
	}

	/**
	 * Get Cms template engine.
	 * For now it's Smarty.
	 *
	 * @return Smarty
	 */
	public static function getEngine(){
		if(empty(UI::$templateEngine)){
			UI::initTemplateEngine();
		}
		return UI::$templateEngine;
	}
	
	public function process(){
		$layout = $this->getLayout();
		if(empty($layout)){
			return "";
		}
		/* @var $templateEngine Smarty */
		$templateEngine = UI::getEngine();
		$templateEngine->assign(UI::CONFIG_NAME, CoreConfig::getGeneralConfig());
		$out = $templateEngine->fetch($layout);
		$templateEngine->clearAllAssign();
		return $out;
	}
	
	protected static function initTemplateEngine(){
		require_once Core::MODULES_FOLDER.'ui/templateEngine/Smarty.class.php';
		define('SMARTY_SPL_AUTOLOAD',1);

		$smarty = new Smarty();

		//$smarty->force_compile = true;
		$smarty->debugging = Core::DEVELOPMENT;
		$smarty->setTemplateDir('./'.UI::TEMPLATES_DIR)
			->setCompileDir('./cache/smarty/templates_c')
			->setCacheDir('./cache/smarty/cache')
			->setConfigDir('./templateEngine/config');

		UI::$templateEngine = $smarty;
	}

	/**
	 * Process persistance table and return array with fields description
	 * for this table
	 * @param OrmTable $table object to parse
	 * @param $data array|OrmPersistenceBase (data of current row)
	 * @param $layout - string, see LAYOUT_* constants for more info
	 * @return array
	 */
	public static function getFormForTable(OrmTable $table, $data, $layout){
		$out = array();
		/* @var $field OrmTableField*/
		foreach($table->getFields() as $field){
			$formfield = new UiFormfield();
			$formfield->useField($field, $table->getName());
			if(is_array($data) && isset($data[$field->getName()])){
				$formfield->setValue($data[$field->getName()]);
			}elseif(is_object($data)) {
				$method = "get".ucfirst($field->getName());
				if(method_exists($data, $method)){
					$formfield->setValue($data->$method());
				}
			}
			if($field->getPrimary()){
				$formfield->setAttribute('type', 'hidden');
			}
			$formfield->setLayout($layout);
			$out['fields'][$field->getName()] = $formfield->toJSON();
			if($field->getType() == 'boolean' || $field->getType() == 'bit') {
				$value = $formfield->getValue();
				if(!empty($value)) {
					$out['fields'][$field->getName()]['attributes']['checked'] = 1;
				}
				$out['fields'][$field->getName()]['attributes']['type'] = 'checkbox';
			}
		}
		$out['type'] = 'form';
		$out['method'] = 'POST';
		$out['id'] = 'form_'.$table->getName();
		return $out;
	}

	/**
	 * Return Formfield's array for submit button
	 * @return array
	 */
	public static function getSubmitButton(){
		$out = new UiFormfield();
		$out->setValue(Word::get('common','submit'));
		$out->setTag(UI::TAG_INPUT);
		$out->setAttribute('type', 'submit');
		$out->setLayout(UI::LAYOUT_GRID);
		return $out->toJSON();
	}

	public static function usePersistObject($object, OrmTable $table, $layout){
		$out = array();
		foreach($table->getFields() as $name => $field){
			$getter = "get".ucfirst($name);
			$formfield = new UiFormfield();
			$formfield->useField($field, $table->getName());
			$formfield->setValue($object->$getter());
			$formfield->setLayout($layout);
			$out[$name] = $formfield->toJSON();
		}
		return $out;
	}


}