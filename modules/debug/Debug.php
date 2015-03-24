<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 23.03.2015
 * Time: 13:35
 */
class Debug{


    public static function getIncludeInformation(PersistIncludes $include)
    {
        static $blocks;
        if(empty($blocks)) {
            $blocks = DB::getAssoc("select id, name from blocks", 'id', 'name');
        }
        $static = $include->getType() != 'executable';
        if($static) {
            $pid = $include->getPage();
            $dto = array('type'=>'Include with static content, belong to '
                .(empty($pid) ? 'current page' : 'template'));
        } else {
            $dto = array('type'=>'Dynamic include','Module'=>$include->getModule(),"Method" => $include->getMethod());
        }
        $dto['block'] = $blocks[$include->getBlock()];
        return Debug::buildTable($dto);
    }

    private static function buildTable($object){
        $out = '<table border="solid" style="margin-bottom: 10px;">';
        foreach($object as $key=>$value) {
            $out .= "<tr>";
            $out .= "<td>".$key."</td>";
            $out .= "<td>".$value."</td>";
            $out .= "</tr>";
        }
        $out .= "</table>";
        return $out;
    }

    public static function getSIncludeExecutionTime(PersistIncludes $include, $time)
    {
        $out = array();
        if($include->getType() == 'executable') {
            $out['module'] = $include->getModule();
            $out['method'] = $include->getMethod();
            $out['time'] = time() - $time;
        }
        return Debug::buildTable($out);
    }

}