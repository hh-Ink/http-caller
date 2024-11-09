<?php

declare(strict_types=1);
/**
 * This file is part of msmm.
 */

namespace Hhink\HttpCaller\Formatters;

use Hhink\HttpCaller\Dto\Result;
use Hhink\HttpCaller\Response\ErrorCode;
use Hhink\HttpCaller\Response\HttpCode;
use Hhink\HttpCaller\Utils\StringUtil;
use Psr\Http\Message\ResponseInterface;

class Formatter implements FormatterInterface
{
    private ResponseInterface $response;

    public function setPsrResponse(ResponseInterface $response): FormatterInterface
    {
        $this->response = $response;
        return $this;
    }

    /**
     * 打包结果.
     */
    public function bind(): Result
    {
        $response = $this->statusCode();
        if ($response instanceof Result) {
            return $response;
        }
        return $this->body();
    }

    /**
     * 请求状态处理.
     */
    private function statusCode(): ?Result
    {
        $statusCode = $this->response->getStatusCode();
        if ($statusCode == HttpCode::HTTP_OK) {
            return null;
        }
        return new Result($statusCode, HttpCode::CODE_MAP[$statusCode]);
    }

    /**
     * 统一处理返回数据.
     */
    private function body(): Result
    {
        $content = $this->response->getBody()->__toString();
        // 返回值解析
        $body = StringUtil::isJson($content);
        if (!$body) {
            return new Result(
                ErrorCode::JSON_ERROR,
                ErrorCode::CODE_MAP[ErrorCode::JSON_ERROR],
                $content
            );
        }

        // 标准协议解析
        if (
            !array_key_exists('code', $body)
            || !array_key_exists('message', $body)
            || !array_key_exists('data', $body)
        ) {
            return new Result(
                ErrorCode::RESULT_ERROR,
                ErrorCode::CODE_MAP[ErrorCode::RESULT_ERROR],
                $body
            );
        }

        $code = $body['code'] ?? false;
        $msg = $body['message'] ?? '';
        $data = $body['data'] ?? null;
        $fieldErrors = $body['errors'] ?? ($body['error'] ?? []);

        // 业务状态码异常
        if ($code != ErrorCode::CODE_SUCCESS) {
            $baseDto = new Result($code, $msg, $data);
            if (is_array($fieldErrors) && $fieldErrors) {
                $baseDto->setError($fieldErrors);
            }
            return $baseDto;
        }

        // 字符串解析处理
        if (is_string($data)) {
            $data = StringUtil::isJson($data) ?: $data;
        }

        return new Result($code, $msg, $data);
    }
}
