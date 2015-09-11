<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 9/11/2015
 * Time: 1:03 PM
 */
class CorePageContent{

    /**
     * @param $dispatcher PageDispatcher
     */
    public function onPageContent($pathParams){
        $this->pageModule = Core::getModule("Page");
        $core = Core::getInstance();

        /* @var $pageService PageService */
        $pageService = Core::getModule("Page")->getService();
        $page = $pageService->findPage($pathParams);
        $pageService->setCurrentPage($page);
        $template = $pageService->getTemplate();
        $blocks = $core->getBlocks($template->getId());
        $pageData = $this->processBlocks($blocks);
        $this->sendResponse($pageData, $template);

        if(isset($_GET['force_admin_panel'])) {
            $ui = new UI();
            /** @var $cms Cms */
            $cms =Core::getModule("Cms");
            $cms->getAdminPanel($ui);
            echo $ui->process();
        }
    }

    protected function processBlocks($blocks){
        $core = Core::getInstance();
        /* @var $pageService PageService */
        $pageService = Core::getModule("Page")->getService();
        $includes = $core->getPageIncludes($pageService->getCurrentPage());
        $out = array();
        $data = array();
        if(!empty($includes)) {
            /* @var $include PersistIncludes */
            foreach ($includes as $include) {
                if (empty($data[$blocks[$include->getBlock()]])) {
                    $data[$blocks[$include->getBlock()]] = array(
                        "before" => array(),
                        "template" => array(),
                        "after" => array()
                    );
                }
                if(!empty($data[$blocks[$include->getBlock()]][$include->getPosition()][$include->getPositionNumber()])) {
                    $include->setPositionNumber($include->getPositionNumber() +100);
                }
                $data[$blocks[$include->getBlock()]][$include->getPosition()][$include->getPositionNumber()] =
                    $this->processInclude($include);
            }
        }
        //iterate over each block
        foreach($data as $key => $block) {
            if(empty($out[$key])) {
                $out[$key] = "";
            }
            //iterate over positions: before, template, after
            foreach($block as $positionedIncludes){
                //iterate over includes
                $keys = (array_keys($positionedIncludes));
                sort($keys);
                foreach($keys as $incKey) {
                    $out[$key] .= $positionedIncludes[$incKey];
                }
            }
        }
        $out['scriptCollector'] = ScriptCollector::get();
        return $out;
    }


    protected function sendResponse($pageData, PersistTemplates $template){
        $ui = new UI();
        $ui->addVariable("block", $pageData);
        $ui->setLayout($template->getTemplate());
        header('Content-Type: text/html; charset=utf-8');
        echo $ui->process();
    }


    protected function processInclude(PersistIncludes $include){
        /*if(Core::DEVELOPMENT && isset($_GET['dbug_time']) && Core::isModuleExist("Debug")){
            $time = time();
        }*/
        switch($include->getType()){
            case "text":
                $out = "\t<p>".htmlspecialchars(Core::getIncludeStaticContent($include))."</p>";
                break;
            case "html":
                $out = Core::getIncludeStaticContent($include)."\n";
                break;
            case "image":
                $out = '<div class="image-holder image-holder-'.$include->getId().'">'
                    .'<img src="'.$include->getContent().'" alt="content_image" title="content_image" />'
                    .'</div>';
                break;
            case "executable":
                $module = $include->getModule();
                $method = $include->getMethod();
                if(empty($module) || empty($method) || !Core::isModuleExist($module)) {
                    $out = Core::DEVELOPMENT ? '[undefined module: '.$module.']' : '';
                } else {
                    $object = Core::getModule($module);
                    if(method_exists($object, $method) ) {
                        $ui = new UI();
                        $object->$method($ui);
                        $out = $ui->process();
                    } else {
                        $out = Core::DEVELOPMENT ? '[undefined mehod: '.$module.'::'.$method.']' : '';
                    }
                }
                break;
            default:
                throw new Exception("Unknown include type ".$include->getType());

        }

        /*if(Core::DEVELOPMENT && isset($_GET['dbug_time']) && Core::isModuleExist("Debug")){
            $out = Debug::getIncludeExecutionTime($include, $time);
        }*/

        return $out;
    }
}