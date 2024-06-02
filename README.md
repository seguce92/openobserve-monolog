# Open Observe Monolog Handler

Searching for logs for your applications can be tedious and challenging. OpenObserve solves the problem very elegantly. You can use standard log forwarders like fluentd, fluent-bit, vector, syslog-ng or others to forward logs to OpenObserve. OpenObserve can then store the indexed logs in S3 or on disk and provide fast search for your logs.
## OpenObserve  Monolog Handler is used for pushing laravel log into the OpenObserve for collection and analysis.

Log forwarders can read the log files incrementally as new logs appear in them and can then forward them in batches in order to be more efficient in sending them.

## Install

Install [openobserve-monolog](https://packagist.org/packages/seguce92/openobserve-monolog).

```shell
composer require seguce92/openobserve-monolog
```

## Get Started

1.Modify `config/logging.php`.
```php
return [
    'channels' => [
        // ...
        "OpenObserve" => \Tasmidur\OpenObserveMonologHandler\OpenObserveLogger::getInstance(
            indexName: env('OPENOBSERVE_INDEX', "app_log"),
            baseUrl: env('OPENOBSERVE_BASE_URL', 'http://admin:admin123@localhost:4080/api')
        ),
    ],
];
```
### OpenObserve with SSL_VERIFY
```php
return [
    'channels' => [
        // ...
       "OpenObserve" => \Tasmidur\OpenObserveMonologHandler\OpenObserveLogger::getInstance(
            indexName: env('LOG_INDEX', "app_log"),
            baseUrl: env('OPENOBSERVE_BASE_URL', 'http://admin:admin123@localhost:4080/api'),
            options: [
                "is_ssl_verify" => true //true or false
            ]
        ),
    ],
];
```
2.Modify `.env`.
```
LOG_CHANNEL=OpenObserve
OPENOBSERVE_INDEX=zinc_log
OPENOBSERVE_BASE_URL=url

```
## License

[MIT](LICENSE)
