<?php

declare(strict_types=1);
/**
 * This file is part of msmm.
 */

namespace Hhink\HttpCaller\Formatters;

use Hhink\HttpCaller\Dto\Result;
use Psr\Http\Message\ResponseInterface;

interface FormatterInterface
{
    /**
     * 写入 psr response.
     */
    public function setPsrResponse(ResponseInterface $response): FormatterInterface;

    /**
     * 打包结果.
     */
    public function bind(): Result;
}
