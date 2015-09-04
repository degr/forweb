<?
interface IModule{
	/**
	 * Get module ajax handlers
	 * @return AjaxHandler[]
	 */
	public function getAjaxHandlers();

	/**
	 * Get module event handlers
	 * @return EventHandler[]
	 */
	public function getEventHandlers();
}

abstract class Module implements IModule{
	/**
	 * @var AjaxHandler[]
	 */
	protected $ajaxHandlers;
	/**
	 * @var EventHandler[]
	 */
	protected $eventHandlers;

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
				$service = $class."Service";
				$this->service = new $service();
			}
		}
		return $this->service;
	}



}