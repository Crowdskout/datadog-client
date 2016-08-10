<?php
namespace Elite50\DataDogClient;

use Elite50\DataDogClient\Client\EmptyMetricException;
use Elite50\DataDogClient\Client\EmptySeriesException;
use Elite50\DataDogClient\Client\RequestException;
use Elite50\DataDogClient\Series\Metric;

/**
 * Class Client
 *
 * This client is used to send series and events to datadog and
 * handles to serialization of objects into the right format.
 *
 * Use the methods `Client::sendEvent`, `Client::sendMetric` or `Client::sendSeries`
 * to send previously build objects through the wire.
 * Alternatively, you can use `Client::metric` or `Client::event` methods to send
 * data without having to build objects first.
 *
 * @package Bayer\DataDogClient
 */
class Client
{
    const ENDPOINT_EVENT = 'https://app.datadoghq.com/api/v1/events?api_key=';
    const ENDPOINT_SERIES = 'https://app.datadoghq.com/api/v1/series?api_key=';
    /**
     * Your personal API key
     *
     * @var string
     */
    protected $apiKey;
    /**
     * Application key for read actions.
     * Currently unused
     *
     * @var null|string
     */
    protected $applicationKey;

    /**
     * @param string $apiKey
     * @param null|string $applicationKey
     */
    public function __construct($apiKey, $applicationKey = null)
    {
        $this->apiKey = $apiKey;
        $this->applicationKey = $applicationKey;

    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     *
     * @return Client
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getApplicationKey()
    {
        return $this->applicationKey;
    }

    /**
     * @param string $applicationKey
     *
     * @return Client
     */
    public function setApplicationKey($applicationKey)
    {
        $this->applicationKey = $applicationKey;

        return $this;
    }

    /**
     * Send a Series object to datadog
     *
     * @param Series $series
     * @throws Client\EmptySeriesException
     *
     * @return Client
     */
    public function sendSeries(Series $series)
    {
        $metrics = $series->getMetrics();
        if (empty($metrics)) {
            throw new EmptySeriesException('The series must contain metric data to send');
        }

        $this->send(
            self::ENDPOINT_SERIES . $this->getApiKey(),
            $series->toArray()
        );

        return $this;
    }

    /**
     * Send a Metric object to datadog
     *
     * The metric object will be encapsulated into a
     * dummy series, as the datadog API requires.
     *
     * @param Metric $metric
     * @throws EmptyMetricException
     *
     * @return Client
     */
    public function sendMetric(Metric $metric)
    {
        $points = $metric->getPoints();
        if (empty($points)) {
            throw new EmptyMetricException('The metric must contain points to send');
        }

        $this->sendSeries(new Series($metric));

        return $this;
    }

    /**
     * Send metric data to datadog
     *
     * The given values will be used to create a new Metric
     * object, encapsulated it into a Series and sent.
     *
     * @param string $name
     * @param array $points
     * @param array $options
     *
     * @return Client
     */
    public function metric($name, array $points, array $options = [])
    {
        return $this->sendMetric(
            Factory::buildMetric($name, $points, $options)
        );
    }

    /**
     * Send an event to datadog
     *
     * The given values will be used to create a new Event
     * object which will be sent.
     *
     * @param string $text
     * @param string $title
     * @param array $options
     *
     * @return Client
     */
    public function event($text, $title = '', array $options = [])
    {
        return $this->sendEvent(
            Factory::buildEvent($text, $title, $options)
        );
    }

    /**
     * Send an Event object to datadog
     *
     * @param Event $event
     *
     * @return Client
     */
    public function sendEvent(Event $event)
    {
        $this->send(
            self::ENDPOINT_EVENT . $this->getApiKey(),
            $event->toArray()
        );

        return $this;
    }

    /**
     * @param $url
     * @param $data
     * @throws Client\RequestException
     */
    protected function send($url, $data)
    {
        // Prepare request
        $guzzleClient = new \GuzzleHttp\Client();
        $request = $guzzleClient->createRequest('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($data),
        ]);

        // Send request
        $rawResponse = $guzzleClient->send($request);
        $responseCode = $rawResponse->getStatusCode();

        // Check for api errors
        if ($responseCode >= 400) {
            $response = json_decode($rawResponse->getBody(), true);
            $message = "Error $responseCode: ";
            if (isset($response['errors'])) {
                $message .= implode(' ', $response['errors']);
            }

            if (isset($response['warnings'])) {
                $message .= implode(' ', $response['warnings']);
            }

            throw new RequestException($message, $responseCode);
        }
    }
}
