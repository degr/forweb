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
			$this->setHandler("editPage", new AjaxHandler("editPage", AjaxHandler::JSON));
			$this->setHandler("changePagePositions", new AjaxHandler("changePagePositions", AjaxHandler::JSON));
			$this->setHandler("showPagesTree", new AjaxHandler("getPagesList", AjaxHandler::JSON));
			$this->setHandler("editTemplateForm", new AjaxHandler("editTemplateForm", AjaxHandler::JSON));
			$this->setHandler("processPageContent", new AjaxHandler("processPageContent", AjaxHandler::JSON));
			$this->setHandler("pageContent", new AjaxHandler("pageContent", AjaxHandler::JSON));
			$this->setHandler("deletePageInclude", new AjaxHandler("deletePageInclude", AjaxHandler::JSON));
			$this->setHandler("getMethodsList", new AjaxHandler("getMethodsList", AjaxHandler::JSON));
			$this->setHandler("getModulesList", new AjaxHandler("getModulesList", AjaxHandler::JSON));
			$this->setHandler("getIncludeTextForm", new AjaxHandler("getIncludeTextForm", AjaxHandler::JSON));

			$this->setHandler("createTemplate", new AjaxHandler("createTemplate", AjaxHandler::JSON));
			$this->setHandler("editTemplate", new AjaxHandler("editTemplate", AjaxHandler::JSON));
			$this->setHandler("deleteTemplate", new AjaxHandler("deleteTemplate", AjaxHandler::TEXT));

			$this->setHandler("deleteTemplateForm", new AjaxHandler("deleteTemplateForm", AjaxHandler::JSON));
			$this->setHandler("createBlock", new AjaxHandler("createBlock", AjaxHandler::JSON));
			$this->setHandler("deleteBlock", new AjaxHandler("deleteBlock", AjaxHandler::TEXT));
			$this->setHandler("saveBlocksPosition", new AjaxHandler("saveBlocksPosition", AjaxHandler::TEXT));
			$this->setHandler("updateBlock", new AjaxHandler("updateBlock", AjaxHandler::JSON));

		}
		return $this->ajaxHandlers;
	}

	/**
	 * @var Page_Service
	 */
	protected $pageService;
	protected $template;
	protected $blockProvider;
	
	public function __construct(){
		/* @var $pageService Page_Service */
		$this->pageService = Core::getModule("Page_Service");
		$this->pageService->getTable()->getName();
	}
	

	/**
	 * @param $page PersistPages
	 * @return array
	 * @throws Exception
	 */
	public function getPageForm($page){
		Access::denied("can_edit_pages");
		$provider = new Page_Admin_Page();
		return $provider->getContent($page);
	}

	/**
	 * get page form for edit, and process page save/delete requests
	 * @return array
	 */
	public function editPage(){
		Access::denied("can_edit_pages");
		$provider = new Page_Admin_Page();
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
			$provider = new Page_Admin_Template();
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
			$provider = new Page_Admin_Includes($this);
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

		$dir = glob("modules/*");
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
		$provider = new Page_MethodList();
		return $provider->getContent($moduleName);
	}

	/**
	 * Ajax handler
	 * @return array
	 */
	public function processPageContent(){
		Access::denied("can_edit_pages");

		$provider = new Page_Admin_Includes();
		return $provider->onAjaxSaveBlock();
	}

	/**
	 * Ajax handler
	 * @return int|string
	 */
	public function deletePageInclude(){
		Access::denied("can_edit_pages");

		$provider = new Page_Admin_Includes();
		return $provider->delete();
	}

	/**
	 * Ajax handler
	 * @return array
	 */
	public function createTemplate(){
		Access::denied("can_edit_templates");
		$provider = new Page_Admin_Template();
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
		$provider = new Page_Admin_Template();
		return $provider->processTemplateEdit();
	}
	/**
	 * Ajax handler. Process ajax delete request
	 * @return array
	 */
	public function deleteTemplate(){
		Access::denied("can_edit_templates");
		$provider = new Page_Admin_Template();
		return $provider->deleteTemplate();
	}
	/**
	 * Ajax handler. Get delete form for templates
	 * @return array
	 */
	public function deleteTemplateForm(){
		Access::denied("can_edit_templates");
		$provider = new Page_Admin_Template();
		return $provider->getDeleteTemplateForm();
	}
	/**
	 * Ajax handler. Get delete form for templates
	 * @return array
	 */
	public function createBlock(){
		Access::denied("can_edit_templates");
		$provider = new Page_Admin_Template();
		return $provider->createBlock();
	}
	/**
	 * Ajax handler. Get delete form for templates
	 * @return array
	 */
	public function deleteBlock(){
		Access::denied("can_edit_templates");
		$provider = new Page_Admin_Template();
		return $provider->deleteBlock();
	}
	/**
	 * Ajax handler. Get delete form for templates
	 * @return array
	 */
	public function saveBlocksPosition(){
		Access::denied("can_edit_templates");
		$provider = new Page_Admin_Template();
		return $provider->saveBlocksPosition();
	}
	/**
	 * Ajax handler. Get delete form for templates
	 * @return array
	 */
	public function updateBlock(){
		Access::denied("can_edit_templates");
		$provider = new Page_Admin_Template();
		return $provider->updateBlock();
	}

	public function getIncludeTextForm(){
		Access::denied("can_edit_pages");
		$provider = new Page_Admin_Includes();
		return $provider->getIncludeTextForm();
	}

	public function changePagePositions(){
		Access::denied("can_edit_pages");
		$provider = new Page_Admin_Page();
		$provider->changePagePositions();
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