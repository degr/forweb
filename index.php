<?php
session_start();
define('TIME', microtime(true));
/*main requirements*/
require_once 'modules/module/Module.php';
require_once 'modules/core/Core.php';
$core = Core::getInstance();
spl_autoload_register(array($core, 'autoload'));
spl_autoload_register(array("ORM", 'autoload'));

/* db initializing */
$manager = new DbManager("", "Mysql");
$manager->setCredentials("127.0.0.1", "root", "admin", "forweb");
DB::init($manager);
DB::setEncoding("utf8");

/* project deploy */
if($_GET['deploy'] == 1 && Core::DEVELOPMENT){
    $coreInstall = new CoreInstall();
    $coreInstall->run();
    exit;
}
/* css style building */
if(Core::DEVELOPMENT && $_GET['scss'] == 1 || !is_file('css/compilled.css')) {
    $scss = new ScssServer('templates/scss', 'cache/scss');
    $scss->serve();
}

/* page generation */
$core->process();
DB::close();
echo Core::$incCount;
?>