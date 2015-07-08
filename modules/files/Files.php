<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 21.03.2015
 * Time: 14:13
 */
class Files extends Module{
    /**
     * Get module ajax handlers
     * @return AjaxHandler[]
     */
    public function getAjaxHandlers()
    {
        if($this->ajaxHandlers == null) {
            $this->ajaxHandlers = array(
                'getAjaxUserMedia' => new AjaxHandler('getAjaxUserMedia', AjaxHandler::TEXT),
                'updateTextFile' => new AjaxHandler('updateTextFile', AjaxHandler::TEXT),
                'showFileContent' => new AjaxHandler('showFileContent', AjaxHandler::TEXT),
                'adminDeleteFile' => new AjaxHandler('adminDeleteFile', AjaxHandler::TEXT),
                'adminNewFile' => new AjaxHandler('adminNewFile', AjaxHandler::TEXT),
                'adminUploadFile' => new AjaxHandler('adminUploadFile', AjaxHandler::JSON)//@TODO fix this. In past it was FormHandler
            );
        }
        return $this->ajaxHandlers;
    }

    /**
     * download file from url to selected location
     * @param $url string
     * @param $folder string
     * @return string file path
     */
    public static function download($url, $folder){

    }

    /**
     * Extract archive, and put all content into selected folder
     * @param $archive string - path to archive
     * @param $folder - path to folder
     */
    public static function extract($archive, $folder)
    {
    }

    public function getAjaxUserMedia(){
        Access::denied("can_modify_files");
        $provider = new Files_Admin();
        return $provider->getAjaxUserMedia();
    }
    public function showFileContent(){
        Access::denied("can_modify_files");
        $provider = new Files_Admin();
        return $provider->showFileContent();
    }
    public function updateTextFile(){
        Access::denied("can_modify_files");
        $provider = new Files_Admin();
        return $provider->updateTextFile();
    }

    public function adminUploadFile(){
        Access::denied('can_modify_files');
        $provider = new Files_Admin();
        $provider->adminUploadFile($handler);//todo fix from from handler to ajax handler
    }
    public function adminDeleteFile(){
        Access::denied('can_modify_files');
        $provider = new Files_Admin();
        return $provider->adminDeleteFile();
    }
    public function adminNewFile(){
        Access::denied('can_modify_files');
        $provider = new Files_Admin();
        return $provider->adminNewFile();
    }

    /**
     * Get module event handlers
     * @return EventHandler[]
     */
    public function getEventHandlers()
    {
        // TODO: Implement getEventHandlers() method.
    }
}