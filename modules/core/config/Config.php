<?

class Core_Config_Config{


    public function saveConfig()
    {
        $query = "INSERT INTO config (module, name, value) VALUES";
        $values = array();
        $config = Config::getConfig();
        foreach($_POST as $module => $data) {
            foreach($data as $name => $value) {
                if(!isset($config[$module][$name])) {
                    $values[] = "('".DB::escape($module)."', '".DB::escape($name)."','".DB::escape($value)."')";
                } else {
                    $updateQuery = "UPDATE config SET value='".$value."' WHERE module='".DB::escape($module)
                        ."' AND name='".DB::escape($name)."'";
                    DB::query($updateQuery);
                }
            }
        }
        if(count($values) != 0) {
            $query .= implode(",",$values);
            DB::query($query);
        }
        return 1;
    }

    public function getAjaxConfig()
    {
        $config = Config::getConfig();
        /* @var $page Page */
        $page = Core::getModule('Page');
        $modules = $page->getModulesList();
        foreach($modules as $k => $v) {
            if(empty($config[$k])) {
                $config[$k] = array();
            }
        }
        return $config;
    }

    public function deleteConfig()
    {
        $module = $_POST['module'];
        if(empty($module)){
            return 0;
        }
        $name = $_POST['name'];
        if(empty($name)) {
            return 0;
        }
        $query = "DELETE FROM config WHERE module = '".DB::escape($module)."' AND name='".DB::escape($name)."'";
        DB::query($query);
        return 1;
    }
}