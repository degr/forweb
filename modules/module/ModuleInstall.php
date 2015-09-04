<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 21.03.2015
 * Time: 11:55
 */
interface ModuleInstall{
    /**
     * Make all necessary preparations for this module
     * as PersistTables deploy, cache files creating etc..
     * @return void
     */
    public function install();

    /**
     * Return information about this module
     * @return string
     */
    public function getInfo();

    /**
     * get module dependencies for deploy
     * @return ModuleDependency[]
     */
    public function getDependencies();

    /**
     * Get user input group
     * @return ModuleInput
     */
    public function getUserInput();
}