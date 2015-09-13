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
     * @return ModuleAjaxHandler[]
     */
    public function getAjaxHandlers()
    {
        if($this->ajaxHandlers == null){
            $this->ajaxHandlers = array(
                'search' => new ModuleAjaxHandler('onAjaxSearch', ModuleAjaxHandler::JSON)
            );
        }
        return $this->ajaxHandlers;
    }

    /**
     * Get module event handlers
     * @return ModuleEventHandler[]
     */
    public function getEventHandlers()
    {
        // TODO: Implement getEventHandlers() method.
    }

    public function onSearch(UI $ui){
        /** @var $dispatcher PageDispatcher */
        if(empty($_REQUEST['search'])) {
            if(Core::getPathParam(0) !== "") {
                CoreRedirect::redirectToHome();
            }
            return;
        }
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 0;
        /** @var $service SearchService */
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
        /** @var $service SearchService */
        $service = $this->getService();
        $search = $service->onPageSearch($_POST['search'], $page);
        return array(
            'search' => $search,
            'page' => $page
        );
    }


}