<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 21.03.2015
 * Time: 11:52
 */

class CoreInstall implements ModuleInstall{

    protected $installedModules;
    
    public function run(){
        if(!empty($_GET['dependecies'])) {
            $out = $this->getProjectDependencies();
        } else if(!empty($_GET['download'])) {
            $out = $this->getDownloadResult();
        } elseif (!empty($_GET['collectUserInput'])) {
            $out = $this->collectUserInput();
        } elseif(!empty($_GET['installExisting'])){
            $out = $this->installExisting();
        } else {
            echo file_get_contents(Core::MODULES_FOLDER . 'core/install/resources/install.html');
            exit;
        }
        $this->onAjaxResult($out);
        exit;


    }

    private function onAjaxResult($var){
        header('Content-Type: application/json; charset=utf-8');
        header("Pragma: no-cache");
        header("Cache-control: private, must-revalidate");
        header("Content-disposition: inline");
        echo json_encode($var);
    }

    /**
     * Get module dependencyes, install each of them, than install module
     * @param $moduleName string
     * @throws FwException if module not exist LOCALLY
     * @return string (html data about installed module)
     */
    private function installModule($moduleName)
    {
        $out = array();
        if($this->isModuleExistLocally($moduleName)) {
            $installMessage = array(
                0 => '<div style="padding: 10px; border: 1px dotted lightgray">',
                1 => null,
                2 => '<p>Module <span style="font-weight: bold">"'.$moduleName.'"</span> installed successfully.</p>'.
                    '</div>'
            );
            if($this->isModuleHasInstallation($moduleName) && !in_array(strtolower($moduleName), $this->installedModules)) {
                /* @var $moduleInstallObject ModuleInstall */
                $moduleInstallObject = $this->getInstallObject($moduleName);
                $dependencies = $moduleInstallObject->getDependencies();
                if (!empty($dependencies)) {
                    $items = array();
                    foreach ($dependencies as $dependency) {
                        $items = array_merge($items, $this->resolveDependency($dependency));
                    }
                    $out = $items;
                }
                $moduleInstallObject->install();
                $this->resolveUserInput($moduleInstallObject, $moduleName);
                $installMessage[1] = $moduleInstallObject->getInfo();
            }
            if(!in_array(strtolower($moduleName), $this->installedModules)) {
                array_unshift($out, implode('', array_filter($installMessage)));
                $this->installedModules[] = strtolower($moduleName);
            }
        } else {
            throw new FwException("Can't install module because it does not exist.");
        }
        return $out;
    }

    /**
     * Get install object
     * @param $moduleName string
     * @return ModuleInstall
     */
    protected function getInstallObject($moduleName) {
        $class = ucfirst($moduleName)."Install";
        return new $class;
    }

    /**
     * Make all necessary preparations for this module
     * as PersistTables deploy, cache files creating etc..
     * @return void
     */
    public function install()
    {
        ORM::createTable($this->getConfigTable());
        $query = "SELECT id FROM config where name='url' AND module = 'Core'";
        $id = DB::getCell($query);
        if(empty($id)) {
            $query = "insert into config (module, name, value) values ('Core', 'url', '')";
            DB::query($query);
        }
    }

    /**
     * Return information about this module
     * @return string
     */
    public function getInfo()
    {
        ob_start();
        echo '<h3>ForWeb framework Core package</h3>';
        echo '<p>Contain base modules pack:</p>';
        echo '<ul>';
        echo '<li>Core</li>';
        echo '<li>Cms</li>';
        echo '<li>module</li>';
        echo '<li>page</li>';
        echo '<li>access</li>';
        echo '<li>user</li>';
        echo '</ul>';
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }


    /**
     * get module dependencies for deploy
     * @return array
     */
    public function getDependencies()
    {
        return array(
            new ModuleDependency('module'),
            new ModuleDependency('db'),
            new ModuleDependency('fwexception'),
            new ModuleDependency('scriptcollector'),
            new ModuleDependency('scss'),
            new ModuleDependency('ui'),
            new ModuleDependency('validation'),
            new ModuleDependency('page'),
            new ModuleDependency('access'),
            new ModuleDependency('api')
        );
    }
    /**
     * Try to resolve module dependency
     * @param $dependency ModuleDependency
     * @return array
     * @throws FwException, if dependency does not contain id and name;
     */
    private function resolveDependency($dependency)
    {
        if(empty($dependency->moduleName)) {
            throw new FwException("Module dependency does not contain name property.");
        }
        $out = array();
        if(!in_array($dependency->moduleName, $this->installedModules)) {
            $out = array_merge($out, $this->installModule($dependency->moduleName));
        }
        return $out;
    }

    /**
     * Download module from dependency url, or,
     * if dependencyUrl is undefined, from ForWeb modules storage
     * @param $dependency ModuleDependency
     * @throws FwException
     */
    private function downloadModule($dependency){
        if(!empty($dependency->moduleUrl)) {
            $this->downloadModuleFromUrl($dependency, $dependency->moduleUrl);
        } elseif(empty($dependency->moduleUrl) && !empty($dependency->moduleId)) {
            $this->downloadModuleFromFwStorage($dependency);
        } else {
            throw new FwException("You have no local module copy, to satisfy dependency with name: '"
                .$dependency->moduleName."' and dependency does not contain info about web storage.");
        }
    }


    private function isModuleExistLocally($moduleName)
    {
        $file = Core::MODULES_FOLDER.strtolower($moduleName)."/".ucfirst($moduleName).".php";
        return is_file($file);
    }

    private function isModuleHasInstallation($moduleName)
    {
        $installClassName = $moduleName.'Install';
        return class_exists($installClassName, true);
    }

    /**
     * Download module using dependency url
     * @param $dependency ModuleDependency
     * @param $url string location, from where file will be downloaded
     */
    private function downloadModuleFromUrl($dependency, $url)
    {
        $folder = Core::MODULES_FOLDER.strtolower($dependency->moduleName);
        $archive = $folder.'/archive.zip';
        Files::download($url, $archive);
        Files::extract($archive, $folder);
        unlink($archive);
    }

    /**
     * Download file from Forweb modules storage
     * @param $dependency ModuleDependency
     */
    private function downloadModuleFromFwStorage($dependency)
    {
        $url = "http://forweb.org/modules/storage/".$dependency->moduleId;
        $this->downloadModuleFromUrl($dependency, $url);
    }

    private function getConfigTable()
    {
        $table = new OrmTable("config", 'config');
        $id = new OrmTableField("id", "integer");
        $id->setAutoIncrement(true)->setPrimary(true)->setLength(11);
        $table->addField($id);

        $module = new OrmTableField("module", "varchar");
        $module->setLength(50);
        $module->setDefaultValue("");
        $table->addField($module);

        $key = new OrmTableField("name", "varchar");
        $key->setLength(50);
        $table->addField($key);

        $value = new OrmTableField("value", "varchar");
        $value->setLength(255);
        $table->addField($value);

        return $table;
    }

    /**
     * Get user input group
     * @return ModuleInput
     */
    public function getUserInput()
    {
        return array(new CoreInstallInputUrl());
    }


    /////////////////////////////////////////////////////////////////////////
    private function getProjectDependencies()
    {
        $modules = glob(Core::MODULES_FOLDER."*");
        $out = array();
        foreach($modules as $module) {
            if(!is_dir($module)) {
                continue;
            }
            $installClass = $this->findModuleInstallationClass($module);
            if($installClass == null || !class_exists($installClass, true)) {
                $dependenices = array();
            } else {
                /** @var $installObject ModuleInstall */
                $installObject = new $installClass();
                $dependenices = $installObject->getDependencies();
                if (empty($dependenices)) {
                    $dependenices = array();
                }
            }
            $out[strtolower(basename($module))] = $dependenices;
        }
        return $this->getDependenciesAsArray($out);
    }

    private function findModuleInstallationClass($moduleFolder){
        $files = glob(preg_replace('/\/$/', '', $moduleFolder)."/*");
        foreach($files as $moduleFile) {
            if(strtolower(basename($moduleFile)) === strtolower(basename($moduleFolder)."install.php")){
                return preg_replace('/\.php$/i', '', basename($moduleFile));
            }
        }
        return null;
    }

    /**
     * Return dependencies object for http transport
     * @param $dependencies ModuleDependency[]
     * @return array of arrays with module dependencies
     * @throws FwException in case when dependency contain invalid data
     */
    private function getDependenciesAsArray($dependencies)
    {
        if(empty($dependencies)) {
            return array();
        } else {
            $out = array();
            foreach($dependencies as $key => $dependency) {
                if(is_array($dependency)) {
                    $out[$key] = $this->getDependenciesAsArray($dependency);
                } elseif (is_object($dependency)) {
                    $out[] = array(
                        'minorVersion' => $dependency->minorVersion,
                        'majorVersion' => $dependency->majorVersion,
                        'moduleId' => $dependency->moduleId,
                        'moduleName' => $dependency->moduleName,
                        'moduleUrl' => $dependency->moduleUrl,
                        'isExistLocally' => $this->isModuleExistLocally($dependency->moduleName)
                    );
                } elseif(empty($dependency)) {
                    continue;
                } else {
                    throw new FwException("Invalid data. Dependency expected, ".gettype($dependency)." given.");
                }
            }
            return $out;
        }
    }

    private function getDownloadResult()
    {
        if(empty($_POST['items']) || !is_array($_POST['items'])){
            return array();
        }
        $path = Core::MODULES_FOLDER.'core/install/temp/archive.zip';
        $out = array();
        foreach($_POST['items'] as $object) {
            $module = $object['module'];
            $url = $object['url'];
            try {
                $out[$module] = Files::download($url, $path) 
                    && Files::extract($path, Core::MODULES_FOLDER . strtolower($module));
            } catch (Exception $e) {
                $out[$module] = false;
            }
            unlink($path);
        }
        return $out;
    }

    private function installExisting()
    {
        $this->installedModules = array("module");
        $out = $this->installModule('core');
        $modules = glob(Core::MODULES_FOLDER."*");
        foreach($modules as $module) {
            if(is_dir($module) && in_array(basename($module), $this->installedModules)){
                $out = array_merge($out, $this->installModule(basename($module)));
            }
        }
        return array_filter($out);
    }

    private function collectUserInput()
    {
        $out = array();
        $modules = glob(Core::MODULES_FOLDER."*");
        foreach($modules as $moduleName) {
            $moduleName = basename($moduleName);
            if($this->isModuleExistLocally($moduleName)) {
                if($this->isModuleHasInstallation($moduleName)) {
                    /* @var $moduleInstallObject ModuleInstall */
                    $moduleInstallObject = $this->getInstallObject($moduleName);
                    $inputArray = $moduleInstallObject->getUserInput();
                    if(!empty($inputArray)) {
                        $item = array();
                        /** @var $input ModuleInput */
                        foreach($inputArray as $input) {
                            $item[] = array(
                                'identifier' => $input->getIdentifier(),
                                'question' => $input->getQuestion(),
                                'default' => $input->getDefaultValue()
                            );
                        }
                        if(!empty($item)) {
                            $out[$moduleName] = $item;
                        }
                    }
                }
            } else {
                throw new FwException("Can't install module because it does not exist(".$moduleName.").");
            }
        }
        return array_filter($out);
    }

    /**
     * @param $installObject ModuleInstall
     * @param $moduleName
     */
    private function resolveUserInput($installObject, $moduleName)
    {
        $input = $installObject->getUserInput();
        if(empty($_POST[$moduleName]) || empty($input)) {
            return;
        }
        /** @var $inputProcessObject ModuleInput */
        foreach($installObject->getUserInput() as $inputProcessObject) {
            $id = $inputProcessObject->getIdentifier();
            $value = empty($_POST[$moduleName][$id]) ? '' : $_POST[$moduleName][$id]; 
            $inputProcessObject->process($value);
        }
    }
}