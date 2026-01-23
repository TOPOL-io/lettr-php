<?php

declare(strict_types=1);

namespace Lettr;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Lettr\Contracts\TransporterContract;
use Lettr\Exceptions\ApiException;
use Lettr\Exceptions\TransporterException;

/**
 * HTTP Client for Lettr API.
 */
final class Client implements TransporterContract
{
    private readonly ClientInterface $httpClient;

    private readonly string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = new GuzzleClient([
            'base_uri' => Lettr::BASE_URL,
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function post(string $uri, array $data): array
    {
        return $this->request('POST', $uri, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $uri): array
    {
        return $this->request('GET', $uri);
    }

    /**
     * {@inheritDoc}
     */
    public function getWithQuery(string $uri, array $query = []): array
    {
        return $this->request('GET', $uri, null, $query);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $uri): void
    {
        $this->request('DELETE', $uri);
    }

    /**
     * Send a request to the API.
     *
     * @param  array<string, mixed>|null  $data
     * @param  array<string, mixed>|null  $query
     * @return array<string, mixed>
     *
     * @throws ApiException|TransporterException
     */
    private function request(string $method, string $uri, ?array $data = null, ?array $query = null): array
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'lettr-php/'.Lettr::VERSION,
            ],
        ];

        if ($data !== null) {
            $options['json'] = $data;
        }

        if ($query !== null && count($query) > 0) {
            $options['query'] = $query;
        }

        try {
            $response = $this->httpClient->request($method, $uri, $options);
            $contents = $response->getBody()->getContents();

            if (trim($contents) === '') {
                return [];
            }

            /** @var array<string, mixed> $decoded */
            $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

            // Unwrap data envelope if present
            if (isset($decoded['data']) && is_array($decoded['data'])) {
                return $decoded['data'];
            }

            return $decoded;
        } catch (GuzzleException $e) {
            $this->handleGuzzleException($e);
        } catch (JsonException $e) {
            throw new TransporterException('Failed to decode API response: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Handle Guzzle exceptions and convert them to Lettr exceptions.
     *
     * @throws ApiException|TransporterException
     */
    private function handleGuzzleException(GuzzleException $e): never
    {
        if (method_exists($e, 'getResponse') && $e->getResponse() !== null) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $contents = $response->getBody()->getContents();

            try {
                /** @var array{message?: string, error?: string} $body */
                $body = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
                $message = $body['message'] ?? $body['error'] ?? 'Unknown API error';
            } catch (JsonException) {
                $message = $contents ?: 'Unknown API error';
            }

            throw new ApiException($message, $statusCode, $e);
        }

        throw new TransporterException($e->getMessage(), (int) $e->getCode(), $e);
    }
}
