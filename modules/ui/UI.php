<?
require_once 'modules/Config.php';

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

	const TEMPLATES_DIR = 'templates';
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
	 * @param $data array|ORM_Persistence_Base[]
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
				$object = $row->toArray();
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


	/**
	 * this weird function save form errors on post, and
	 * @see FormHandler::getErrors
	 * @param $moduleName
	 * @param $handlerName
	 * @param $errors array
	 */
	public static function setFormErrors($moduleName, $handlerName, $errors)
	{
		$_SESSION[Core::FORM_ERRORS][$moduleName][$handlerName] = $errors;
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
	 * Get CMS template engine.
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
		$templateEngine->assign(UI::CONFIG_NAME, Config::getGeneralConfig());
		$out = $templateEngine->fetch($layout);
		$templateEngine->clearAllAssign();
		return $out;
	}
	
	protected static function initTemplateEngine(){
		require_once 'templateEngine/libs/Smarty.class.php';
		define('SMARTY_SPL_AUTOLOAD',1);
		spl_autoload_register('__autoload');

		$smarty = new Smarty();

		//$smarty->force_compile = true;
		$smarty->debugging = true;
		$smarty->caching = true;
		$smarty->cache_lifetime = 120;
		$smarty->setTemplateDir('./'.UI::TEMPLATES_DIR)
			->setCompileDir('./Cache/smarty/templates_c')
			->setCacheDir('./Cache/smarty/cache')
			->setConfigDir('./templateEngine/config');
		$smarty->caching = false;

		UI::$templateEngine = $smarty;
	}

	/**
	 * Process persistance table and return array with fields description
	 * for this table
	 * @param ORM_Objects_Table $table object to parse
	 * @param $data array|ORM_Persistence_Base (data of current row)
	 * @param $layout - string, see LAYOUT_* constants for more info
	 * @return array
	 */
	public static function getFormForTable(ORM_Objects_Table $table, $data, $layout){
		$out = array();
		/* @var $field ORM_Objects_Field*/
		foreach($table->getFields() as $field){
			$formfield = new UI_Formfield();
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
		$out = new UI_Formfield();
		$out->setValue(Word::get('common','submit'));
		$out->setTag(UI::TAG_INPUT);
		$out->setAttribute('type', 'submit');
		$out->setLayout(UI::LAYOUT_GRID);
		return $out->toJSON();
	}

	public static function usePersistObject($object, ORM_Objects_Table $table, $layout){
		$out = array();
		foreach($table->getFields() as $name => $field){
			$getter = "get".ucfirst($name);
			$formfield = new UI_Formfield();
			$formfield->useField($field, $table->getName());
			$formfield->setValue($object->$getter());
			$formfield->setLayout($layout);
			$out[$name] = $formfield->toJSON();
		}
		return $out;
	}


}