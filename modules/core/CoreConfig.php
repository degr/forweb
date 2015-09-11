<?
class CoreConfig{
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
		return CoreConfig::module("Core", $key);
	}

	/**
	 * Get value for selected key from config
	 * @param $key string
	 * @return string
	 * @throws Exception if unknown key was passed
	 */
	public static function module($module, $key){
		if(CoreConfig::$config == null){
			CoreConfig::getConfig();
		}
		if(isset(CoreConfig::$config[$module][$key])){
			return CoreConfig::$config[$module][$key];
		} else {
			throw new Exception("Undefined config value for key: $key");
		}
	}

	/**
	 * get configuration assoc array
	 * @return array
	 */
	public static function getConfig(){
		if(CoreConfig::$config == null){
			$query = "SELECT module, name, value FROM config ORDER by module, name";
			$table = DB::getTable($query);
			foreach($table as $row) {
				CoreConfig::$config[$row['module']][$row['name']] = $row['value'];
			}
		}
		return CoreConfig::$config;
	}
	/**
	 * get configuration assoc array
	 * @return array
	 */
	public static function getGeneralConfig(){
		if(CoreConfig::$config == null){
			CoreConfig::getConfig();
			CoreConfig::$config['Core']['url'] = CoreConfig::getUrl();
			CoreConfig::$config['Core']['localUrl'] = CoreConfig::getLocalUrl();
		}
		if(!empty(CoreConfig::$config['Core'])) {

			return CoreConfig::$config['Core'];
		} else {
			return array();
		}
	}

	public static function getUrl()
	{
		if(CoreConfig::$url == null) {
			$url = CoreConfig::get("url");
			if (empty($url)) {
				$isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
				$url = ($isHttps ? 'https://' : 'http://').$_SERVER['HTTP_HOST']."/";
			}
			CoreConfig::$url = $url;
		}
		return CoreConfig::$url;
	}

	public static function getLocalUrl(){
		if(CoreConfig::$localUrl === null) {
			if(Core::LANGUAGE_IN_URL) {
				$language = Word::getLanguage();
				CoreConfig::$localUrl = CoreConfig::getUrl() . ($language !== null ? $language['locale'] . '/' : '');
			} else {
				CoreConfig::$localUrl = CoreConfig::getUrl();
			}
		}
		return CoreConfig::$localUrl;
	}
}