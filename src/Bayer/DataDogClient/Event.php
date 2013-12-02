<?php

namespace Bayer\DataDogClient;

use Bayer\DataDogClient\Event\InvalidTypeException;
use Bayer\DataDogClient\Event\InvalidSourceTypeException;
use Bayer\DataDogClient\Event\InvalidPriorityException;

/**
 * Class Event
 *
 * An event is shown in the datadog timeline and consists of
 * at least an event text.
 *
 * @package Bayer\DataDogClient
 */
class Event {

    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_LOW    = 'low';

    const TYPE_INFO    = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR   = 'error';
    const TYPE_SUCCESS = 'success';

    const SOURCE_NAGIOS     = 'nagios';
    const SOURCE_HUDSON     = 'hudson';
    const SOURCE_JENKINS    = 'jenkins';
    const SOURCE_USER       = 'user';
    const SOURCE_MYAPPS     = 'my apps';
    const SOURCE_FEED       = 'feed';
    const SOURCE_CHEF       = 'chef';
    const SOURCE_PUPPET     = 'puppet';
    const SOURCE_GIT        = 'git';
    const SOURCE_BITBUCKET  = 'gitbucket';
    const SOURCE_FABRIC     = 'fabric';
    const SOURCE_CAPISTRANO = 'capistrano';

    /**
     * Title of the event
     *
     * @var string
     */
    protected $title;

    /**
     * The event message
     *
     * @var string
     */
    protected $text;

    /**
     * Timestamp when the event occured
     *
     * @var int
     */
    protected $timestamp;

    /**
     * Event priority
     *
     * Datadog supports low and normal
     *
     * @var string
     */
    protected $priority;

    /**
     * Tags of the event
     *
     * @var array
     */
    protected $tags = array();

    /**
     * Event type
     *
     * Datadog supports info, warning, error and success
     *
     * @var string
     */
    protected $type;

    /**
     * Arbitary string used to group events
     *
     * @var string
     */
    protected $aggregationKey;

    /**
     * Type of the event source
     *
     * This indicated which source fired the event.
     * Datadog supports:
     * nagios, hudson, jenkins, user, my apps, feed,
     * chef, puppet, git, bitbucket, fabric, capistrano
     *
     * @var string
     */
    protected $sourceType;

    /**
     * @param string $text
     * @param string $title
     */
    public function __construct($text, $title = '') {
        $this->setText($text)
            ->setTitle($title)
            ->setTimestamp(time())
            ->setPriority(self::PRIORITY_NORMAL)
            ->setType(self::TYPE_INFO);
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Event
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return Event
     */
    public function setText($text) {
        $this->text = $text;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp() {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     *
     * @return Event
     */
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriority() {
        return $this->priority;
    }

    /**
     * @param mixed $priority
     * @throws Event\InvalidPriorityException
     *
     * @return Event
     */
    public function setPriority($priority) {
        if (!$this->isValidPriority($priority)) {
            throw new InvalidPriorityException('Priority must be on of Event::PRIORITY_*');
        }
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return array
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * @param array $tags
     *
     * @return Event
     */
    public function setTags($tags) {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return Event
     */
    public function addTag($name, $value) {
        $this->tags[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return Event
     */
    public function removeTag($name) {
        if (isset($this->tags[$name])) {
            unset($this->tags[$name]);
        }

        return $this;
    }

    /**
     * @return Event
     */
    public function removeTags() {
        $this->tags = array();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @throws InvalidTypeException
     *
     * @return Event
     */
    public function setType($type) {
        if (!$this->isValidType($type)) {
            throw new InvalidTypeException('Type must be one of Event::TYPE_*');
        }
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAggregationKey() {
        return $this->aggregationKey;
    }

    /**
     * @param mixed $aggregationKey
     *
     * @return Event
     */
    public function setAggregationKey($aggregationKey) {
        $this->aggregationKey = $aggregationKey;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSourceType() {
        return $this->sourceType;
    }

    /**
     * @param mixed $sourceType
     * @throws Event\InvalidSourceTypeException
     *
     * @return Event
     */
    public function setSourceType($sourceType) {
        if (!$this->isValidSourceType($sourceType)) {
            throw new InvalidSourceTypeException('SourceTyoe must be on of Event::SOURCE_*');
        }
        $this->sourceType = $sourceType;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray() {
        $data = array(
            'title'         => $this->getTitle(),
            'text'          => $this->getText(),
            'date_happened' => $this->getTimestamp(),
            'priority'      => $this->getPriority(),
            'alert_type'    => $this->getType(),
        );

        if ($tags = $this->getTags()) {
            $data['tags'] = array();
            foreach ($tags as $tag => $value) {
                $data['tags'][] = "$tag:$value";
            }
        }

        if ($this->getAggregationKey()) {
            $data['aggregation_key'] = $this->getAggregationKey();
        }

        if ($this->getSourceType()) {
            $data['source_type_name'] = $this->getSourceType();
        }

        return $data;
    }

    protected function isValidType($type) {
        return in_array(
            $type,
            array(
                self::TYPE_ERROR,
                self::TYPE_INFO,
                self::TYPE_SUCCESS,
                self::TYPE_WARNING,
            )
        );
    }

    protected function isValidSourceType($sourceType) {
        return in_array(
            $sourceType,
            array(
                self::SOURCE_NAGIOS,
                self::SOURCE_HUDSON,
                self::SOURCE_JENKINS,
                self::SOURCE_USER,
                self::SOURCE_MYAPPS,
                self::SOURCE_FEED,
                self::SOURCE_CHEF,
                self::SOURCE_PUPPET,
                self::SOURCE_GIT,
                self::SOURCE_BITBUCKET,
                self::SOURCE_FABRIC,
                self::SOURCE_CAPISTRANO,
            )
        );
    }

    protected function isValidPriority($priority) {
        return in_array(
            $priority,
            array(
                self::PRIORITY_NORMAL,
                self::PRIORITY_LOW,
            )
        );
    }

}