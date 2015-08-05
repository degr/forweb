<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 8/5/2015
 * Time: 12:11 PM
 */
class Search extends Module{

    /**
     * Get module ajax handlers
     * @return AjaxHandler[]
     */
    public function getAjaxHandlers()
    {
        if($this->ajaxHandlers == null){
            $this->ajaxHandlers = array(
                'search' => new AjaxHandler('onAjaxSearch', AjaxHandler::JSON)
            );
        }
        return $this->ajaxHandlers;
    }

    /**
     * Get module event handlers
     * @return EventHandler[]
     */
    public function getEventHandlers()
    {
        // TODO: Implement getEventHandlers() method.
    }

    public function onSearch(UI $ui){
        /** @var $dispatcher Page_Dispatcher */
        if(empty($_REQUEST['search'])) {
            if(Core::getPathParam(0) !== "") {
                Core_Utils::redirectToHome();
            }
            return;
        }
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 0;
        /** @var $service Search_Service */
        $service = $this->getService();
        $searchResult = $service->onPageSearch($_REQUEST['search'], $page);
        $ui->setLayout("search/page.tpl");
        $ui->addVariable("search", $_REQUEST['search']);
        $ui->addVariable("result", $searchResult);
    }

    public function onAjaxSearch(){
        if(empty($_POST['search'])) {
            return null;
        }
        $page = !empty($_POST['page']) ? $_POST['page'] : 0;
        /** @var $service Search_Service */
        $service = $this->getService();
        $search = $service->onPageSearch($_POST['search'], $page);
        return array(
            'search' => $search,
            'page' => $page
        );
    }


}