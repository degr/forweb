<?php
session_start();
$debug = true;


require_once "modules/module/Module.php";
require_once "modules/core/Core.php";
$manager = new DB_Manager("", "MySQL");
$manager->setCredentials("localhost", "root", "", "php_db");
DB::init($manager);




if($_GET['init'] == 1){
    $coreInstall = new Core_Install();
    $coreInstall->run();
    exit;
}


$core = Core::getInstance();
$core->process();
DB::close();


exit;


function __autoload($class) {
    if (strpos($class, '_') !== false) {
        $path = strtolower($class);
        $parts = explode('_', $path);
        array_pop($parts);
        $name = substr($class, strrpos ($class, "_")+1, strlen($class));
        if(count($parts) !== 0) {
            $name = '/'.$name;
        }
        $file = Core::MODULES_FOLDER.implode('/', $parts).$name.'.php';
    } else {
        $file = Core::MODULES_FOLDER.lcfirst($class).'/'.$class.'.php';
        if(!is_file($file)){
            $file = Core::MODULES_FOLDER.$class.'.php';
        }
    }
    if(!is_file($file)){
        if(is_file(ORM::getPersistExtendedObjectsFolder().$class.".php")){
            require_once ORM::getPersistObjectsFolder().$class.".php";
            return;
        }
        if(is_file(ORM::getPersistObjectsFolder().$class.".php")){
            require_once ORM::getPersistObjectsFolder().$class.".php";
            return;
        }
    }
    require_once($file);
}

function includeFiles($folder){
    $files = glob($folder.'/*');
    //print_r ($files);
    $folders = array();
    foreach($files as $file) {
        if(is_file($file)){
            //echo $file."<br/>";
            include $file;
        }else{
            $folders[] = $file;
        }
    }
    foreach($folders as $innerFolder){
        includeFiles($innerFolder);
    }
}
function debug($message, $no_table=false, $no_line=false){
    global $debug;
    static $count;
    if(empty($count)){
        $count = 1;
    }else{
        $count++;
    }
    if($count > 300){
        return;
    }


    if(!$no_table){
        echo "<style>";
        echo "table.debug td{padding: 3px 5px; border: 1px dotted lightgray;}";
        echo "table.debug.array td.key{background-color: blanchedalmond;}";
        echo "table.debug.null td.null{color: red;}";
        echo "table.debug.object > tbody > tr > td.key, table.debug.object > tr > td.key{background-color: lightblue;}";

        echo "</style>";
    }
    if($debug){
        $type = gettype($message);
        $no_table = $no_table && ($type != 'object' && $type != 'array');
        if(!$no_line) {

            $trace = debug_backtrace();

            $file = $trace[0]['file'];
            $func = '';
            $line = $trace[0]['line'];
            $class = null;
            if($trace[1]){
                $func = $trace[1]['function'];
                $class= $trace[1]['class'];
            }

            $c = $file . ": <br/>"
             .($class != '' ? $class . " -> " : "")
             .($func != '' ? $func . "(): " : "")
             ." on line: ".$line." ";
            echo ($c);
        }
        if(!$no_table )
            echo '<table class="debug '.$type.'">';


        switch($type){
            case "NULL":
                echo (!$no_table) ? '<td class="null">NULL</td>' : 'NULL';
                break;
            case "boolean":
                $message = "boolean: ".($message ? "true" : "false");
                /* all ok */
            case "string":
                if(empty($message)){
                    $message = "[empty string]";
                }
                //all ok
            case "integer":

                echo (!$no_table) ? "<td>".$message."</td>" : $message;
                break;
            case 'object':
                $class = get_class($message);
                echo '<tr><th>class: '.$class.'<th><th></th></tr>';
                $methods = get_class_methods($class);
                foreach($methods as $key => $value){
                    echo '<tr><td class="key">'.$value."</td><td></td></tr>";
                }
                if(in_array('toArray', $methods)){
                    echo '<tr><td class="key">'.$value."</td><td>";
                    debug($message->toArray());
                    echo "</td></tr>";
                }
                // all ok
            case "array":
                foreach($message as $key => $value){
                    echo '<tr><td class="key">'.$key."</td><td>";
                    debug($value, true, true);
                    echo "</td></tr>";
                }
                break;
            default:
                echo "<pre>".gettype($message)."\n";var_dump($message);echo "</pre>";
        }
        if(!$no_table)
            echo "</table>";

    }
}


?>