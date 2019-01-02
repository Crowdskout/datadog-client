# Datadog PHP Client
> Lightweight php-only datadog client

This is a simple php client for datadog which does not require setting up the [DataDog Agent](https://app.datadoghq.com/account/settings#agent)
This library supports sending metric data or events at the moment, as those should be the most common use cases.
Feel free to extend it to your own needs or sending pull requests.

## Installation

This library can be installed using composer

```
composer require crowdskout/datadog-client
```


## Getting Started

To start sending requests to Datadog you need to provider your personal API key first. You find that key in [Datadog API Settings](https://app.datadoghq.com/account/settings#api).

```
$client = new Client('mysecretapikey');
```

## Sending Events


Events can be sent directly to Datadog using the provided Event class:

```
$myEvent = new Event('This is my test event', 'Optional event title');
$client->sendEvent($myEvent);
```


Additional properties can be set the usual way:

```
$myEvent->setAlertType(Event::TYPE_ERROR);
```


#### There is also a shortcut method which handles all that stuff for you:

```
// Create and send event in one call
$client->event('My test event', 'Optional event title', array(
    'alert_type' => Event::TYPE_ERROR
));
```

### Event Properties


#### dateHappened

Type: `integer`

Timestamp of the event. Defaults to current timestamp.

#### priority

Type: `Event::PRIORITY_`

Event priority. Datadog supports LOW and NORMAL

#### alertType

Type: `Event::TYPE_`

Event alert_type. Datadog supports INFO, WARNING, ERROR and SUCCESS

#### aggregationKey

Type: `string`

Arbitary string used to group events

## Sending Metrics

Datadog requires metric data to be encapsulated into a series. One series contains one or more metric objects, which itself contains one or more measurement points.


```
// Create Series
$mySeries = new Series();

// Create a new metric with multiple points
$firstMetric = new Metric('my.metric.name', array(
    array(20),            // Dummy points
    array(13456789, 30),  // Point with timestamp set
    array(40),            // If not set, timestamp default to current time
));

// Create a new metric with one point
$secondMetric = new Metric('my.second.metric', array(20));

$mySeries->addMetrics(array(
    $firstMetric,
    $secondMetric
));

// Send data
$client->sendSeries($mySeries);
```

If you want to send just one metric at a time, you can use the Client::sendMetric method:

```
// Create a new metric with multiple points
$myMetric = new Metric('my.metric.name', array(
    array(20),            // Dummy points
    array(13456789, 30),  // Point with timestamp set
    array(40),            // If not set, timestamp default to current time
));

$client->sendMetric($myMetric);
```

#### Again, there is a shortcut method which can handle that for you:

```
$client->metric('my.test.metric', array(
    array(20),
    array(13456789, 30),
    array(40),
));
```


### Metric Properties


#### name

Type: `string`

Name of the metric. Can contain underscored or dots for easier grouping, e.g. web.exception.404

#### type

Type: `Metric::TYPE_`

Type of the metric. Datadog supports gauge or counter

#### host

Type: `string`

Hostname of the source machine

#### points

Type: `array`

Array of data points.

A point consists of an optional timestamp and a numeric value. If
no timestamp is specified, the current timestamp will be used. Order
matters. If a timestamp is specified, it should be the first value.

Examples:
```
  // Timestamp will be set to time()
  $simplePoint = array(20);
  
  // Set timestamp to explicit value
  $pointWithTimestamp = array(1234567, 20);
```





