<?php
/**
 * SCSS compressed formatter
 *
 * @author Leaf Corcoran <leafot@gmail.com>
 */
class ScssFormatterCompressed extends ScssFormatter {
    public $open = "{";
    public $tagSeparator = ",";
    public $assignSeparator = ":";
    public $break = "";

    public function indentStr($n = 0) {
        return "";
    }
}