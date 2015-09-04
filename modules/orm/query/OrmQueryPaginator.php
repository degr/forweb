<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 04.04.2015
 * Time: 8:54
 */
class OrmQueryPaginator extends OrmQueryAbstractItem{

    /**
     * Page number
     * @var integer
     */
    protected $pageNumber;

    /**
     * Number of items in result set
     * @var integer
     */
    protected $itemsOnPage;

    /**
     * Return command query command name,
     * to each this item must be applied
     * Command's is integer, not string,
     * because, each command define it's position in query.
     * @see ORM_QueryBuilder::applyQueryItems()
     * @return int
     */
    public function getQueryCommand()
    {
        return OrmCommands::LIMIT;
    }

    public function __construct($table, $field, $pageNumber = null, $itemsOnPage = null, $autoActivateRequestMethod = null, $autoActivateRequestKey = null){
        parent::__construct($table, $field, $autoActivateRequestMethod, $autoActivateRequestKey);
        if($pageNumber != null ) {
            $this->setPageNumber($pageNumber);
        }
        if($itemsOnPage != null ) {
            $this->setItemsOnPage($itemsOnPage);
        }

    }
    /**
     * Return query separator type:
     * ',', 'AND', 'OR', etc.
     * @return string
     */
    public function getQuerySeparator()
    {
        return "";
    }

    /**
     * Build query string for this item
     * @return int
     */
    public function buildQueryString()
    {
        return $this->getPageNumber().".".$this->getItemsOnPage();
    }


    /**
     * @return int
     */
    public function getItemsOnPage()
    {
        if($this->itemsOnPage == null) {
            return 20;
        }
        return $this->itemsOnPage;
    }

    /**
     * @param int $itemsOnPage
     */
    public function setItemsOnPage($itemsOnPage)
    {
        $this->itemsOnPage = $itemsOnPage;
    }

    /**
     * @return int
     */
    public function getPageNumber()
    {
        if($this->pageNumber == null) {
            return 0;
        }
        return $this->pageNumber;
    }

    /**
     * @param int $pageNumber
     */
    public function setPageNumber($pageNumber)
    {
        $this->pageNumber = $pageNumber;
    }
}