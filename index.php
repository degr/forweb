<?php
//$time = time();
session_start();
define('TIME', microtime(true));
/*main requirements*/
require_once 'modules/module/Module.php';
require_once 'modules/core/Core.php';
$core = Core::getInstance();
spl_autoload_register(array($core, 'autoload'));

/* css style building */
if(Core::DEVELOPMENT && $_GET['scss'] == 1 || !is_file('css/compilled.css')) {
    $scss = new ScssServer('templates/scss', 'cache/scss');
    $scss->serve();
}
/* db initializing */
$manager = new DbManager("", "Mysql");
$manager->setCredentials("localhost", "root", "", "forweb");
DB::init($manager);
DB::setEncoding("utf8");

/* project deploy */
if($_GET['deploy'] == 1 && Core::DEVELOPMENT){
    $coreInstall = new CoreInstall();
    $coreInstall->run();
    exit;
}
/* page generation */
$core->process();
DB::close();
?>