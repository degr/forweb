<?php
//$time = time();
session_start();
$debug = true;


require_once "modules/project.functions.php";
require_once "modules/module/Module.php";
require_once "modules/core/Core.php";
$manager = new DB_Manager("", "MySQL");
$manager->setCredentials("localhost", "root", "", "forweb");
DB::init($manager);

if($_GET['init'] == 1){
    $coreInstall = new Core_Install();
    $coreInstall->run();
    exit;
}
$a = 'aaaaaa';

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