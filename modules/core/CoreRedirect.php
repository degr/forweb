<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 24.03.2015
 * Time: 20:15
 */

class CoreRedirect {

    public static function redirectToHome()
    {
        $url = CoreConfig::getUrl();
        CoreRedirect::redirect($url);
    }

        public static function redirect($url){
        if(empty($url)) {
            $params = parse_url($_SERVER['HTTP_REFERER']);
            $url = $params['scheme']."://".$params['host']."/";
        }
        header('location: '.$url);
        exit;
    }

    /**
     * Send error as http response
     * @param $code integer
     * @param array $params
     */
    public static function httpError($code, $params = array()){
        switch($code) {
            case 404:
                header("HTTP/1.0 404 Not Found");
                break;
        }

        if(!empty($params['layout'])) {
            $layout = $params['layout'];
        } elseif(is_file(UI::TEMPLATES_DIR."errors/".$code.".tpl")) {
            $layout = UI::TEMPLATES_DIR.$code.".tpl";
        } elseif(is_file(UI::TEMPLATES_DIR."errors/error.tpl")) {
            $layout = UI::TEMPLATES_DIR."errors/error.tpl";
        } else {
            $layout = null;
        }
        if(!empty($layout)) {
            $ui = new UI();
            $ui->setLayout($layout);
            $ui->addVariable('code', $code);
            $ui->addVariable('params', $params);
            echo $ui->process();
        }
        exit;
    }

}