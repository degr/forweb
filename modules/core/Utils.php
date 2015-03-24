<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 24.03.2015
 * Time: 20:15
 */

class Core_Utils {

    public static function redirectToHome()
    {
        $url = Config::get('url');
        return Core_Utils::redirect($url);
    }

    public static function redirect($url){
        if(empty($url)) {
            $params = parse_url($_SERVER['HTTP_REFERER']);
            $url = $params['scheme']."://".$params['host']."/";
        }
        header('location: '.$url);
        exit;
    }

}