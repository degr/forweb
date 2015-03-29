<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 28.03.2015
 * Time: 23:58
 */
interface ORM_Filter_IBase{
    const METHOD_POST = 'post';
    const METHOD_GET = 'get';

    /**
     * Check, if this filter applicable to this request
     * @return boolean
     */
    public function isActiveOnThisRequest();

    /**
     * Set method, where filter key will be searched
     * @param string $method
     * @return void
     */
    public function setHttpMethod($method);

    /**
     * Set request key on which filter will react
     * @param string $key
     * @return void
     */
    public function setHttpMethodKey($key);


    /**
     * Generate filter hash for pagination
     * @return string
     */
    public function getFilterHash();

    /**
     * Change sql parameter and set this value
     * @see ORM_QueryBuilder::filterAddWhere
     * @see ORM_QueryBuilder::filterAddWhere
     *
     * @param string $sql
     * @return string
     */
    public function modifySqlCommand($sql);
}
