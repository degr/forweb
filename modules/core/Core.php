<?
class Core extends Module{

	const INIT_REQUEST = "init";
	const RESOURCES_FOLDER = "resources/";
	const MODULES_FOLDER = "modules/";
	const DEVELOPMENT = true;
	const MULTIPLE_LANGUAGES = true;
	const SYS_INCLUDES = 'sys_includes';

	public static $FORBIDDEN_URLS = array(
		'api', 'ajax'
	);

	public function getAjaxHandlers()
	{
		if($this->ajaxHandlers == null) {
			$this->ajaxHandlers['getAjaxConfig'] = new AjaxHandler("getAjaxConfig", 'json');
			$this->ajaxHandlers['saveConfig'] = new AjaxHandler("saveConfig", 'text');
			$this->ajaxHandlers['deleteConfig'] = new AjaxHandler("deleteConfig", 'text');
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
		$file = "modules/".strtolower($module)."/".$module.".php";
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
		$dispatcher = new Page_Dispatcher($_SERVER['REQUEST_URI']);
		$dispatcher->handleRequest();
		if($dispatcher->getParam(0) == 'api') {
			/** @var $api Api */
			$api = Core::getModule("Api");
			$api->handleRequest($dispatcher);
			return;
		}
		$this->pageModule = Core::getModule("Page");

		if($dispatcher->getParam(0) == 'ajax'){
			Cms::ajaxHandler($dispatcher->getParam(1), $dispatcher->getParam(2));
			//unreachable 'return' statement. Exist only as end function marker.
			// Script will exit in Cms::ajaxHandler;
			return;
		}

		self::$pathParams = $dispatcher->getParams();
		/* @var $pageService Page_Service */
		$pageService = $this->pageModule->getService();
		$page = $pageService->findPage(self::$pathParams);
		$pageService->setCurrentPage($page);
		$template = $pageService->getTemplate();
		$blocks = $this->getBlocks($template->getId());
		$pageData = $this->processBlocks($blocks);

		$this->sendResponse($pageData, $template);

		if(isset($_GET['force_admin_panel'])) {
			$ui = new UI();
			/** @var $cms Cms */
			$cms =Core::getModule("Cms");
			$cms->getAdminPanel($ui);
			echo $ui->process();
		}
	}
	
	public function sendResponse($pageData, PersistTemplates $template){
		$ui = new UI();
		$ui->addVariable("block", $pageData);
		$ui->setLayout($template->getTemplate());
		header('Content-Type: text/html; charset=utf-8');
		echo $ui->process();
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
		$customFilter = new ORM_Query_CustomFilter($customFilterQuery, true);
		return ORM::load("includes", false,	$customFilter, null, null);
	}

	protected function processBlocks($blocks){
		/* @var $pageService Page_Service */
		$pageService = $this->pageModule->getService();
		$includes = $this->getPageIncludes($pageService->getCurrentPage());
		$out = array();
		$data = array();
		if(!empty($includes)) {
			/* @var $include PersistIncludes */
			foreach ($includes as $include) {
				if (empty($data[$blocks[$include->getBlock()]])) {
					$data[$blocks[$include->getBlock()]] = array(
						"before" => array(),
						"template" => array(),
						"after" => array()
					);
				}
				if(Core::DEVELOPMENT && isset($_GET['dbug_functions']) && Core::isModuleExist("Debug")){
					$time = time();
				}
				if(!empty($data[$blocks[$include->getBlock()]][$include->getPosition()][$include->getPositionNumber()])) {
					$include->setPositionNumber($include->getPositionNumber() +100);
				}
				$data[$blocks[$include->getBlock()]][$include->getPosition()][$include->getPositionNumber()] =
					$this->processInclude($include);
				if(Core::DEVELOPMENT && isset($_GET['dbug_functions']) && Core::isModuleExist("Debug")){
					$data[$blocks[$include->getBlock()]][$include->getPosition()][$include->getPositionNumber()]
						= Debug::getSIncludeExecutionTime($include, $time);
				}
			}
		}
		//iterate over each block
		foreach($data as $key => $block) {
			if(empty($out[$key])) {
				$out[$key] = "";
			}
			//iterate over positions: before, template, after
			foreach($block as $positionedIncludes){
				//iterate over includes
				$keys = (array_keys($positionedIncludes));
				sort($keys);
				foreach($keys as $incKey) {
					$out[$key] .= $positionedIncludes[$incKey];
				}
			}
		}
		$out['scriptCollector'] = ScriptCollector::get();
		return $out;
	}


	protected function processInclude(PersistIncludes $include){
		if(Core::DEVELOPMENT && isset($_GET['dbug_functions']) && Core::isModuleExist("Debug")) {
			return Debug::getIncludeInformation($include);
		}
		/*if(Core::DEVELOPMENT && isset($_GET['dbug_time']) && Core::isModuleExist("Debug")){
			$time = time();
		}*/
		switch($include->getType()){
			case "text":
				$out = "\t<p>".htmlspecialchars(Core::getIncludeStaticContent($include))."</p>";
				break;
			case "html":
				$out = Core::getIncludeStaticContent($include)."\n";
				break;
			case "image":
				$out = '<div class="image-holder image-holder-'.$include->getId().'">'
				.'<img src="'.$include->getContent().'" alt="content_image" title="content_image" />'
				.'</div>';
				break;
			case "executable":
				$module = $include->getModule();
				$function = $include->getMethod();
				if(empty($module) || empty($function)) {
					$out = "";
				} else {
					$object = Core::getModule($module);
					$ui = new UI();
					$object->$function($ui);
					$out = $ui->process();
				}
				break;
			default:
				throw new Exception("Unknown include type ".$include->getType());

		}

		/*if(Core::DEVELOPMENT && isset($_GET['dbug_time']) && Core::isModuleExist("Debug")){
			$out = Debug::getIncludeExecutionTime($include, $time);
		}*/

		return $out;
	}


	private function getIncludeStaticContent(PersistIncludes $include){
		if(Core::MULTIPLE_LANGUAGES) {
			return Word::get(Core::SYS_INCLUDES, $include->getId());
		} else {
			return $include->getContent();
		}
	}


	public function getAdminScript(){
		$adminScript = Core::getResource("admin.js");
		$url = Config::get('url');
		return str_replace('###url###', $url, $adminScript);
	}
	
	public static function getResource($name){
		return file_get_contents(Core::RESOURCES_FOLDER.$name);
	}
	
	public static function showModules(){
		foreach(Core::$modules as $key => $module){
			echo $key ." <br/>";
		}
	}

	public function getAjaxConfig(){
		Access::denied("can_edit_config");
		$provider = new Core_Config_Config();
		return $provider->getAjaxConfig();
	}
	/**
	 * Ajax handler
	 */
	public function saveConfig(){
		Access::denied("can_edit_config");
		$provider = new Core_Config_Config();
		return $provider->saveConfig();
	}
	public function deleteConfig(){
		Access::denied("can_edit_config");
		$provider = new Core_Config_Config();
		return $provider->deleteConfig();
	}
	public static function triggerEvent($eventName, $params){
		$files = glob(self::MODULES_FOLDER.'*');
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
	 * @return EventHandler[]
	 */
	public function getEventHandlers()
	{
		// TODO: Implement getEventHandlers() method.
	}
}