<?php

namespace Seguce92\OpenObserveMonolog;

use Monolog\Logger;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;


class OpenObserveLogger
{
    /**
     * Get the logging definition of openObserve Log channel
     * @param string $indexName
     * @param string $baseUrl
     * @param array $options
     * @return array
     */
    public static function getInstance(string $indexName, string $baseUrl, array $options = []): array
    {
        $default = [
            'driver' => 'custom',
            'via' => static::class,
            'index' => $indexName,
            'base_url' => $baseUrl,
            'fallback' => 'daily'
        ];
        return array_merge($default, $options);
    }

    /**
     * @throws Throwable
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('openObserve');
        throw_if(empty($config['base_url']), new \Exception('Provided baseUrl is invalid', ResponseAlias::HTTP_UNPROCESSABLE_ENTITY));
        $indexName = $config['index'];
        $baseUrl = $config['base_url'];
        $handler = new OpenObserveLogHandler(index: $indexName, baseUrl: $baseUrl, config: $config);
        $logger->pushHandler($handler);
        
        return $logger;
    }
}
