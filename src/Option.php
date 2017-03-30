<?php
/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 12/1/16
 * Time: 5:37 PM
 */

namespace JCFirebase;

use JCFirebase\Enums\RequestType;
use JCFirebase\Enums\PrintType;

class Option
{
    const _SHALLOW = 'shallow';
    const _PRINT = 'print';

    public static function isAllowPrint($reqType, $printType)
    {
        if ($printType == PrintType::PRETTY) {
            return true;
        }

        if ($printType == PrintType::SILENT) {
            if ($reqType == RequestType::DELETE) {
                return false;
            }
        }

        return true;
    }
}