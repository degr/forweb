<?
class Page_Dispatcher{
	protected $path;
	protected $params;
	protected $url;
	
	public function __construct($url){
		$this->url = $url;
	}
	
	public function handleRequest(){
		$parsed = parse_url($this->url);

		$path = urldecode($parsed['path']);
		$path = preg_replace("/(^\/)|(\/)$/", "", $path);
		$this->path = $path;
		$this->params = explode("/", $this->path);
		if(empty($this->params)){
			$this->params = array('home');
		}
	}

	public function getParams(){
		return $this->params;
	}
	
	public function getParam($position){
		if(!empty($this->params[$position])){
			return $this->params[$position];
		}
		return "";
	}
	
	public function getUrl(){
		return $this->url;
	}
}