<?
class Config{
	protected static $config;
	protected static $url;
	protected static $localUrl;

	/**
	 * Get value for selected key from config
	 * @param $key string
	 * @return string
	 * @throws Exception if unknown key was passed
	 */
	public static function get($key){
		return Config::module("Core", $key);
	}

	/**
	 * Get value for selected key from config
	 * @param $key string
	 * @return string
	 * @throws Exception if unknown key was passed
	 */
	public static function module($module, $key){
		if(Config::$config == null){
			Config::getConfig();
		}
		if(isset(Config::$config[$module][$key])){
			return Config::$config[$module][$key];
		} else {
			throw new Exception("Undefined config value for key: $key");
		}
	}

	/**
	 * get configuration assoc array
	 * @return array
	 */
	public static function getConfig(){
		if(Config::$config == null){
			$query = "SELECT module, name, value FROM config ORDER by module, name";
			$table = DB::getTable($query);
			foreach($table as $row) {
				Config::$config[$row['module']][$row['name']] = $row['value'];
			}
		}
		return Config::$config;
	}
	/**
	 * get configuration assoc array
	 * @return array
	 */
	public static function getGeneralConfig(){
		if(Config::$config == null){
			Config::getConfig();
			Config::$config['Core']['url'] = Config::getUrl();
			Config::$config['Core']['localUrl'] = Config::getLocalUrl();
		}
		if(!empty(Config::$config['Core'])) {

			return Config::$config['Core'];
		} else {
			return array();
		}
	}

	public static function getUrl()
	{
		if(Config::$url == null) {
			$url = Config::get("url");
			if (empty($url)) {
				$params = parse_url($_SERVER['HTTP_REFERER']);
				$url = $params['scheme'] . "://" . $params['host'] . "/";
			}
			Config::$url = $url;
		}
		return Config::$url;
	}

	public static function getLocalUrl(){
		if(Config::$localUrl === null) {
			if(Core::LANGUAGE_IN_URL) {
				$language = Word::getLanguage();
				Config::$localUrl = Config::getUrl() . ($language !== null ? $language['locale'] . '/' : '');
			} else {
				Config::$localUrl = Config::getUrl();
			}
		}
		return Config::$localUrl;
	}
}