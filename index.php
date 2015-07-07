<?php
//$time = time();
session_start();
$debug = true;



require_once "modules/project.functions.php";

require_once "modules/module/Module.php";
require_once "modules/core/Core.php";

/*$provider = new Api_Provider("user");
$_REQUEST['filter'] = json_encode(array(
    array('type'=>'filter', 'field'=>'surname', 'comparation' => '='),
    array('type'=>'group', 'operator' => 'or', 'filters' => array(
        array('type'=>'filter', 'field'=>'name', 'comparation' => 'like', 'value' => 'lol'),
        array('type'=>'filter', 'field'=>'flag', 'comparation' => '=', 'value' => 'A'),
    ))
));
$_REQUEST['order'] = json_encode(array(
    array('field'=>'name', 'direction' => 'desc'),
    array('field'=>'rand()', 'direction' => 'desc')
));
$_REQUEST['page'] = 3;
echo json_encode($_REQUEST);
exit;
echo $provider->getFilterCondition().$provider->getOrder().$provider->getLimit();
exit;*/
$manager = new DB_Manager("", "MySQL");
$manager->setCredentials("localhost", "root", "admin", "forweb");
DB::init($manager);

if($_GET['init'] == 1 && Core::DEVELOPMENT){
    $coreInstall = new Core_Install();
    $coreInstall->run();
    exit;
}

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