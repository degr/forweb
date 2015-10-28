<?
class Page extends Module{

	const PAGE_TITLE_PREFIX = "title_";

	public static function getIncludeTypesList()
	{
		return array(
			'html'=>Word::get('common', 'include_type_html'),
			'text'=>Word::get('common', 'include_type_text'),
			'image'=>Word::get('common', 'include_type_image'),
			'executable'=>Word::get('common', 'include_type_executable')
		);
	}
	public function getAjaxHandlers()
	{
		if($this->ajaxHandlers == null) {
			$this->setHandler("editPage", new ModuleAjaxHandler("editPage", ModuleAjaxHandler::JSON));
			$this->setHandler("changePagePositions", new ModuleAjaxHandler("changePagePositions", ModuleAjaxHandler::JSON));
			$this->setHandler("showPagesTree", new ModuleAjaxHandler("getPagesList", ModuleAjaxHandler::JSON));
			$this->setHandler("editTemplateForm", new ModuleAjaxHandler("editTemplateForm", ModuleAjaxHandler::JSON));
			$this->setHandler("processPageContent", new ModuleAjaxHandler("processPageContent", ModuleAjaxHandler::JSON));
			$this->setHandler("pageContent", new ModuleAjaxHandler("pageContent", ModuleAjaxHandler::JSON));
			$this->setHandler("deletePageInclude", new ModuleAjaxHandler("deletePageInclude", ModuleAjaxHandler::JSON));
			$this->setHandler("getMethodsList", new ModuleAjaxHandler("getMethodsList", ModuleAjaxHandler::JSON));
			$this->setHandler("getModulesList", new ModuleAjaxHandler("getModulesList", ModuleAjaxHandler::JSON));
			$this->setHandler("getIncludeTextForm", new ModuleAjaxHandler("getIncludeTextForm", ModuleAjaxHandler::JSON));

			$this->setHandler("createTemplate", new ModuleAjaxHandler("createTemplate", ModuleAjaxHandler::JSON));
			$this->setHandler("editTemplate", new ModuleAjaxHandler("editTemplate", ModuleAjaxHandler::JSON));
			$this->setHandler("deleteTemplate", new ModuleAjaxHandler("deleteTemplate", ModuleAjaxHandler::TEXT));

			$this->setHandler("deleteTemplateForm", new ModuleAjaxHandler("deleteTemplateForm", ModuleAjaxHandler::JSON));
			$this->setHandler("createBlock", new ModuleAjaxHandler("createBlock", ModuleAjaxHandler::JSON));
			$this->setHandler("deleteBlock", new ModuleAjaxHandler("deleteBlock", ModuleAjaxHandler::TEXT));
			$this->setHandler("saveBlocksPosition", new ModuleAjaxHandler("saveBlocksPosition", ModuleAjaxHandler::TEXT));
			$this->setHandler("updateBlock", new ModuleAjaxHandler("updateBlock", ModuleAjaxHandler::JSON));

		}
		return $this->ajaxHandlers;
	}

	/**
	 * @var PageService
	 */
	protected $pageService;
	protected $template;
	protected $blockProvider;
	
	public function __construct(){
		/* @var $pageService PageService */
		$this->pageService = new PageService();
		$this->pageService->getTable()->getName();
	}
	

	/**
	 * @param $page PersistPages
	 * @return array
	 * @throws Exception
	 */
	public function getPageForm($page){
		Access::denied("can_edit_pages");
		$provider = new PageAdminPage();
		return $provider->getContent($page);
	}

	/**
	 * get page form for edit, and process page save/delete requests
	 * @return array
	 */
	public function editPage(){
		Access::denied("can_edit_pages");
		$provider = new PageAdminPage();
		return $provider->editPage();
	}

	/**
	 * JSON data
	 * for edit template form
	 * for admin panel
	 * @return array
	 */
	public function editTemplateForm(){
		Access::denied("can_edit_templates");
		if(isset($_POST['href'])){
			$provider = new PageAdminTemplate();
			return $provider->getContent();

		}
		return array('text'=>'Page not found');
	}

	/**
	 * JSON data for content form rendering
	 * for admin panel
	 */
	public function pageContent(){
		Access::denied("can_edit_pages");
		if(isset($_POST['href'])){
			$provider = new PageAdminIncludes($this);
			return $provider->getContent();
		}
		return array('text'=>'Page not found');
	}


	/**
	 * Ajax handler
	 * Return all pages for pages overview rendering
	 * @return array
	 */
	public function getPagesList(){
		Access::denied("can_edit_pages");
		$pagesArray = $this->pageService->loadAll('');
		$out = array();

		/* @var $value PersistPages */
		foreach($pagesArray as $key => $value) {
			$out['pages'][$key] = $value->toJson();
		}
		return $out;
	}

	/**
	 * Ajax Handler
	 * Return all modules list
	 */
	public function getModulesList(){
		Access::denied(array(
			"can_edit_pages",
			"can_edit_templates"
		));

		$dir = glob(Core::MODULES_FOLDER."*");
		$out = array();
		$exclude = array('Core', 'Debug', 'Db', 'Files', 'Module', 'Orm', 'Ui', 'FwException', 'Validation');
		foreach($dir as $name) {
			if(is_dir($name)) {
				$v = ucfirst(basename($name));
				if(!in_array($v, $exclude)) {
					$out[$v] = $v;
				}
			}
		}
		return $out;
	}


	/**
	 * Ajax Handler
	 * parse module, and return this module public methods collection
	 * @param string|null $moduleName
	 * @return array
	 */
	public function getMethodsList($moduleName=null){
		Access::denied(array(
			"can_edit_pages",
			"can_edit_templates"
		));
		$provider = new PageMethodList();
		return $provider->getContent($moduleName);
	}

	/**
	 * Ajax handler
	 * @return array
	 */
	public function processPageContent(){
		Access::denied("can_edit_pages");

		$provider = new PageAdminIncludes();
		return $provider->onAjaxSaveBlock();
	}

	/**
	 * Ajax handler
	 * @return int|string
	 */
	public function deletePageInclude(){
		Access::denied("can_edit_pages");

		$provider = new PageAdminIncludes();
		return $provider->delete();
	}

	/**
	 * Ajax handler
	 * @return array
	 */
	public function createTemplate(){
		Access::denied("can_edit_templates");
		$provider = new PageAdminTemplate();
		if($_POST['action'] == 'save') {
			return $provider->saveNewTemplate();
		} else {
			return $provider->createTemplate();
		}
	}
	/**
	 * Ajax handler
	 * @return array
	 */
	public function editTemplate(){
		Access::denied("can_edit_templates");
		$provider = new PageAdminTemplate();
		return $provider->processTemplateEdit();
	}
	/**
	 * Ajax handler. Process ajax delete request
	 * @return array
	 */
	public function deleteTemplate(){
		Access::denied("can_edit_templates");
		$provider = new PageAdminTemplate();
		return $provider->deleteTemplate();
	}
	/**
	 * Ajax handler. Get delete form for templates
	 * @return array
	 */
	public function deleteTemplateForm(){
		Access::denied("can_edit_templates");
		$provider = new PageAdminTemplate();
		return $provider->getDeleteTemplateForm();
	}
	/**
	 * Ajax handler. Get delete form for templates
	 * @return array
	 */
	public function createBlock(){
		Access::denied("can_edit_templates");
		$provider = new PageAdminTemplate();
		return $provider->createBlock();
	}
	/**
	 * Ajax handler. Get delete form for templates
	 * @return array
	 */
	public function deleteBlock(){
		Access::denied("can_edit_templates");
		$provider = new PageAdminTemplate();
		return $provider->deleteBlock();
	}
	/**
	 * Ajax handler. Get delete form for templates
	 * @return array
	 */
	public function saveBlocksPosition(){
		Access::denied("can_edit_templates");
		$provider = new PageAdminTemplate();
		return $provider->saveBlocksPosition();
	}
	/**
	 * Ajax handler. Get delete form for templates
	 * @return array
	 */
	public function updateBlock(){
		Access::denied("can_edit_templates");
		$provider = new PageAdminTemplate();
		return $provider->updateBlock();
	}

	public function getIncludeTextForm(){
		Access::denied("can_edit_pages");
		$provider = new PageAdminIncludes();
		return $provider->getIncludeTextForm();
	}

	public function changePagePositions(){
		Access::denied("can_edit_pages");
		$provider = new PageAdminPage();
		$provider->changePagePositions();
	}

	/**
	 * Get module event handlers
	 * @return ModuleEventHandler[]
	 */
	public function getEventHandlers()
	{
		// TODO: Implement getEventHandlers() method.
	}
}