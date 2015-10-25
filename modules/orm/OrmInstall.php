<?php
class OrmInstall implements ModuleInstall{


    public static function createDBTable(OrmTable $table){
        $primaryKey = $table->getPrimaryKey();
        if(empty($primaryKey)){
            throw new Exception("Can't create table with no primary keys, please define it (".$table->getName().")");
        }

        $tables = DB::getColumn("SHOW TABLES");
        if(in_array($table->getName(), $tables)) {
            $columns = DB::getColumn("SHOW COLUMNS FROM ".$table->getName());
            foreach($table->getFields() as $field) {
                if(!in_array($field->getName(), $columns)) {
                    $defaultValue = $field->getDefaultValue();

                    $enumValues = $field->getEnumValues();
                    $query = "ALTER TABLE ".$table->getName()." ADD "
                        .$field->getName()." ".$field->getType()
                        .(!empty($enumValues) ? "('".implode("','", $enumValues)."')" : "")
                        .($field->getLength() !== 0 ? "(".$field->getLength().")" : "" )
                        .($field->getAutoIncrement()?" AUTO_INCREMENT ":"")
                        .($field->getCanBeNull() ? "" : " NOT NULL " )
                        .(!empty($defaultValue ) || $defaultValue === 0 ? " DEFAULT ".self::getDefaultValueForField($field)." " : "");
                    DB::query($query);
                    if($field->getUnique()) {
                        $query = "ALTER TABLE ".$table->getName()." ADD UNIQUE (".$field->getName().")";
                        DB::query($query);
                    }
                }
            }
        } else {
            $query = "CREATE TABLE ".$table->getName()." (";
            $fields = array();
            /* @var $field OrmTableField */
            foreach($table->getFields() as $field){
                $defaultValue = $field->getDefaultValue();
                $enumValues = $field->getEnumValues();
                $fields[] = $field->getName()." ".$field->getType()." "
                    .(!empty($enumValues) ? "('".implode("','", $enumValues)."')" : "")
                    .($field->getLength() !== 0 ? "(".$field->getLength().")" : "" )
                    .($field->getAutoIncrement()?" AUTO_INCREMENT ":"")
                    .($field->getCanBeNull() ? "" : " NOT NULL " )
                    .(!empty($defaultValue ) || $defaultValue === 0 ? " DEFAULT ".self::getDefaultValueForField($field)." " : "")

                    .($field->getUnique() ? ", UNIQUE (".$field->getName().")" : "")
                    .($field->getPrimary() ? ", PRIMARY KEY (".$field->getName().")" : "");
            }

            $query .= implode(", ", $fields).") ENGINE=InnoDB CHARACTER SET=UTF8;";
            DB::query($query);
        }


        foreach($table->getFields() as $field) {
            if($field->getIndex()) {
                if(!OrmInstall::isIndexExist($table->getName(), $field->getName())) {
                    $query = 'ALTER TABLE ' . $table->getName() . ' ADD INDEX ' . $field->getName() . '(' . $field->getName() . ')';
                    DB::query($query);
                }
            }
        }
    }

    private static function getDefaultValueForField(OrmTableField $field){
        $intTypes = array('integer', 'boolean', 'bit');
        if(!in_array($field->getType(), $intTypes)) {
            $defaultValue = "'".DB::escape($field->getDefaultValue())."'";
        } else {
            $defaultValue = $field->getDefaultValue();
        }
        return $defaultValue;
    }

    public static function serializeTable(OrmTable $table){
        $data = serialize($table);
        $folder = ORM::getTablesFolder();
        if(!is_dir($folder)){
            OrmInstall::createFolder($folder);
        }
        if(!is_file($folder.".htaccess")){
            file_put_contents($folder.".htaccess", "deny from all");
        }
        $filename = $folder.$table->getName().".data";
        if(is_file($filename)){
            unlink($filename);
        }
        file_put_contents($filename, $data);
    }

    public static function createPersistanceClass(OrmTable $table){
        $text = "<?php \n"
            .OrmInstall::getClassDescription($table)
            ."\nclass ".$table->getPersistClassName()." extends OrmPersistenceBaseImpl {\n";

        $binds = $table->getBinds();
        //write all class properties, including binded properties and their doc
        /* @var $field OrmTableField */

        foreach($table->getFields() as $field) {
            $currentBind = OrmInstall::getBindForField($field, $binds);
            if(!empty($currentBind)){
                $postfix = $currentBind->getLeftField() === $field->getName() ? OrmUtils::BIND_PREFIX : "";
                $text .= OrmInstall::getTextForBindVariable($currentBind, $postfix);
            } else {
                $postfix = "";
            }
            $text .= OrmInstall::getTextForVariable($field, $postfix);
        }

        //write all class setters and getters, include binded
        /* @var $field OrmTableField */
        foreach($table->getFields() as $field) {
            /* @var $bind OrmTableBind */
            $currentBind = OrmInstall::getBindForField($field, $binds);

            if(!empty($currentBind)){
                //$text .= "//////////////Persist object " . $field->getName(). "  getter and setter ///////////////\n";
                $postfix = $currentBind->getLeftField() === $field->getName() ? OrmUtils::BIND_PREFIX : "";
                $text .= OrmInstall::getTextForBindGetter($currentBind, $postfix);
                $text .= OrmInstall::getTextForBindSetter($currentBind);
            } else {
                $postfix = "";
            }
            //$text .= "//////////////" . $field->getName().$postfix . " getter and setter ///////////////\n";
            $text .= OrmInstall::getTextForFieldGetter($field, $table, $postfix);
            $text .= OrmInstall::getTextForSetter($field, $table, $postfix);
            if($field->getPrimary()) {
                $text .= OrmInstall::getTextForPrimaryKey($field, $postfix);
            }
        }
        $text .= "}";
        $folder = ORM::getPersistObjectsFolder();
        $path = $folder.$table->getPersistClassName().".php";
        OrmInstall::createFolder($folder);
        if(!is_file($folder.".htaccess")){
            file_put_contents($folder.".htaccess", "deny from all");
        }
        if(is_file($path)){
            unlink($path);
        }
        file_put_contents($path, $text);
    }

    protected static function getClassDescription(OrmTable $table){
        $fileName = $table->getPersistClassName().ORM::EXTEND;
        return self::getTemplate("class.description", array('fileName' => $fileName));
    }

    protected static function getTextForBindVariable(OrmTableBind $bind, $postfix)
    {
        $type = $bind->getType();
        if($type == OrmTable::MANY_TO_MANY || $type == OrmTable::ONE_TO_MANY) {
            $typePrefix = "[]";
        } else {
            $typePrefix = "";
        }
        return self::getTemplate(
            'bind.variable',
            array('bind' => $bind, 'postfix' => $postfix, 'typePreffix' => $typePrefix)
        );
    }

    protected static function getTextForVariable(OrmTableField $field, $postfix)
    {
        return self::getTemplate(
            'text.for.variable',
            array(
                'field' => $field,
                'enumValues'=> $field->getEnumValues(),
                'name' => $field->getName().$postfix,
                'type' => OrmInstall::defineFieldType($field)
            )
        );
    }

    protected static function getTextForBindGetter(OrmTableBind $bind, $postfix)
    {
        $type = $bind->getType();
        if($type == OrmTable::MANY_TO_MANY || $type == OrmTable::ONE_TO_MANY) {
            $typePrefix = "[]";
        } else {
            $typePrefix = "";
        }
        $name = $bind->getLeftField();
        return self::getTemplate(
            'text.for.bind.getter',
            array(
                'bind' => $bind,
                'postfix' => $postfix,
                'type' => $type,
                'typePreffix' => $typePrefix,
                'name' => $name,
                'lazyLoadText' => $bind->getLazyLoad() 
                    ? OrmInstall::getLazyLoadTextForBind($bind, $postfix) 
                    : ''
            )
        );
       
    }
    protected static function getTextForBindSetter(OrmTableBind $bind)
    {
        return self::getTemplate(
            'text.for.bind.setter',
            array(
                'typePrefix' => (
                    $bind->getType() == OrmTable::MANY_TO_MANY
                    || $bind->getType() == OrmTable::ONE_TO_MANY ? '[]' : ''
                ),
                'name' => $bind->getLeftField(),
                'persistClassName' => $bind->getRightTable()->getPersistClassName(),
            )
        );
    }

    protected static function getTextForFieldGetter(OrmTableField $field, OrmTable $table, $postfix)
    {
        $name = $field->getName().$postfix;
        $type = OrmInstall::defineFieldType($field);
        return self::getTemplate(
            'text.for.getter',
            array(
                'name' => $name,
                'type' => $type,
                'lazyLoadText' => $field->getLazyLoad()
                    ? OrmInstall::getLazyLoadTextForField($field, $table, $postfix)
                    : ""
            )
        );
    }


    protected static function getLazyLoadTextForBind(OrmTableBind $bind, $postfix){
        return self::getTemplate(
            'lazy.load.binded',
            array(
                'propertyName' => $bind->getLeftField(),
                'bind' => $bind,
                'postfix' => $postfix
            )
        );
    }

    protected static function getLazyLoadTextForField(OrmTableField $field, OrmTable $table, $suffix){
        return self::getTemplate(
            'lazy.load.field',
            array(
                'propertyName' => $field->getName().$suffix,
                'field' => $field,
                'table' => $table
            )
        );
    }

    protected static function getTextForSetter(OrmTableField $field, OrmTable $table, $suffix)
    {
        $name = $field->getName().$suffix;
        $type = OrmInstall::defineFieldType($field);
        $enumValues = $field->getEnumValues();
        return self::getTemplate(
            'text.for.setter',
            array(
                'name' => $name,
                'type' => $type,
                'enumValues' => (!empty($enumValues) ? " enum ['".implode("', '",$enumValues)."']" : "" ),
                'className' => $table->getPersistClassName(),
            )
        );
    }

    protected static function createFolder($folder){
        if(!is_dir($folder)){
            mkdir($folder, 0777, true);
        }
    }

    /**
     * @param $field OrmTableField
     * @param $binds OrmTableBind[]
     * @return null|OrmTableBind
     */
    private static function getBindForField($field, $binds)
    {
        foreach ($binds as $bind) {
            if ($bind->getLeftKey() == $field->getName()) {
                return $bind;
            }
        }
        return null;
    }

    private static function defineFieldType(OrmTableField $field)
    {
        $type = $field->getType();
        $string = array('varchar', 'enum', 'text');
        if(in_array($type, $string)){
            return "string";
        }
        $boolean = array('boolean', 'tinyint', 'bit');
        if(in_array($type, $boolean)){
            return "boolean";
        }
        $boolean = array('integer');
        if(in_array($type, $boolean)){
            return "integer";
        }
        return 'undefined';
    }

    /**
     * Get method getter for primary key
     * @param $field OrmTableField
     * @param $postfix string
     * @return string
     */
    private static function getTextForPrimaryKey($field, $postfix)
    {
        return self::getTemplate(
            'text.for.primary.key',
            array(
                'field' => $field,
                'postfix' => $postfix
            )
        );
    }

    private static function isIndexExist($tableName, $fieldName)
    {
        $query = "SHOW INDEXES FROM ".$tableName;
        $indexes = DB::getTable($query);
        foreach($indexes as $index) {
            if($index['Column_name'] == $fieldName) {
                return true;
            }
        }
        return false;
    }

    private static function getTemplate($templateName, $data)
    {
        ob_start();
        include  Core::MODULES_FOLDER.'orm/install/'.$templateName.".php";
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    /**
     * Make all necessary preparations for this module
     * as PersistTables deploy, cache files creating etc..
     * @return void
     */
    public function install()
    {
        //do nothing
    }

    /**
     * Return information about this module
     * @return string
     */
    public function getInfo()
    {
        ob_start();
        echo '<h3>ForWeb framework ORM package</h3>';
        echo "<p>Object relationship mapper package. Main idea of this package is in class ORM_Table.</p>";
        echo "<p>Each instance of this class are equal to database table. It contain ORM_Table_Field list,"
            ." and ORM_Table_Bind list. Field is equal to database field, and bind is equal to data base join.</p>";
        echo "<p>As usual, ORM tables instances stored as serialized string in cache folder. Use for it"
            ." ORM::saveTable() method. This method do 3 actions :</p>"
            ."<ul>"
            ."<li>save table and all binded tables to file</li>"
            ."<li>create table in database, if it not exist</li>"
            ."<li>Create persistence classes for this and each binded table.</li>"
            ."</ul>"
            ."<p>Each auto-generated class is equal to one row from table, and contain all necessary field</p>";
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    /**
     * get module dependencies for deploy
     * @return ModuleDependency[]
     */
    public function getDependencies()
    {
        return array(new ModuleDependency("DB"));
    }

    /**
     * Get user input group
     * @return ModuleInput
     */
    public function getUserInput()
    {
        // TODO: Implement getUserInput() method.
    }
}