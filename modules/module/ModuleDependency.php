<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 21.03.2015
 * Time: 12:34
 */
class ModuleDependency{
    public function __construct($moduleName) {
        if(empty($moduleName)) {
            throw new FwException("Module name property is required for dependeny object. It can't be empty.");
        }
        $this->moduleName = $moduleName;
    }
    /**
     * minimum module version
     * can be nul
     * @var integer @TODO
     */
    public $minorVersion;
    /**
     * maximum module version
     * can be null
     * @var integer @TODO
     */
    public $majorVersion;
    /**
     * Module id used for download module from http://forweb.org/storage/%moduleId%
     * can be null
     * @var string hash @TODO
     */
    public $moduleId;

    /**
     * module name for local storage.
     *
     * Can't be null. If you have no local module copy, but $this::moduleId or $this::moduleUrl defined,
     * module archive will be downloaded from any source, and extracted to folder with dependency name.
     *
     * If name defined, and module exist, it will check for versions. If version satisfy,
     * download operation will not be invoked.
     *
     * @var string
     */
    public $moduleName;
    /**
     * If module does not exist localy, it will be downloaded from this url
     * (not from ForWeb storage)
     * @var string url
     */
    public $moduleUrl;
}