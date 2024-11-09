<?php

declare(strict_types=1);
/**
 * This file is part of msmm.
 */

namespace Hhink\HttpCaller\Utils;

use Hhink\HttpCaller\Dto\Result;
use Hhink\HttpCaller\Response\ErrorCode;

class ReturnUtil
{
    public const RETURN_ZERO = 0;

    public const RETURN_EMPTY = 1;

    public const RETURN_EMPTY_ARRAY = 2;

    /**
     * 服务异常时，返回指定类型的假值
     */
    public static function wrap(Result $response, int $returnType = self::RETURN_EMPTY_ARRAY)
    {
        if ($response->getCode() == ErrorCode::CODE_SUCCESS) {
            return $response->getData();
        }
        switch ($returnType) {
            case self::RETURN_ZERO:
                return 0;
            case self::RETURN_EMPTY:
                return '';
            default:
                return [];
        }
    }
}
