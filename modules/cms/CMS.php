<?
class CMS implements IModule{
	protected static $ajaxHandlers;
	
	public static function ajaxHandler($moduleName, $handlerName){
		$moduleName = ucfirst($moduleName);
		if(Core::isModuleExist($moduleName)) {
			/* @var $obj IModule */
			$obj = Core::getModule($moduleName);

			$handlers = $obj->getAjaxHandlers();
			if(!empty($handlers[$handlerName])) {
				$handler = $handlers[$handlerName];
			} else {
				$handler = null;
			}
			if($handler != null) {
				$function = $handler->getMethod();
				$out = $obj->$function();
				if (isset($out)) {
					if ($handler->getResponse() == AjaxHandler::JSON) {
						header('Content-Type: application/json');
						echo json_encode($out);
					} else {
						header("Content-Type: text/plain");
						echo $out;
					}
				}
				exit;
			}
		}
		echo "unknown ajax handler";
		exit;
	}

	public static function processForm($moduleName, $handlerName)
	{
		$moduleName = ucfirst($moduleName);
		if(Core::isModuleExist($moduleName)) {
			/* @var $obj IModule */
			$obj = Core::getModule($moduleName);
			$handlers = $obj->getFormHandlers();
			if(!empty($handlers[$handlerName])) {
				$handler = $handlers[$handlerName];
			} else {
				$handler = null;
			}

			if($handler != null) {
				$function = $handler->getMethod();
				$obj->$function($handler);
				$url = $handler->getUrl();
				if(empty($url)) {
					$url = $_SERVER['HTTP_REFERER'];
					if(empty($url)) {
						$url = Config::get("url");
					}
				}
				$errors = $handler->getErrors();
				if(!empty($errors)) {
					UI::setFormErrors($moduleName, $handlerName, $errors);
				}
				header('location: '.$url);
				exit;
			}
		}
		echo "unknown form handler";
		exit;
	}

	/**
	 * Get admin panel's javascript files
	 * @param UI $ui
	 * @throws Exception
	 */
	public function getAdminPanel(UI $ui) {
		if(Access::can("can_see_admin_panel")) {
			$ui->addVariable(
				'admin_translations',
				addslashes(json_encode(Word::get('admin')))
			);
			$ui->addVariable('adminIncludeOptions', addslashes(json_encode(Page::getIncludeTypesList())));
			$ui->addVariable('url', Config::get("url"));
			$ui->setLayout('page/admin/main.tpl');
		}
	}


	/**
	 * Get module ajax handlers
	 * @return AjaxHandler[]
	 */
	public function getAjaxHandlers()
	{
		// TODO: Implement getAjaxHandlers() method.
	}

	/**
	 * Get module form handlers
	 * @return FormHandler[]
	 */
	public function getFormHandlers()
	{
		// TODO: Implement getFormHandlers() method.
	}

	/**
	 * Get module ajax handler with selected name
	 * @param string $name
	 * @return AjaxHandler
	 */
	public function getAjaxHandler($name)
	{
		// TODO: Implement getAjaxHandler() method.
	}

	/**
	 * Get module form handlers with selected name
	 * @param string $name
	 * @return FormHandler
	 */
	public function getFormHandler($name)
	{
		// TODO: Implement getFormHandler() method.
	}
}