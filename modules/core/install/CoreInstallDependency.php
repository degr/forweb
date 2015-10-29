<?php

/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 10/28/2015
 * Time: 3:42 PM
 */
class CoreInstallDependency
{
    /**
     * @var string
     */
    private $package;
    /**
     * @var array
     */
    private $input;
    /**
     * @var boolean
     */
    private $state;


    /**
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param string $package
     */
    public function setPackage($package)
    {
        $this->package = $package;
    }

    /**
     * @return array
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param array $input
     */
    public function setInput($input)
    {
        $this->input = $input;
    }

    /**
     * @return boolean
     */
    public function isState()
    {
        return $this->state;
    }

    /**
     * @param boolean $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }
}