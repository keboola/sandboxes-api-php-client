<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

use Closure;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use JsonException;
use Keboola\Sandboxes\Api\Exception\ClientException;
use Keboola\Sandboxes\Api\Exception\ServerException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validation;

abstract class AbstractClient
{
    private const DEFAULT_USER_AGENT = 'Internal Sandboxes-api PHP Client';
    private const DEFAULT_BACKOFF_RETRIES = 10;
    private const JSON_DEPTH = 512;

    protected GuzzleClient $client;

    public function __construct(string $apiUrl, ?array $options = [])
    {
        $options = $this->validateOptions($apiUrl, $options);
        $this->client = $this->initClient($options);
    }

    protected function validateOptions(string $apiUrl, ?array $options = []): array
    {
        $options['apiUrl'] = $apiUrl;
        $validator = Validation::createValidator();
        $errors = $validator->validate($options['apiUrl'], [new Url()]);
        if (!empty($options['backoffMaxTries'])) {
            $errors->addAll($validator->validate($options['backoffMaxTries'], [new Range(['min' => 0, 'max' => 100])]));
            $options['backoffMaxTries'] = intval($options['backoffMaxTries']);
        } else {
            $options['backoffMaxTries'] = self::DEFAULT_BACKOFF_RETRIES;
        }
        if (empty($options['userAgent'])) {
            $options['userAgent'] = self::DEFAULT_USER_AGENT;
        }
        if ($errors->count() !== 0) {
            $messages = '';
            /** @var ConstraintViolationInterface $error */
            foreach ($errors as $error) {
                $messages .= 'Value "' . $error->getInvalidValue() . '" is invalid: ' . $error->getMessage() . "\n";
            }
            throw new Exception('Invalid parameters when creating internal client: ' . $messages);
        }
        return $options;
    }

    protected function createDefaultDecider(int $maxRetries): Closure
    {
        return function (
            $retries,
            RequestInterface $request,
            ?ResponseInterface $response = null,
            $error = null,
        ) use ($maxRetries) {
            if ($retries >= $maxRetries) {
                return false;
            } elseif ($response && $response->getStatusCode() >= 500) {
                return true;
            } elseif ($error) {
                return true;
            } else {
                return false;
            }
        };
    }

    protected function initClient(array $options = []): GuzzleClient
    {
        // Initialize handlers (start with those supplied in constructor)
        if (isset($options['handler']) && $options['handler'] instanceof HandlerStack) {
            $handlerStack = HandlerStack::create($options['handler']); // @phpstan-ignore-line
        } else {
            $handlerStack = HandlerStack::create();
        }
        // Set exponential backoff
        $handlerStack->push(Middleware::retry($this->createDefaultDecider($options['backoffMaxTries'])));
        // Set handler to set default headers
        $handlerStack->push(Middleware::mapRequest(
            function (RequestInterface $request) use ($options) {
                $requestUpdated = $request
                    ->withHeader('User-Agent', $options['userAgent'])
                    ->withHeader('Content-type', 'application/json');
                foreach ($options['headers'] as $key => $value) {
                    $requestUpdated = $requestUpdated->withHeader($key, $value);
                }
                return $requestUpdated;
            },
        ));
        // Set client logger
        if (isset($options['logger']) && $options['logger'] instanceof LoggerInterface) {
            $handlerStack->push(Middleware::log(
                $options['logger'],
                new MessageFormatter('[sandboxes-api] {method} {uri} : {code} {res_header_Content-Length}'),
            ));
        }
        // finally, create the instance
        return new GuzzleClient(['base_uri' => $options['apiUrl'], 'handler' => $handlerStack]);
    }

    protected function sendRequest(Request $request): array
    {
        try {
            $response = $this->client->send($request);
        } catch (GuzzleException $e) {
            if ($e->getCode() < 500) {
                throw new ClientException($e->getMessage(), $e->getCode(), $e);
            }
            throw new ServerException($e->getMessage(), $e->getCode(), $e);
        }

        $body = $response->getBody()->getContents();
        if (!strlen($body)) {
            return [];
        }

        try {
            $data = (array) json_decode($body, true, self::JSON_DEPTH, JSON_THROW_ON_ERROR);
            return $data ?: [];
        } catch (JsonException $e) {
            throw new ServerException(
                'Unable to parse response body into JSON: ' . $e->getMessage(),
                $e->getCode(),
                $e,
            );
        }
    }

    protected function sendRequestWithPagination(Request $request): iterable
    {
        $nextPageLink = (string) $request->getUri();

        do {
            try {
                $request = $request->withUri(new Uri($nextPageLink));
                $response = $this->client->send($request);
            } catch (GuzzleException $e) {
                if ($e->getCode() < 500) {
                    throw new ClientException($e->getMessage(), $e->getCode(), $e);
                }
                throw new ServerException($e->getMessage(), $e->getCode(), $e);
            }

            $body = $response->getBody()->getContents();
            if (!strlen($body)) {
                return;
            }

            try {
                $data = (array) json_decode($body, true, self::JSON_DEPTH, JSON_THROW_ON_ERROR);
                foreach ($data as $item) {
                    yield $item;
                }
            } catch (JsonException $e) {
                throw new ServerException(
                    'Unable to parse response body into JSON: ' . $e->getMessage(),
                    $e->getCode(),
                    $e,
                );
            }

            $nextPageLink = $response->getHeaderLine('Link') ?: null;
        } while ($nextPageLink !== null);
    }
}
