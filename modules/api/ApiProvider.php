<?php

/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 7/2/2015
 * Time: 10:06 AM
 */
interface ApiProvider
{
    /**
     * @return array
     */
    public function getAllowedFields();

    /**
     * @return array
     */
    public function getDeniedFields();
}