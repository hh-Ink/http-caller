<?php

declare(strict_types=1);
/**
 * This file is part of msmm.
 */

namespace Hhink\HttpCaller\Utils;

class StringUtil
{
    /**
     * 判断是否是json串.
     */
    public static function isJson($jsonStr)
    {
        if (!$jsonStr || !is_string($jsonStr)) {
            return false;
        }
        $jsonStr = self::removeBOM($jsonStr);
        $data = json_decode($jsonStr, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            return false;
        }
        return $data;
    }

    /**
     * 转换BOM.
     */
    public static function removeBOM(string $str): string
    {
        if (
            ord(substr($str, 0, 1)) == 239
            && ord(substr($str, 1, 1)) == 187
            && ord(substr($str, 2, 1)) == 191
        ) {
            $str = substr($str, 3);
        }
        return $str;
    }
}
