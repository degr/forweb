<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/2/2015
 * Time: 1:00 PM
 */

/**
 * Class Api_Provider
 *
 */
class Api_Provider implements  Api_IProvider{

    const ORDER = 'order';
    const PAGE = 'page';
    private static $ORDER_DIRECTION = array('asc', 'desc', 'ASC', 'DESC');
    private $table;
    const FILTER = 'filter';

    private static $ALLOWED_COMPARATORS = array('>', '<', '=', 'like', '<>', '!=');
    private static $ALLOWED_OPERATORS = array('and', 'or', 'AND', 'OR');

    public function __construct($table){
        $this->table = $table;
        $this->rowsPerPage = 15;
    }

    public function getAllowedFields()
    {
        return Api::OPEN_API ? array("*") : array();
    }

    public function getDeniedFields()
    {
        return Api::OPEN_API ? array() : array("*");
    }


    public function getCell()
    {

        if (empty($_REQUEST[self::FILTER])) {
            throw new FwException('Can\'t return cell, because query param "filter" is undefined.');
        }
        $result = $this->getColumn();
        return count($result) > 0 ? reset($result) : null;
    }

    public function getRow()
    {
        if (empty($_REQUEST[self::FILTER])) {
            throw new FwException('Can\'t return cell, because query param "filter" is undefined.');
        }
        $result = $this->getList();
        return count($result) > 0 ? reset($result) : null;
    }

    public function getColumn()
    {
        $field = $_REQUEST['field'];
        if(empty($field)) {
            throw new FwException('Malformed request. Unknown field to select, please specify it with key "field".');
        }
        if(!$this->isAllowedField($field)) {
            throw new FwException('This field is forbidden, or not exist.');
        }
        return DB::getColumn("SELECT " . $field . " FROM " . $this->table . $this->getFilterCondition()
            .$this->getOrder().$this->getLimit());
    }

    /**
     * Api function, require q
     * @return mixed
     * @throws FwException
     */
    public function getAssoc()
    {
        $key = $_REQUEST['key'];
        $value = $_REQUEST['value'];

        if(!$this->isAllowedField($key) || !$this->isAllowedField($value)) {
            throw new FwException('This fields is forbidden, or not exist.');
        }
        return DB::getAssoc("SELECT " . $key . ", " . $value . " FROM " . $this->table . $this->getFilterCondition()
            .$this->getOrder().$this->getLimit(),
            $key, $value);
    }

    public function getList()
    {
        $allowed = $this->getAllowedFields();
        $denied = $this->getDeniedFields();
        if(count($allowed) == 0 || in_array("*", $denied)) {
            return null;
        }
        $table = DB::getTable("SELECT ".implode(", ", $allowed)." FROM " . $this->table .$this->getFilterCondition()
            .$this->getOrder().$this->getLimit());
        if(count($denied) > 0) {
            foreach($table as &$row) {
                foreach($denied as $deniedField) {
                    if(isset($row[$deniedField])) {
                        unset($row[$deniedField]);
                    }
                }
            }
        }
        return $table;
    }

    private function isAllowedField($field){
        $allowed = $this->getAllowedFields();
        $denied = $this->getDeniedFields();
        if(in_array("*", $allowed) || in_array($field, $allowed)) {
            return !in_array("*", $denied) && !in_array($field, $denied);
        } else {
            return false;
        }
    }

    public function getFilterCondition()
    {
        if(empty($_REQUEST[self::FILTER])) {
            return '';
        }
        $data = json_decode($_REQUEST[self::FILTER], true);
        if(empty($data) || !is_array($data)) {
            throw new FwException('Malformed filter. Please specify json string, equal to array of filter objects:'
             .'[{type: "filter", field: "name", value: "Rassel", comparation: [on of: "'
                .implode(self::$ALLOWED_COMPARATORS).'"], fieldType:[one of: "int", "string"]},
                {type: "group", operator: [on of: "and", "or"], filters: [array of filters]
             ]');
        }

        $out = array();
        foreach($data as $object) {
            if(empty($object['type'])) {
                throw new FwException("Malformed filter object, type field is required: ".json_encode($object));
            }
            if($object['type'] == 'filter') {
                $out[] = $this->buildFilter($object);
            } elseif($object['type'] == 'group') {
                $out[] = $this->buildFilterGroup($object);
            } else {
                throw new FwException("unknown filter type. Only [filter, group] allowed.");
            }
        }
        $out = array_filter($out);
        return !empty($out) ? ' WHERE '.implode(" and ", array_filter($out)).' ' : '';
    }

    private function buildFilter($filter){
        if(empty($filter['field'])) {
            throw new FwException('Malformed filter. Field "field" is required');
        }

        if(!in_array($filter['comparation'], self::$ALLOWED_COMPARATORS)) {
            throw new FwException('Malformed filter. Field "comparation" is required, or invalid. Please specify one '
                .'of: ['.implode(', ', self::$ALLOWED_COMPARATORS).']');
        }

        if(!$this->isFieldExist($filter['field'])) {
            throw new FwException('Field with name '.$filter['field'].' is not exist.'
                .' Check field name, or api provider url');
        }
        return ' '.$filter['field'].' '.$filter['comparation'].' '.(empty($filter['fieldType']) || $filter['fieldType'] == 'string'
                ? "'".addslashes($filter['value'])."'"
                : intval($filter['value'])).' ';
    }

    private function buildFilterGroup($group)
    {
        if(empty($group['filters'])) {
            return '';
        }
        if(!is_array($group['filters'])) {
            throw new FwException('Malformed filter group. Field "filters" must be array of filters or filter groups.');
        }
        $filters = array();
        foreach($group['filters'] as $filter) {

            if($filter['type'] == 'filter') {
                $filters[] = $this->buildFilter($filter);
            } elseif($filter['type'] == 'group') {
                $filters[] = $this->buildFilterGroup($filter);
            } else {

                throw new FwException("Unknown filter type. Only [filter, group] allowed.".json_encode($filter));
            }
        }
        $operator = empty($group['operator']) ? 'and' : $group['operator'];
        if(!in_array($operator, self::$ALLOWED_OPERATORS)) {
            throw new FwException('Malformed filter group. Operator field must be one of th following: ['
                .implode(", ", self::$ALLOWED_OPERATORS).']');
        }
        $filters = array_filter($filters);

        return !empty($filters) ? ' ('.implode($operator, $filters).') ' : '';
    }

    public function getOrder()
    {
        if(empty($_REQUEST[self::ORDER])) {
            return '';
        }
        $order = json_decode($_REQUEST[self::ORDER], true);
        if(!is_array($order)) {
            throw new FwException("Malformed order object. It must be array with object: "
                ."[{field: 'name', direction: 'asc'}, {field: 'surname', direction: 'desc'})");
        }
        $out = array();
        foreach($order as $item) {
            if(empty($item['field'])) {
                throw new FwException('Malformed order object. Field "field" is required');
            }
            if(!empty($item['direction'])) {
                if(!in_array($item['direction'], self::$ORDER_DIRECTION)) {
                    throw new FwException("Unknown direction value in order object, must be one of ["
                        .implode(', ', self::$ORDER_DIRECTION)."]");
                }
            }
            if(!$this->isFieldExist($item['field'])) {
                throw new FwException('Field with name '.$item['field'].' is not exist.'
                    .' Check field name, or api provider url');
            }
            if($item['field'] == 'rand()') {
                $out[] = $item['field'];
            } else {
                $out[] = $item['field'] . ' ' . (!empty($item['direction']) ? $item['direction'] : 'asc');
            }
        }
        return ' ORDER BY '.implode(', ', $out).' ';
    }

    private function isFieldExist($field){
        static $fields;
        if(empty($fields)) {
            $fields = DB::getColumn("SHOW COLUMNS FROM " . $this->table);
        }
        return in_array($field, $fields);
    }

    public function getLimit()
    {
        if(empty($_REQUEST[self::PAGE])) {
            return '';
        }
        $page = intval($_REQUEST[self::PAGE]);
        if($page === 0) {
            throw new FwException("Invalid param ".self::PAGE." must be an integer greater than zero.");
        }
        $rowsPerPage = $this->getRowsPerPage();
        return ' LIMIT '. $rowsPerPage * ($page - 1).', '.$rowsPerPage;
    }

    public function getRowsPerPage(){
        return $this->rowsPerPage;
    }
}