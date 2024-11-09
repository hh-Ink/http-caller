<?php

declare(strict_types=1);
/**
 * This file is part of msmm.
 */

namespace Hhink\HttpCaller\Response;

class ErrorCode
{
    // 成功状态
    public const CODE_SUCCESS = 200;

    // 服务异常
    public const CODE_ERROR = 100;

    // JSON 解析失败
    public const JSON_ERROR = 107;

    // 返回数据格式不是标准格式
    public const RESULT_ERROR = 108;

    public const CODE_MAP = [
        self::JSON_ERROR => '返回不是json格式',
        self::RESULT_ERROR => '返回数据格式不是标准格式',
    ];
}
