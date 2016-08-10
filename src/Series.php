<?php

namespace Elite50\DataDogClient;

use Elite50\DataDogClient\Series\Metric;
use Elite50\DataDogClient\Series\MetricNotFoundException;

/**
 * Class Series
 *
 * Metric data can only be sent to datadog encapsulated
 * in a series. A series is a simple container for one
 * or more metric objects.
 *
 * @package Bayer\DataDogClient
 */
class Series {
    /**
     * Metrics in this series
     *
     * @var Metric[]
     */
    protected $metrics = array();

    /**
     * @param Metric|Metric[] $metrics
     */
    public function __construct($metrics = array()) {
        if ($metrics instanceof Metric) {
            $metrics = array($metrics);
        }
        $this->setMetrics($metrics);
    }

    /**
     * @return Metric[]
     */
    public function getMetrics() {
        return $this->metrics;
    }

    /**
     * @param $name
     * @throws Series\MetricNotFoundException
     *
     * @return Metric
     */
    public function getMetric($name) {
        if (isset($this->metrics[$name])) {
            return $this->metrics[$name];
        }

        throw new MetricNotFoundException("Metric $name not found");
    }

    /**
     * @param Metric[] $metrics
     *
     * @return Series
     */
    public function setMetrics(array $metrics) {
        $this->removeMetrics();
        $this->addMetrics($metrics);

        return $this;
    }

    /**
     * @param Metric $metric
     *
     * @return Series
     */
    public function addMetric(Metric $metric) {
        $this->metrics[$metric->getName()] = $metric;

        return $this;
    }

    public function addMetrics(array $metrics) {
        foreach ($metrics as $metric) {
            $this->addMetric($metric);
        }

        return $this;
    }

    /**
     * @param $name
     * @throws MetricNotFoundException
     *
     * @return Series
     */
    public function removeMetric($name) {
        if (isset($this->metrics[$name])) {
            unset($this->metrics[$name]);

            return $this;
        }

        throw new MetricNotFoundException("Metric $name not found");
    }

    /**
     * @return Series
     */
    public function removeMetrics() {
        $this->metrics = array();

        return $this;
    }

    /**
     * @return array
     */
    public function toArray() {
        $data = array();
        foreach ($this->getMetrics() as $metric) {
            /** @var Metric $metric */
            $data[] = (object)$metric->toArray();
        }

        return array(
            'series' => $data
        );
    }
}