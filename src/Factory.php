<?php

namespace Elite50\DataDogClient;

use Elite50\DataDogClient\Factory\InvalidPropertyException;
use Elite50\DataDogClient\Series\Metric;

/**
 * Class Factory
 *
 * Simple factory to build metric or event objects dynamically
 *
 * @package Bayer\DataDogClient
 */
class Factory {
    /**
     * @param string $name
     * @param array  $points
     * @param array  $options
     *
     * @return Metric
     */
    public static function buildMetric($name, array $points, array $options = array()) {
        $metric = new  Metric($name, $points);

        foreach ($options as $property => $value) {
            self::setProperty($metric, $property, $value);
        }

        return $metric;
    }

    /**
     * @param string $text
     * @param string $title
     * @param array  $options
     *
     * @return Event
     */
    public static function buildEvent($text, $title = '', array $options = array()) {
        $event = new Event($text, $title);

        foreach ($options as $property => $value) {
            self::setProperty($event, $property, $value);
        }

        return $event;
    }

    /**
     * @param $object
     * @param $property
     * @param $value
     * @throws InvalidPropertyException
     */
    protected static function setProperty($object, $property, $value) {
        $method = self::getMethodName($property);

        if (!method_exists($object, $method)) {
            throw new InvalidPropertyException('Unable to call ' . get_class(
                    $object
                ) . '::' . $method . '(' . $value . ')');
        }
        $object->$method($value);
    }

    /**
     * Transform `under_score` property names to `getUnderScore` method names
     *
     * @param $string
     * @return mixed
     */
    protected static function getMethodName($string) {
        return 'set' . preg_replace_callback(
            '/_([a-z])/',
            function ($chunk) {
                return strtoupper($chunk[1]);
            },
            $string
        );
    }
}