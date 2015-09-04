<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 9/4/2015
 * Time: 9:23 AM
 */
interface ModuleInput{
    /**
     * Return input identifier for input.
     * Each identifier must be unique for each module
     * @return int
     */
    public function getIdentifier();

    /**
     * Process user response
     * @return boolean
     */
    public function process($userInput);

    /**
     * Ask question for user on module install
     * @return string
     */
    public function getQuestion();
}