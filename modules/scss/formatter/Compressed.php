<?php
/**
 * SCSS compressed formatter
 *
 * @author Leaf Corcoran <leafot@gmail.com>
 */
class Scss_Formatter_Compressed extends Scss_Formatter {
    public $open = "{";
    public $tagSeparator = ",";
    public $assignSeparator = ":";
    public $break = "";

    public function indentStr($n = 0) {
        return "";
    }
}