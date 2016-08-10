<?php
namespace Elite50\DataDogClient;

/**
 * Class AbstractDataObject
 *
 * This class defines all methods and properties that are the
 * same for every object that can be sent through the datadog API.
 *
 * @package Bayer\DataDogClient
 */
abstract class AbstractDataObject implements TagContainerInterface
{
    /**
     * @inheritdoc
     */
    protected $tags = [];

    /**
     * @inheritdoc
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @inheritdoc
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addTag($name, $value)
    {
        $this->tags[$name] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeTag($name)
    {
        if (isset($this->tags[$name])) {
            unset($this->tags[$name]);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function removeTags()
    {
        $this->tags = [];

        return $this;
    }
}
