<?php

/**
 * This file is part of msmm.
 */

namespace Hhink\HttpCaller\Dto;

class Result
{
    /**
     * 记录数据.
     * @var mixed
     */
    private $data;

    /**
     * 状态.
     * @var string
     */
    private string $status = 'success';

    /**
     * 返回信息.
     * @var string
     */
    private string $message;

    /**
     * 请求状态code.
     * @var int
     */
    private int $code;

    /**
     * 字段错误提示信息.
     * @var mixed
     */
    private $error = [];

    /**
     * 服务器时间.
     * @var string
     */
    private $currentTime;

    public function __construct($code, $msg, $data = null)
    {
        $this->code = $code;
        $this->message = $msg;
        $this->data = $data;
        $this->currentTime = date('Y-m-d H:i:s');
        if (intval($code) !== 200) {
            $this->status = 'error';
        }
    }

    /**
     * 设置错误的参数列表.
     */
    public function setError(array $error)
    {
        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getMsg(): string
    {
        return $this->message;
    }

    /**
     * 状态码
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getCurrentTime(): string
    {
        return $this->currentTime;
    }

    /**
     * 转数组.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data,
            'currentTime' => $this->currentTime,
            'errors' => $this->error,
        ];
    }

    /**
     * 转json.
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}
