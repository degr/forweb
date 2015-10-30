<?php
/**
 * Created by IntelliJ IDEA.
 * User: rsmirnou
 * Date: 9/4/2015
 * Time: 9:23 AM
 */
interface ModuleInput{
    /**
     * Return identifier for input.
     * Each identifier must be unique in module scope
     * @return int
     */
    public function getIdentifier();

    /**
     * Process user response
     * @param $userInput string. not null, ''.
     * @return boolean
     */
    public function process($userInput);

    /**
     * Ask question for user on module install
     * @return string
     */
    public function getQuestion();
    /**
     * Ask question for user on module install
     * @return string
     */
    public function getDefaultValue();
}