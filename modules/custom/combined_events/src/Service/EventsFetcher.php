<?php

namespace Drupal\combined_events\Service;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Service that fetches external event data.
 */
class EventsFetcher {

    /**
     * The HTTP client service.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected ClientInterface $httpClient;

    /**
     * The logger service.
     *
     * @var \Drupal\Core\Logger\LoggerChannelInterface
     */
    protected LoggerChannelInterface $logger;

    /**
     * Constructs a new EventsFetcher.
     *
     * @param \GuzzleHttp\ClientInterface $http_client
     *   The HTTP client service.
     * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
     *   The logger channel factory interface.
     */
    public function __construct(ClientInterface $http_client, LoggerChannelFactoryInterface $logger_factory) {
        $this->httpClient = $http_client;
        $this->logger = $logger_factory->get('combined_events');
    }

    /**
     * Fetches events from an external API.
     *
     * @return array
     *   A list of events.
     */
    public function fetchEvents(): array {
        try {
            $response = $this->httpClient->request('GET', 'https://digitalakdemin.se/events.json', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), TRUE, 512, JSON_THROW_ON_ERROR);
        }
        catch (GuzzleException $e) {
            // Log the error if the HTTP request fails.
            $this->logger->error('Failed to fetch events: @message', [
                '@message' => $e->getMessage(),
            ]);
        }
        catch (\JsonException $e) {
            // Handle JSON decoding errors gracefully.
            $this->logger->error('Failed to decode events data: @message', [
                '@message' => $e->getMessage(),
            ]);
        }

        // Return an empty array in case of failure.
        return [];
    }

}