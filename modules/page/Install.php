<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 21.03.2015
 * Time: 15:41
 */
class Page_Install implements Module_IInstall{
    /**
     * @var ORM_Objects_Table[]
     */
    protected $tables;

    /**
     * @return ORM_Objects_Table[]
     */
    public function getTables(){
        if(empty($this->tables)) {
            $tablesProvider = new Core_Install_Tables();
            $this->tables['pages'] = $tablesProvider->getPagesTable();
            $this->tables['templates'] = $tablesProvider->getTemplatesTable();
            $this->tables['includes'] = $tablesProvider->getIncludesTable();
            $this->tables['blocks'] = $tablesProvider->getBlocksTable();
        }
        return $this->tables;
    }
    /**
     * Make all necessary preparations for this module
     * as PersistTables deploy, cache files creating etc..
     * @return void
     */
    public function install()
    {
        $tables = $this->getTables();
        foreach($tables as $table) {
            ORM::registerTableOnFly($table);
        }
        $tables['pages']->bindTable('template', 'id', 'templates', ORM_Objects_Table::MANY_TO_ONE, false, false);
        $pagesBind = $this->tables['pages']->bindTable('parent', 'id', 'pages', ORM_Objects_Table::MANY_TO_ONE, false, true);
        $pagesBind->setCustomLeftField("parentPage");

        $pageToIncludeBind = $tables['pages']->bindTable('id', 'page', 'includes', ORM_Objects_Table::ONE_TO_MANY, true, true);
        $pageToIncludeBind->setCustomLeftField("includes");


        //@TODO includes table must be binded to blocks, blocks to templates. Templates need no bind to includes.
        $templateToIncludeBind = $tables['templates']->bindTable('id', 'template', 'includes', ORM_Objects_Table::ONE_TO_MANY, true, true);
        $templateToIncludeBind->setCustomLeftField("includes");

        $templateToBlockBind = $tables['templates']->bindTable('id', 'template', 'blocks', ORM_Objects_Table::ONE_TO_MANY, true, true);
        $templateToBlockBind->setCustomLeftField("blocks");

        foreach($tables as $table) {
            ORM::createTable($table);
        }
        $this->addBaseDataToDatabase();
    }

    /**
     * Return information about this module
     * @return string
     */
    public function getInfo()
    {
        ob_start();
        echo '<h3>ForWeb framework Page package</h3>';
        echo '<p>Contain main page rendering functions and database tables:</p>';
        echo '<ul>';
        echo '<li>Pages table</li>';
        echo '<li>Templates table</li>';
        echo '<li>Blocks table</li>';
        echo '<li>Includes table</li>';
        echo '</ul>';
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    /**
     * get module dependencies for deploy
     * @return Module_Dependency[]
     */
    public function getDependencies()
    {
        return null;
    }

    private function addBaseDataToDatabase()
    {

        $query = "select id from pages where id = 1";
        $pageId = DB::getCell($query);
        if(empty($pageId)) {
            $query = "insert into pages (id, name, url, parent, template) VALUES ("
                . "1, 'home', 'home', 0, 1)";
            DB::query($query);
        }
        $query = "select id from templates where id = 1";
        $templateId = DB::getCell($query);
        if(empty($templateId)) {
            $query = "insert into templates (id, name, parent, template) VALUES ("
                . "1, 'base', 0, 'base.tpl')";
            DB::query($query);
        }
        $query = "select id from blocks where id IN (1,2,3)";
        $blockIds = DB::getColumn($query);
        if(empty($blockIds)) {
            $query = "insert into blocks (id, name, position, template) VALUES "
                ."(1, 'content', 1, 1),"
                ."(2, 'sidebar', 2, 1),"
                ."(3, 'header',  3, 1) ";
            DB::query($query);
        }

        $query = "select id from includes where id IN (1,2,3,4)";
        $includesIds = DB::getColumn($query);
        if(empty($includesIds)) {
            $query = "insert into includes (id, page, template, type, block, positionNumber, position, content, module, method, comment) VALUES "
                ."(1, 0, 1, 'executable', 1, 1, 'template', '', 'CMS', 'getAdminPanel', 'Administrator panel. Do not delete.'),"
                ."(2, 0, 1, 'html', 1, 2, 'template', '<h1>this is content header (template include)</h1>', '', '', 'Static text example 1'),"
                ."(3, 0, 1, 'html', 2, 1, 'template', '<br/>this is sidebar (page include', '', '', 'Static text example 2'),"
                ."(4, 0, 1, 'html', 3, 1, 'template', '<h1>This is site header (template include)</h1>', '', '', 'Static text example 3')";
            DB::query($query);
        }

    }
}