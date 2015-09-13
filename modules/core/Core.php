<?
class Core extends Module{

	const INIT_REQUEST = "init";
	const RESOURCES_FOLDER = "resources/";
	const DEVELOPMENT = true;
	const MULTIPLE_LANGUAGES = true;
	const LANGUAGE_IN_URL = true;
	const SYS_INCLUDES = 'sys_includes';
	const MODULES_FOLDER = "modules/";

	public static $FORBIDDEN_URLS = array(
		'api', 'ajax', 'sitemap.xml'
	);

	public function getAjaxHandlers()
	{
		if($this->ajaxHandlers == null) {
			$this->ajaxHandlers['getAjaxConfig'] = new ModuleAjaxHandler("getAjaxConfig", 'json');
			$this->ajaxHandlers['saveConfig'] = new ModuleAjaxHandler("saveConfig", 'text');
			$this->ajaxHandlers['deleteConfig'] = new ModuleAjaxHandler("deleteConfig", 'text');
		}
		return $this->ajaxHandlers;
	}

	/**
	 * get core instance
	 * @return Core
	 */
	public static function getInstance(){
		if(Core::$instance == null) {
			Core::$instance = new Core();
		}
		return Core::$instance;
	}


	/**
	 * Array with modules
	 * @var IModule[]
	 */
	protected static $modules;

	/**
	 * Page module
	 * @var Page
	 */
	protected $pageModule;

	protected static $pathParams;

	public static function getPathParam($num){
		if(isset(self::$pathParams[$num])) {
			return self::$pathParams[$num];
		}
		return "";
	}
	/**
	 * Core instance.
	 * Core class must have singleton model. Do not create core objects,
	 * use Core::getInstance method.
	 * @var Core
	 */
	protected static $instance;


	/**
	 * Get module. If module is not defined, try to find it in path:
	 * 'modules/yourModule/YourModule.php'
	 * @param string $module
	 * @return Module
	 * @throws Exception
	 */
	public static function getModule($module){
		if(empty(Core::$modules[$module])) {
			Core::loadModule($module);
		}
		return Core::$modules[$module];
	}

	/**
	 * Create new module instance, and put into modules storage.
	 * 'modules/yourModule/YourModule.php'
	 * @param string $module
	 * @return Module
	 * @throws Exception
	 */
	protected static function loadModule($module){
		Core::$modules[$module] = new $module();
	}

	/**
	 * Check is module exists in memory. If module not exist, try to find it in path:
	 * 'modules/yourModule/YourModule.php'. Does not load module to memory.
	 * @param string $module
	 * @return boolean
	 */
	public static function isModuleExist($module){
		if(!empty(Core::$modules[$module])){
			return true;
		}
		$file = Core::MODULES_FOLDER."/".strtolower($module)."/".$module.".php";
		return is_file($file);
	}


	/**
	 * Save any object into modules array. Use it carefully,
	 * method was created to store Module instances.
	 * @param $module IModule
	 * @param $key string
	 */
	protected function setModule(IModule $module, $key){
		Core::$modules[$key] = $module;
	}

	/**
	 * Main core method,
	 * @return void
	 */
	public function process(){
		$dispatcher = new PageDispatcher($_SERVER['REQUEST_URI']);
		$dispatcher->handleRequest();
		self::$pathParams = $dispatcher->getParams();
		if(Core::MULTIPLE_LANGUAGES && Core::LANGUAGE_IN_URL) {
			$languageUrl = array_shift(self::$pathParams);
		} else {
            $languageUrl = null;
        }
		$key = Core::getPathParam(0);
		if($key == 'api') {
			/** @var $api Api */
			$api = Core::getModule("Api");
			$api->handleRequest();
			return;
		}
		if($key == 'ajax'){
			Cms::ajaxHandler(Core::getPathParam(0), Core::getPathParam(1));
			//unreachable 'return' statement. Exist only as end function marker.
			// Script will exit in Cms::ajaxHandler;
			return;
		}
		if($key === 'sitemap.xml') {
			Cms::onSitemapDisplay();
		}
		if(Core::MULTIPLE_LANGUAGES && Core::LANGUAGE_IN_URL) {
			Word::onLanguageUrl($languageUrl);
		}
		$provider = new CorePageContent();
		$provider->onPageContent(self::$pathParams);
	}


	/**
	 * @param integer $id
	 * @return array assoc
	 */
	public function getBlocks($id){
		return DB::getAssoc("select id, name FROM blocks where template = '".$id."' order by position ASC", "id", "name");
	}

	public function getPageIncludes(PersistPages $page){
		$customFilterQuery = " (page='".$page->getId()."')"
			." OR (template = '".$page->getTemplate()->getId()."')";
		$customFilter = new OrmQueryCustomFilter($customFilterQuery, true);
		return ORM::load("includes", false,	$customFilter, null, null);
	}




	public static function getIncludeStaticContent(PersistIncludes $include){
		if(Core::MULTIPLE_LANGUAGES) {
			return Word::get(Core::SYS_INCLUDES, $include->getId());
		} else {
			return $include->getContent();
		}
	}


	public function getAdminScript(){
		$adminScript = Core::getResource("admin.js");
		$url = CoreConfig::getUrl();
		return str_replace('###url###', $url, $adminScript);
	}

	public static function getResource($name){
		return file_get_contents(Core::RESOURCES_FOLDER.$name);
	}

	public function getAjaxConfig(){
		Access::denied("can_edit_config");
		$provider = new CoreConfigHandler();
		return $provider->getAjaxConfig();
	}
	/**
	 * Ajax handler
	 */
	public function saveConfig(){
		Access::denied("can_edit_config");
		$provider = new CoreConfigHandler();
		return $provider->saveConfig();
	}
	public function deleteConfig(){
		Access::denied("can_edit_config");
		$provider = new CoreConfigHandler();
		return $provider->deleteConfig();
	}
	public static function triggerEvent($eventName, $params){
		$files = glob(Core::MODULES_FOLDER.'*');
		$forbidden = array('Module', 'Fwexception');
		foreach($files as $file) {
			if(!is_dir($file)) {
				continue;
			}
			$className = ucfirst(basename($file));
			if(in_array($className, $forbidden)){
				continue;
			}
			$module = Core::getModule($className);
			if(is_subclass_of($module, 'IModule') && method_exists($module, 'getEventHandlers')) {
				$handlers = $module->getEventHandlers();
				if(empty($handlers)){
					continue;
				}
				foreach($handlers as $handler) {
					if($handler->getEvent() === $eventName) {
						$method = $handler->getMethod();
						$module->$method($params);
					}
				}
			}
		}
	}

	/**
	 * Get module event handlers
	 * @return ModuleEventHandler[]
	 */
	public function getEventHandlers()
	{
	}

	public function autoload($class)
	{
		$file = $class.'.php';
		if(is_file(Core::MODULES_FOLDER.strtolower($class).'/'.$file)) {
			require_once Core::MODULES_FOLDER.strtolower($class).'/'.$file;
			return;
		}
		if(is_file(Core::MODULES_FOLDER.$file)) {
			require_once Core::MODULES_FOLDER.$file;
			return;
		}
		$parts = preg_split("/(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])|([_]{1,})/", $class);

		$folder = Core::MODULES_FOLDER;
		foreach($parts as $part) {
			$folder .= $part;
			if(!is_dir($folder)) {
				continue;
			} else {
				$folder .= '/';
			}
			$fullPath = $folder.$file;
			if(is_file($fullPath)) {
				require_once $fullPath;
				return;
			}
		}
	}
}