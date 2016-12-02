<?php
/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 12/1/16
 * Time: 5:37 PM
 */

namespace JCFirebase;


class JCFirebaseOption
{
    const __default = null;

    const OPTION_SHALLOW = 'shallow';
    const SHALLOW_TRUE = 'true';
    const SHALLOW_FALSE = 'false';

    const OPTION_PRINT = 'print';
    const PRINT_PRETTY = 'pretty';
    const PRINT_SILENT = 'silent';

    const REQ_TYPE_GET = 0;
    const REQ_TYPE_PUT = 1;
    const REQ_TYPE_POST = 2;
    const REQ_TYPE_PATCH = 3;
    const REQ_TYPE_DELETE = 4;

    public static function isAllowPrint($reqType = self::REQ_TYPE_GET, $pType = self::PRINT_PRETTY)
    {
        if ($pType == self::PRINT_PRETTY) {
            return true;
        }

        if ($pType == self::PRINT_SILENT) {
            if ($reqType == self::REQ_TYPE_DELETE) {
                return false;
            }
        }

        return true;
    }
}