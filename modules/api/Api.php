<?php

/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/2/2015
 * Time: 10:06 AM
 */

/**
 * this class handle this requests: http://site.loc/api/user/getByName?name=John
 *
 * There is two types of behavior - custom
 * Class Api
 */
class Api
{
    const OPEN_API = true;
    const DEFAULT_LIMIT = 15;
    const DEFAULT_PK_NAME = "id";


    const METHOD_UPDATE = "update";
    const METHOD_INSERT = "add";
    const METHOD_DELETE = "delete";
    const METHOD_SELECT = "show";



    public function handleRequest()
    {
        $table = Core::getPathParam(1);
        if(empty($table)) {
            throw new FwException("Undefined table in request path. Please specify table as second request param.");
        }

        /** @var $provider ApiProvider */
        if(is_file(Core::MODULES_FOLDER."api/provider/".ucfirst($table).".php")) {
            $class = "Api_Provider_".ucfirst($table);
            $provider = new $class();
        } else {
            $provider = new ApiProviderImpl($table);
        }
        $method = Core::getPathParam(2);
        if(empty($method)) {
            throw new FwException("Undefined method in request path. Please method name as third request param.");
        }
        Cms::sendHeaders(ModuleAjaxHandler::JSON);
        echo json_encode($provider->$method());
    }
}