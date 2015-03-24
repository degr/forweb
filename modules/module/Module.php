<?
interface IModule{
	/**
	 * Get module ajax handlers
	 * @return AjaxHandler[]
	 */
	public function getAjaxHandlers();
	/**
	 * Get module form handlers
	 * @return FormHandler[]
	 */
	public function getFormHandlers();
}

abstract class Module implements IModule{
	/**
	 * @var array
	 */
	protected $ajaxHandlers;
	protected $formHandlers;

	protected $service;
	/**
	 * @param $name
	 * @return AjaxHandler
	 */
	public function getAjaxHandler($name){
		if($this->ajaxHandlers == null) {
			$this->getAjaxHandlers();
		}
		if(empty($this->ajaxHandlers[$name])){
			return null;
		}else {
			return $this->ajaxHandlers[$name];
		}
	}

	/**
	 * Get form handler from
	 * @param $handlerName
	 * @return FormHandler
	 */
	public function getFormHandler($handlerName)
	{
		if($this->formHandlers == null) {
			$this->getFormHandlers();
		}
		if(empty($this->formHandlers[$handlerName])){
			return null;
		}else {
			return $this->formHandlers[$handlerName];
		}
	}

	public function setHandler($name, AjaxHandler $handler){
		if(empty($this->ajaxHandlers[$name])) {
			$this->ajaxHandlers[$name] = $handler;
		} else {
			throw new Exception("Ajax handler with name: ".$name." already exist.");
		}
	}

	/**
	 * @return Service
	 */
	public function getService(){
		if($this->service == null) {
			$class = get_class($this);
			$path = Core::MODULES_FOLDER.strtolower($class).'/'.$class.'.php';
			if(file_exists($path)) {
				$service = $class."_Service";
				$this->service = new $service();
			}
		}
		return $this->service;
	}



}