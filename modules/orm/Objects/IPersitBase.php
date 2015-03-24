<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 22.03.2015
 * Time: 14:13
 */
interface ORM_Objects_IPersitBase{
    public function getPrimaryKey();
    public function toArray();
}