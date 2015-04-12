<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 04.04.2015
 * Time: 18:58
 */
class ORM_Query_CustomFilter extends  ORM_Query_Filter{

    /**
     * current filter string in format
     * name = 'vasia' AND (role = 'admin' OR role = 'supervisor')
     * @var string
     */
    protected $query;

    public function __construct($query, $active){
        $this->setActive($active);
        $this->query = $query;
    }

    /**
     * Build query string for this item
     * @return int
     */
    public function buildQueryString()
    {
        return $this->query;
    }

}