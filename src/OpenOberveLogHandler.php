<?php

namespace Seguce92\OpenObserveMonolog;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Monolog\Formatter\ElasticsearchFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;


/**
 * OpenObserveLog class
 */
class OpenObserveLogHandler extends AbstractProcessingHandler
{
    /**
     * @var array
     */
    protected array $config;
    /**
     * @var string|mixed
     */
    private string $fallback;

    /**
     * @var string
     */
    private string $baseUrl;

    /**
     * @var string
     */
    private string $index;


    /**
     * @param string $index
     * @param string $baseUrl
     * @param array $config
     * @param string $fallback
     * @param int $level
     * @param bool $bubble
     */
    public function __construct( string $index, string $baseUrl, array $config, string $fallback = 'daily', int $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->config = $config;
        $this->fallback = $fallback;
        $this->index = $index;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param array $record
     * @return void
     */
    protected function write(array $record): void
    {
        if ( !empty($this->config['formatter']) ) {
            $formatter = new ElasticsearchFormatter($this->index, "_doc");
            $record = $formatter->format($record);
        }
        
        if (array_key_exists("_index", $record)) {
            unset($record['_index']);
        }

        if (array_key_exists("_type", $record)) {
            unset($record['_type']);
        }

        try {
            $this->baseUrl = str_ends_with($this->baseUrl, '/') 
                ? substr($this->baseUrl, 0, -1) 
                : $this->baseUrl;
            $this->baseUrl .= "/" . $this->index . "/_doc";
            Http::withOptions([
                'verify' => $this->config['is_ssl_verify'] ?? false
            ])->post($this->baseUrl, $record)
                ->throw(static function (\Illuminate\Http\Client\Response $httpResponse, $httpException) use ($record) {
                    Log::debug(get_class($httpResponse) . ' - ' . get_class($httpException));
                    Log::debug("Http/Curl call error. Destination:: " . $this->baseUrl . ' and Response:: ' . $httpResponse->body());
                });
        } catch ( \Throwable $e ) {
            $method = strtolower($record['level_name']);
            app('log')->channel($this->fallback)->$method(sprintf('%s (%s fallback: %s)', $record['formatted'], $record['channel'], $e->getMessage()));
        }

    }
}
