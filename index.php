<?php
//$time = time();
session_start();
define('TIME', microtime(true));
/*main requirements*/
require_once "modules/project.functions.php";
require_once "modules/module/Module.php";
require_once "modules/core/Core.php";

/* css style building */
if(Core::DEVELOPMENT && $_GET['scss'] == 1 || !is_file('css/compilled.css')) {
    $scss = new Scss_Server('templates/scss', 'cache/scss');
    $scss->serve();
}
/* db initializing */
$manager = new DB_Manager("", "MySQL");
$manager->setCredentials("localhost", "root", "", "roguelike");
DB::init($manager);

/* project deploy */
if($_GET['deploy'] == 1 && Core::DEVELOPMENT){
    $coreInstall = new Core_Install();
    $coreInstall->run();
    exit;
}
/* page generation */
$core = Core::getInstance();
$core->process();
DB::close();

function convert($size)
{
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}
/*
echo "time: ".(time()-$time);
echo "<br>";
echo "memory: ".convert(memory_get_usage(true));
*/
exit;
?>