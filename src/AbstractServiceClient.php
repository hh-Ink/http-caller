<?php

declare(strict_types=1);
/**
 * This file is part of msmm.
 */

namespace Hhink\HttpCaller;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Hhink\HttpCaller\Dto\Result;
use Hhink\HttpCaller\Formatters\Formatter;
use Hhink\HttpCaller\Formatters\FormatterInterface;
use Hhink\HttpCaller\Response\ErrorCode;
use Hhink\HttpCaller\Utils\StringUtil;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

abstract class AbstractServiceClient
{
    /**
     * The domain.
     */
    protected string $domain = '';

    /**
     * The protocol of the target service, this protocol name
     * http or https.
     */
    protected string $protocol = 'http';

    /**
     * @var Client $client
     */
    protected Client $client;

    /**
     * @var FormatterInterface|null $formatter
     */
    protected ?FormatterInterface $formatter = null;

    /**
     * @var LoggerInterface|null $logger
     */
    protected ?LoggerInterface $logger = null;


    /**
     * @var array $clientConfig
     */
    protected array $clientConfig = [];

    /**
     * 设置打包器.
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * 设置日志
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    /**
     * 设置链接客户端.
     * @param array $clientConfig
     * @return void
     */
    public function setClientConfig(array $clientConfig)
    {
        $this->clientConfig = $clientConfig;
    }


    /**
     * @throws Exception
     */
    protected function getBaseUri(): string
    {
        // 域名访问
        return mb_strrpos($this->domain, '://') !== false
            ? $this->domain
            : $this->protocol . '://' . $this->domain;
    }

    /**
     * 获取打包器.
     */
    protected function getFormatter(): FormatterInterface
    {
        return $this->formatter instanceof FormatterInterface
            ? $this->formatter
            : new Formatter();
    }

    /**
     * GET.
     *
     * @throws Exception
     */
    protected function get($uri, array $param = [], array $options = []): Result
    {
        $options['query'] = $param;
        return $this->call('GET', $uri, $options);
    }

    /**
     * DELETE.
     *
     * @throws Exception
     */
    protected function delete($uri, array $param = [], array $options = []): Result
    {
        $options['query'] = $param;
        return $this->call('DELETE', $uri, $options);
    }

    /**
     * POST.
     *
     * @throws Exception
     */
    protected function post($uri, array $param = [], array $options = []): Result
    {
        $options['json'] = $param;
        return $this->call('POST', $uri, $options);
    }

    /**
     * PUT.
     *
     * @throws Exception
     */
    protected function put($uri, array $param = [], array $options = []): Result
    {
        $options['json'] = $param;
        return $this->call('PUT', $uri, $options);
    }

    /**
     * 统一的访问方法.
     */
    protected function call($method, $uri, array $options = []): Result
    {
        try {
            // http 链接客户端
            $baseConfig = [
                'base_uri' => $this->getBaseUri(),
                'handler' => $this->getStack(),
            ];
            $client = new Client(array_merge($baseConfig, $this->clientConfig));

            // 结果处理
            return $this->getFormatter()
                ->setPsrResponse($client->request($method, $uri, $options))
                ->bind();
        } catch (Throwable $ex) {
            $data = null;
            if(method_exists($ex, 'getResponse')){
                $response = $ex->getResponse();
                $data = $response instanceof ResponseInterface
                    ? $response->getBody()->getContents()
                    : null;
                if ($data) {
                    $data = StringUtil::isJson($data) ?: $data;
                }
            }
            $baseDto = (new Result(ErrorCode::CODE_ERROR, $ex->getMessage(), $data));
            $baseDto->setError($ex->getTrace());
            return $baseDto;
        }
    }

    /**
     * 获取堆栈.
     */
    protected function getStack(): HandlerStack
    {
        // 堆栈设置
        $stack = (new HandlerStack())->create();
        // 设置日志记录驱动
        if ($this->logger instanceof LoggerInterface) {
            $format = 'URL:{url}    BODY:{req_body} RESPONSE:{res_body}';
            $stack->push(
                Middleware::log(
                    $this->logger,
                    new MessageFormatter($format)
                )
            );
        }
        return $stack;
    }
}
