<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ror
 * Date: 04.04.2015
 * Time: 13:08
 */

class OrmCommands {
    const SELECT = 0;
    const INSERT = 1;
    const UPDATE = 2;
    const DELETE = 3;

    const FROM = 4;
    const INTO = 5;
    const SET = 6;

    const VALUES = 7;
    const WHERE = 8;
    const ORDER = 9;
    const GROUP = 10;
    const LIMIT = 11;
    const HAVING = 12;

    public static function get($commandNumber)
    {
        switch($commandNumber) {
            case 0: return 'select';
            case 1: return 'insert';
            case 2: return'update';
            case 3: return'delete';
            case 4: return'from';
            case 5: return'into';
            case 6: return'set';
            case 7: return'values';
            case 8: return'where';
            case 9: return'order';
            case 10: return'group';
            case 11: return 'limit';
            case 12: return 'having';
            default:
                throw new FwException("Unknown SQL command type: ".$commandNumber);
        }
    }
}