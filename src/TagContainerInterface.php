<?php

namespace Elite50\DataDogClient;

/**
 * Interface TagContainerInterface
 *
 * Should be implemented by classes which can contain tags
 *
 * @package Bayer\DataDogClient
 */
interface TagContainerInterface {
    /**
     * Get all tags
     *
     * @return array
     */
    public function getTags();

    /**
     * Set all tags, overwriting existing ones
     *
     * @param array $tags
     */
    public function setTags($tags);

    /**
     * Add a single tag
     *
     * @param string $name
     * @param string $value
     */
    public function addTag($name, $value);

    /**
     * Remove a tag by name
     *
     * @param string $name
     */
    public function removeTag($name);

    /**
     * Remove all existing tags
     */
    public function removeTags();
}