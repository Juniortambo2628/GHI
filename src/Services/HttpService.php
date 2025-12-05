<?php

/**
 * HTTP Service using Guzzle
 * Global Harmony Initiative Website
 */

namespace GHI\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

class HttpService
{
    private static ?Client $instance = null;

    private static array $defaultOptions = [];

    /**
     * Get HTTP Client instance (Singleton)
     */
    public static function getInstance(): Client
    {
        if (!self::$instance instanceof \GuzzleHttp\Client) {
            self::$instance = self::createClient();
        }

        return self::$instance;
    }

    /**
     * Create and configure HTTP Client
     */
    private static function createClient(): Client
    {
        $defaultOptions = [
            'timeout' => 30,
            'verify' => true, // SSL verification
            'http_errors' => true, // Throw exceptions on HTTP errors
            'headers' => [
                'User-Agent' => 'Global Harmony Initiative Website/1.0',
            ],
        ];

        return new Client($defaultOptions);
    }

    /**
     * Make GET request
     */
    public static function get(string $url, array $options = []): array
    {
        try {
            $response = self::getInstance()->get($url, $options);
            $body = $response->getBody()->getContents();
            $statusCode = $response->getStatusCode();

            return [
                'success' => true,
                'status_code' => $statusCode,
                'body' => $body,
                'headers' => $response->getHeaders(),
            ];
        } catch (GuzzleException $guzzleException) {
            if (function_exists('log_message')) {
                log_message('error', 'HTTP GET request failed', [
                    'url' => $url,
                    'error' => $guzzleException->getMessage(),
                ]);
            }

            $statusCode = 0;
            if ($guzzleException instanceof RequestException && $guzzleException->hasResponse()) {
                $statusCode = $guzzleException->getResponse()->getStatusCode();
            }

            return [
                'success' => false,
                'error' => $guzzleException->getMessage(),
                'status_code' => $statusCode,
            ];
        }
    }

    /**
     * Make POST request
     */
    public static function post(string $url, array $data = [], array $options = []): array
    {
        try {
            $options[RequestOptions::JSON] = $data;
            $response = self::getInstance()->post($url, $options);
            $body = $response->getBody()->getContents();
            $statusCode = $response->getStatusCode();

            return [
                'success' => true,
                'status_code' => $statusCode,
                'body' => $body,
                'headers' => $response->getHeaders(),
            ];
        } catch (GuzzleException $guzzleException) {
            if (function_exists('log_message')) {
                log_message('error', 'HTTP POST request failed', [
                    'url' => $url,
                    'error' => $guzzleException->getMessage(),
                ]);
            }

            $statusCode = 0;
            if ($guzzleException instanceof RequestException && $guzzleException->hasResponse()) {
                $statusCode = $guzzleException->getResponse()->getStatusCode();
            }

            return [
                'success' => false,
                'error' => $guzzleException->getMessage(),
                'status_code' => $statusCode,
            ];
        }
    }

    /**
     * Make PUT request
     */
    public static function put(string $url, array $data = [], array $options = []): array
    {
        try {
            $options[RequestOptions::JSON] = $data;
            $response = self::getInstance()->put($url, $options);
            $body = $response->getBody()->getContents();
            $statusCode = $response->getStatusCode();

            return [
                'success' => true,
                'status_code' => $statusCode,
                'body' => $body,
                'headers' => $response->getHeaders(),
            ];
        } catch (GuzzleException $guzzleException) {
            if (function_exists('log_message')) {
                log_message('error', 'HTTP PUT request failed', [
                    'url' => $url,
                    'error' => $guzzleException->getMessage(),
                ]);
            }

            $statusCode = 0;
            if ($guzzleException instanceof RequestException && $guzzleException->hasResponse()) {
                $statusCode = $guzzleException->getResponse()->getStatusCode();
            }

            return [
                'success' => false,
                'error' => $guzzleException->getMessage(),
                'status_code' => $statusCode,
            ];
        }
    }

    /**
     * Make DELETE request
     */
    public static function delete(string $url, array $options = []): array
    {
        try {
            $response = self::getInstance()->delete($url, $options);
            $body = $response->getBody()->getContents();
            $statusCode = $response->getStatusCode();

            return [
                'success' => true,
                'status_code' => $statusCode,
                'body' => $body,
                'headers' => $response->getHeaders(),
            ];
        } catch (GuzzleException $guzzleException) {
            if (function_exists('log_message')) {
                log_message('error', 'HTTP DELETE request failed', [
                    'url' => $url,
                    'error' => $guzzleException->getMessage(),
                ]);
            }

            $statusCode = 0;
            if ($guzzleException instanceof RequestException && $guzzleException->hasResponse()) {
                $statusCode = $guzzleException->getResponse()->getStatusCode();
            }

            return [
                'success' => false,
                'error' => $guzzleException->getMessage(),
                'status_code' => $statusCode,
            ];
        }
    }

    /**
     * Make request with custom method
     */
    public static function request(string $method, string $url, array $options = []): array
    {
        try {
            $response = self::getInstance()->request($method, $url, $options);
            $body = $response->getBody()->getContents();
            $statusCode = $response->getStatusCode();

            return [
                'success' => true,
                'status_code' => $statusCode,
                'body' => $body,
                'headers' => $response->getHeaders(),
            ];
        } catch (GuzzleException $guzzleException) {
            if (function_exists('log_message')) {
                log_message('error', 'HTTP request failed', [
                    'method' => $method,
                    'url' => $url,
                    'error' => $guzzleException->getMessage(),
                ]);
            }

            $statusCode = 0;
            if ($guzzleException instanceof RequestException && $guzzleException->hasResponse()) {
                $statusCode = $guzzleException->getResponse()->getStatusCode();
            }

            return [
                'success' => false,
                'error' => $guzzleException->getMessage(),
                'status_code' => $statusCode,
            ];
        }
    }

    /**
     * Download file
     */
    public static function download(string $url, string $destination): bool
    {
        try {
            $response = self::getInstance()->get($url, ['sink' => $destination]);

            if (function_exists('log_message')) {
                log_message('info', 'File downloaded', [
                    'url' => $url,
                    'destination' => $destination,
                ]);
            }

            return $response->getStatusCode() === 200;
        } catch (GuzzleException $guzzleException) {
            if (function_exists('log_message')) {
                log_message('error', 'File download failed', [
                    'url' => $url,
                    'destination' => $destination,
                    'error' => $guzzleException->getMessage(),
                ]);
            }

            return false;
        }
    }
}
