<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 19.04.2015
 * Time: 15:16
 */

/**
 * <pre>
 * Project autoload function.
 * Class with name 'Page' must be in modules/page/Page.php file.
 * If class have name Page_Service it must be in modules/page/Service.php file
 * If class have name Page_Admin_Includes, it must be in modules/page/Admin/Includes.php file
 *
 * If class have name Page, but there is no modules/page/Page.php file, function will try
 * to search it in modules/Page.php. If there is no that file, exception will be thrown on require_once construction
 *
 * Also, this function used for ORM persistance class loading
 * If no one of previous condition was executted, it will try to search your file in
 * ORM::getPersistExtendedObjectsFolder().$class.".php" for extended persistance classes, and
 * ORM::getPersistObjectsFolder().$class.".php" for autogenerated classes
 *
 * </pre>
 * @param string $class
 */
function __autoload($class) {
    if (strpos($class, '_') !== false) {
        $path = strtolower($class);
        $parts = explode('_', $path);
        $lowered = array_pop($parts).'.php';
        $folder = Core::MODULES_FOLDER.implode('/', $parts).'/';
        $files = glob($folder.'*');
        foreach($files as $file) {
            if(strtolower($file) == $folder.$lowered) {
                require_once $file;
                return;
            }
        }
        $file = 'undefined class: '.$class;
    } else {
        $file = Core::MODULES_FOLDER.strtolower($class).'/'.$class.'.php';
        if(!is_file($file)){
            $file = Core::MODULES_FOLDER.$class.'.php';
        }
    }
    if(!is_file($file)){
        if(is_file(ORM::getPersistExtendedObjectsFolder().$class.".php")){
            require_once ORM::getPersistExtendedObjectsFolder().$class.".php";
            return;
        }
        if(is_file(ORM::getPersistObjectsFolder().$class.".php")){
            require_once ORM::getPersistObjectsFolder().$class.".php";
            return;
        }
    }
    $c = explode('/', $file);
    array_shift($c);
    checkFolder('modules', implode('/', $c), $file);
    require_once($file);
}

function checkFolder($base, $file, $im) {
    $parts = explode('/', $file);
    if(count($parts) < 2) {
        return;
    }
    $current = array_shift($parts);
    $folders = glob($base.'/*');
    $ok = false;
    foreach($folders as $folder) {
        if(!is_dir($folder)) {
            continue;
        }
        if($folder == $base.'/'.$current) {
            $ok = true;
            break;
        }
    }
    if($ok) {
        if(count($parts) > 1) {
            checkFolder($base.'/'.$current, implode('/', $parts), $im);
        }
    } else {
        echo 'not ok'.$base.'/'.$file.'::'.$im.'<br>';
    }
}

/**
 * Include all files from folder and all files from subfolders.
 * @param $folder
 */
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

/**
 * Debug function
 * @param $message - any object
 * @param bool $no_table - internal variable
 * @param bool $no_line - internal variable
 */
function debug($message, $no_table=false, $no_line=false){
    $debug = Core::DEVELOPMENT;
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
                    debug($message->toJson());
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

