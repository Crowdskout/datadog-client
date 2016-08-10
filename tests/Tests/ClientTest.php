<?php

namespace Elite50\DataDogClient\Tests;

use Elite50\DataDogClient\Client;
use Elite50\DataDogClient\Event;
use Elite50\DataDogClient\Series;
use Elite50\DataDogClient\Series\Metric;

class ClientTest extends \PHPUnit_Framework_TestCase {

    const API_KEY = 'test';
    const APP_KEY = 'test';

    /**
     * @var Client
     */
    protected $client;

    protected function setUp() {
        $this->client = new Client(self::API_KEY, self::APP_KEY);
    }

    public function testConstructor() {
        $client = new Client(self::API_KEY);
        $this->assertInstanceOf('Elite50\DataDogClient\Client', $client);
    }

    public function testGetAndSetApiKey() {
        $this->assertEquals(self::API_KEY, $this->client->getApiKey());
        $this->client->setApiKey('test_api_key');
        $this->assertEquals('test_api_key', $this->client->getApiKey());
    }

    public function testGetAndSetApplicationKey() {
        $this->assertEquals(self::APP_KEY, $this->client->getApplicationKey());
        $this->client->setApplicationKey('test_app_key');
        $this->assertEquals('test_app_key', $this->client->getApplicationKey());
    }

    public function testSendSeries() {
        $series  = new Series();
        $metric1 = new Metric('test.metric.name', array(
            array(time(), 20),
            array(time() - 5, 15),
            array(time() - 10, 10),
        ));
        $metric1->setType(Metric::TYPE_GAUGE)
            ->setHost('host1.com')
            ->addTag('test', 'tag');

        $series->addMetric($metric1);

        $metric2 = new Metric('test.metric2.name', array(
            array(time(), 18),
            array(time() - 1, 21),
            array(time() - 2, 12),
        ));
        $metric2->setType(Metric::TYPE_COUNTER);

        $series->addMetric($metric2);

        $this->client->sendSeries($series);
    }

    public function testSendEvent() {
        $event = new Event('TestEvent', 'This is a testevent');
        $event->addTag('foo', 'bar')
            ->setAlertType(Event::TYPE_SUCCESS)
            ->setSourceTypeName(Event::SOURCE_MYAPPS)
            ->setAggregationKey('unittest')
            ->setPriority(Event::PRIORITY_LOW);

        $this->client->sendEvent($event);
    }

    public function testSendMetric() {
        $metric1 = new Metric('test.metric.name', array(
            array(time(), 20),
            array(time() - 5, 15),
            array(time() - 10, 10),
        ));

        $this->client->sendMetric($metric1);
    }

    public function testCreatingMetricWithEmptyPointArray() {
        $metric = new Metric('test.metric.name', array());
        $this->assertEmpty($metric->getPoints());
    }

    /**
     * @expectedException \Elite50\DataDogClient\Client\EmptyMetricException
     */
    public function testSendingEmptyMetricThrowsException() {
        $metric = new Metric('test.metric.name', array(20));
        $metric->removePoints();
        $this->client->sendMetric($metric);
    }

    public function testSendMetricWithShortcutMethod() {
        $this->client->metric(
            'shortcut.metric',
            array(
                array(time(), 20),
                array(time() - 5, 15),
                array(time() - 10, 10),
            )
        );

        $this->client->metric('shortcut.metric', array(20));

        $this->client->metric(
            'custom.metric',
            array(20),
            array(
                'host' => 'foo.com',
                'type' => Metric::TYPE_COUNTER
            )
        );
    }

    public function testSendEventWithShortcutMethod() {
        $this->client->event('Test Event', 'My Event');

        $this->client->event(
            'Test Event',
            'My Event',
            array(
                'priority'      => Event::PRIORITY_LOW,
                'date_happened' => time() - 1234
            )
        );
    }

    /**
     * @expectedException \Elite50\DataDogClient\Client\EmptySeriesException
     */
    public function testDoNotSendEmptySeries() {
        $series = new Series();

        $this->client->sendSeries($series);
    }
}