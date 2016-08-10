<?php

namespace Elite50\DataDogClient\Tests;

use Elite50\DataDogClient\Event;

class EventTest extends \PHPUnit_Framework_TestCase {
    public function testGetAndSetTitleAndText() {
        $event = new Event('Text', 'Title');
        $this->assertEquals('Title', $event->getTitle());
        $this->assertEquals('Text', $event->getText());

        $event->setTitle('NewTitle');
        $this->assertEquals('NewTitle', $event->getTitle());

        $event->setText('NewText');
        $this->assertEquals('NewText', $event->getText());
    }

    public function testGetAndSetTimestamp() {
        $event = new Event('Text', 'Title');
        $this->assertEquals(time(), $event->getDateHappened());

        $event->setDateHappened(123456789);
        $this->assertEquals(123456789, $event->getDateHappened());
    }

    public function testGetAndSetPriority() {
        $event = new Event('Text', 'Title');
        $this->assertEquals(Event::PRIORITY_NORMAL, $event->getPriority());

        $event->setPriority(Event::PRIORITY_LOW);
        $this->assertEquals(Event::PRIORITY_LOW, $event->getPriority());
    }

    /**
     * @expectedException \Elite50\DataDogClient\Event\InvalidPriorityException
     */
    public function testInvalidPriorityThrowsException() {
        $event = new Event('Text', 'Title');
        $event->setPriority('foo');
    }

    public function testGetAndSetTags() {
        $event = new Event('Text', 'Title');
        $this->assertEmpty($event->getTags());
        $this->assertEquals(array(), $event->getTags());

        $event->addTag('foo', 'bar');
        $this->assertCount(1, $event->getTags());

        $event->removeTag('foo');
        $this->assertCount(0, $event->getTags());

        $event2 = new Event('Text', 'Title');
        $this->assertCount(0, $event2->getTags());
        $event2->setTags(
            array(
                array('foo', 'bar'),
                array('bar', 'baz')
            )
        );
        $this->assertCount(2, $event2->getTags());
        $event2->removeTags();
    }

    public function testRemoveNonExistingTag() {
        $event = new Event('Text', 'Title');
        $event->removeTag('foo');
    }

    public function testGetAndSetAlertType() {
        $event = new Event('Text', 'Title');

        $this->assertEquals(Event::TYPE_INFO, $event->getAlertType());

        $event->setAlertType(Event::TYPE_ERROR);
        $this->assertEquals(Event::TYPE_ERROR, $event->getAlertType());
    }

    /**
     * @expectedException \Elite50\DataDogClient\Event\InvalidAlertTypeException
     */
    public function testInvalidTypeThrowsException() {
        $event = new Event('Text', 'Title');
        $event->setAlertType('foo');
    }

    public function testGetAndSetAggregationKey() {
        $event = new Event('Text', 'Title');
        $this->assertNull($event->getAggregationKey());

        $event->setAggregationKey('test');
        $this->assertEquals('test', $event->getAggregationKey());
    }

    public function testGetAndSetSourceType() {
        $event = new Event('Text', 'Title');
        $this->assertNull($event->getSourceTypeName());

        $event->setSourceTypeName(Event::SOURCE_NAGIOS);
        $this->assertEquals(Event::SOURCE_NAGIOS, $event->getSourceTypeName());
    }

    /**
     * @expectedException \Elite50\DataDogClient\Event\InvalidSourceTypeException
     */
    public function testInvalidSourceTypeThrowsException() {
        $event = new Event('Text', 'Title');
        $event->setSourceTypeName('foo');
    }
}