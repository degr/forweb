<?php
class ORM_Install implements Module_IInstall{

    public static function createDBTable(ORM_Table $table){
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
            /* @var $field ORM_Table_Field */
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
                if(!ORM_Install::isIndexExist($table->getName(), $field->getName())) {
                    $query = 'ALTER TABLE ' . $table->getName() . ' ADD INDEX ' . $field->getName() . '(' . $field->getName() . ')';
                    DB::query($query);
                }
            }
        }
    }

    private static function getDefaultValueForField(ORM_Table_Field $field){
        $intTypes = array('integer', 'boolean', 'bit');
        if(!in_array($field->getType(), $intTypes)) {
            $defaultValue = "'".DB::escape($field->getDefaultValue())."'";
        } else {
            $defaultValue = $field->getDefaultValue();
        }
        return $defaultValue;
    }

    public static function serializeTable(ORM_Table $table){
        $data = serialize($table);
        $folder = ORM::getTablesFolder();
        if(!is_dir($folder)){
            ORM_Install::createFolder($folder);
        }
        $filename = $folder.$table->getName().".data";
        if(is_file($filename)){
            unlink($filename);
        }
        file_put_contents($filename, $data);
    }

    public static function createPersistanceClass(ORM_Table $table){
        $text = "<?php \n"
            .ORM_Install::getClassDescription($table)
            ."\nclass ".$table->getPersistClassName()." extends ORM_Persistence_Base {\n";

        $binds = $table->getBinds();
        //write all class properties, including binded properties and their doc
        /* @var $field ORM_Table_Field */

        foreach($table->getFields() as $field) {
            $currentBind = ORM_Install::getBindForField($field, $binds);
            if(!empty($currentBind)){
                $postfix = $currentBind->getLeftField() === $field->getName() ? ORM_Utils::BIND_PREFIX : "";
                $text .= ORM_Install::getTextForBindVariable($currentBind, $postfix);
            } else {
                $postfix = "";
            }
            $text .= ORM_Install::getTextForVariable($field, $postfix);
        }

        //write all class setters and getters, include binded
        /* @var $field ORM_Table_Field */
        foreach($table->getFields() as $field) {
            /* @var $bind ORM_Table_Bind */
            $currentBind = ORM_Install::getBindForField($field, $binds);

            if(!empty($currentBind)){
                $text .= "//////////////Persist object " . $field->getName(). "  getter and setter ///////////////\n";
                $postfix = $currentBind->getLeftField() === $field->getName() ? ORM_Utils::BIND_PREFIX : "";
                $text .= ORM_Install::getTextForBindGetter($currentBind, $postfix);
                $text .= ORM_Install::getTextForBindSetter($currentBind);
            } else {
                $postfix = "";
            }
            $text .= "//////////////" . $field->getName().$postfix . " getter and setter ///////////////\n";
            $text .= ORM_Install::getTextForFieldGetter($field, $table, $postfix);
            $text .= ORM_Install::getTextForSetter($field, $table, $postfix);
            if($field->getPrimary()) {
                $text .= ORM_Install::getTextForPrimaryKey($field, $postfix);
            }
        }
        $text .= "}";
        $folder = ORM::getPersistObjectsFolder();
        $path = $folder.$table->getPersistClassName().".php";
        ORM_Install::createFolder($folder);
        if(!is_file($folder.".htaccess")){
            file_put_contents($folder.".htaccess", "deny from all");
        }
        if(is_file($path)){
            unlink($path);
        }
        file_put_contents($path, $text);
    }

    protected static function getClassDescription(ORM_Table $table){
        $fileName = $table->getPersistClassName().ORM::EXTEND;
        return "/**\n"
            ." * Warning! Auto-generated code.\n"
            ." * Do not modify by hands, use ORM install script.\n"
            ." * If you need to extend class,\n"
            ." * crete it in file '".ORM::getPersistExtendedObjectsFolder().$fileName.".php'\n"
            ." * with class name: '".$fileName."'.\n"
            ." * All this conditions required by ORM engine.\n"
            ." */";
    }

    protected static function getTextForBindVariable(ORM_Table_Bind $bind, $postfix)
    {
        $out = '';
        $out .="\t/**\n";
        $out .="\t * persist object field for table: "
            ." ".$bind->getRightTable()->getName()."\n";
        $out .= "\t * object bind options: \$this->".$bind->getLeftField().$postfix." on "
            .$bind->getRightTable()->getPersistClassName()."->".$bind->getRightField(). "\n";
        $type = $bind->getType();
        if($type == ORM_Table::MANY_TO_MANY || $type == ORM_Table::ONE_TO_MANY) {
            $typePrefix = "[]";
        } else {
            $typePrefix = "";
        }

        $out .="\t * @var ".$bind->getRightTable()->getPersistClassName().$typePrefix." $".$bind->getLeftField()."\n";
        $out .="\t */\n";
        $out .="\tprotected $".$bind->getLeftField().";\n";
        return $out;
    }

    protected static function getTextForVariable(ORM_Table_Field $field, $postfix)
    {
        $enumValues = $field->getEnumValues();
        $name = $field->getName().$postfix;
        $type = ORM_Install::defineFieldType($field);
        $out = '';
        $out .="\t/**\n";
        $out .="\t * persist object field"
            .(!empty($enumValues) ? " enum ['".implode("', '",$enumValues)."']" : "" )
            .($field->getPrimary() ? ", primary key" : "" )
            .($field->getAutoIncrement() ? ", autoincrement" : "" )."\n";
        $out .="\t * @var ".$type." $".$name."\n";
        $out .="\t */\n";
        $out .="\tprotected $".$name.";\n";
        return $out;
    }

    protected static function getTextForBindGetter(ORM_Table_Bind $bind, $postfix)
    {
        $type = $bind->getType();
        if($type == ORM_Table::MANY_TO_MANY || $type == ORM_Table::ONE_TO_MANY) {
            $typePrefix = "[]";
        } else {
            $typePrefix = "";
        }

        $name = $bind->getLeftField();
        $text ="\t/**\n";
        $text .="\t * `".$name."` field getter\n";
        $text .="\t * @return ".$bind->getRightTable()->getPersistClassName().$typePrefix."\n";
        $text .="\t */\n";

        $text .= "\tpublic function get" . ucfirst($bind->getLeftField()) . "(){\n";
        if ($bind->getLazyLoad()) {
            $text .= ORM_Install::getLazyLoadTextForBind($bind, $postfix);
        }
        $text .= "\t\treturn \$this->" . $bind->getLeftField() . ";\n";
        $text .="\t}\n";
        return $text;
    }
    protected static function getTextForBindSetter(ORM_Table_Bind $bind)
    {
        $type = $bind->getType();
        if($type == ORM_Table::MANY_TO_MANY || $type == ORM_Table::ONE_TO_MANY) {
            $typePrefix = "[]";
        } else {
            $typePrefix = "";
        }

        $persistClassName = $bind->getRightTable()->getPersistClassName();
        $name = $bind->getLeftField();
        $text ="\t/**\n";
        $text .="\t * `".$name."` field setter\n";
        $text .="\t * @var ".$persistClassName.$typePrefix." $".$name."\n";
        $text .="\t * @return ".$bind->getLeftTable()->getPersistClassName()."\n";
        $text .="\t */\n";
        $text .="\tpublic function set".ucfirst($name)."($".$name."){\n";
        $text .="\t\t\$this->".$name." = $".$name.";\n";
        $text .="\t\treturn \$this;\n";
        $text .="\t}\n\n";
        return $text;
    }

    protected static function getTextForFieldGetter(ORM_Table_Field $field, ORM_Table $table, $postfix)
    {
        $name = $field->getName().$postfix;
        $type = ORM_Install::defineFieldType($field);
        $text = ORM_Install::getCommentText($name, $type);
        $text .= "\tpublic function get" . ucfirst($name) . "(){\n";
        if ($field->getLazyLoad()) {
            $text .= ORM_Install::getLazyLoadTextForField($field, $table, $postfix);
        }
        $text .= "\t\treturn \$this->" . $name . ";\n";
        $text .="\t}\n";
        return $text;
    }




    protected static function getCommentText($name, $type){
        $text = "\t/**\n";
        $text .= "\t * `" . $name . "` field getter\n";
        $text .= "\t * @return " . $type . " $" . $name . "\n";
        $text .= "\t */\n";
        return $text;
    }

    protected static function getLazyLoadTextForBind(ORM_Table_Bind $bind, $postfix){
        $propertyName = $bind->getLeftField();
        $text = "\t\tif(\$this->" . $propertyName . " === null){\n";
        $text .= "\t\t\t\$this->" . $propertyName . " = ORM::loadBinded('"
            . $bind->getRightTable()->getName()
            . "', \$this->get".ucfirst($bind->getLeftKey()).$postfix."(), '"
            . $bind->getLeftKey() . "', '"
            . $bind->getRightKey() . "', '"
            . $bind->getType() . "');\n";
        $text .= "\t\t}\n";
        return $text;
    }

    protected static function getLazyLoadTextForField(ORM_Table_Field $field, ORM_Table $table, $suffix){
        $propertyName = $field->getName().$suffix;


        $text = "\t\tif(\$this->" . $propertyName . " === null){\n";
        $text .= "\t\t\t\$this->" . $propertyName . " = ORM::loadField('"
            . $table->getName()
            . "', \$this->getPrimaryKey(), '"
            . $propertyName ."');\n";
        $text .= "\t\t}\n";
        return $text;
    }

    protected static function getTextForSetter(ORM_Table_Field $field, ORM_Table $table, $suffix)
    {
        $name = $field->getName().$suffix;
        $type = ORM_Install::defineFieldType($field);
        $text ="\t/**\n";
        $text .="\t * `".$name."` field setter\n";
        $text .="\t * @var ".$type." $".$name
            .(!empty($enumValues) ? " enum ['".implode("', '",$enumValues)."']" : "" )."\n";
        $text .="\t * @return ".$table->getPersistClassName()."\n";
        $text .="\t */\n";
        $text .="\tpublic function set".ucfirst($name)."($".$name."){\n";
        $text .="\t\t\$this->".$name." = $".$name.";\n";
        $text .="\t\treturn \$this;\n";
        $text .="\t}\n\n";
        return $text;
    }

    protected static function createFolder($folder){
        $parts = explode("/", $folder);
        $path = "";
        foreach($parts as $part){
            if($path === "") {
                $path .= $part;
            } else {
                $path .= "/".$part;
            }
            if(!is_dir($path)){
                echo "creating folder $path <br/>";
                mkdir($path, 0777);
            }
        }
    }

    /**
     * @param $field ORM_Table_Field
     * @param $binds ORM_Table_Bind[]
     * @return null|ORM_Table_Bind
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

    private static function defineFieldType(ORM_Table_Field $field)
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
     * @param $field ORM_Table_Field
     * @param $postfix string
     * @return string
     */
    private static function getTextForPrimaryKey($field, $postfix)
    {
        $text = "\t/**\n";
        $text .= "\t * Primary key getter\n";
        $text .= "\t */\n";

        $text .= "\tpublic function getPrimaryKey(){\n";
        $text .= "\t\treturn \$this->" . $field->getName().$postfix . ";\n";
        $text .= "\t}\n";
        $text .= "\t\n";
        return $text;
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
     * @return Module_Dependency[]
     */
    public function getDependencies()
    {
        return array(new Module_Dependency("DB"));
    }
}