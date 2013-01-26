<?php

namespace Kodify\AdminBundle\Tests\Entity;

use \Mockery as M;

use Kodify\AdminBundle\Entity\Video;


/**
 * @group video
 */
class VideoControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetThumbnails()
    {
        $video = new Video();
        $video->setDurationInMilliseconds(31000);
        $video->setFilename('filename.avi');
        $video->setThumbnailsTranscoderId('strangeId');
        $interval = 30;
        $response = $video->getThumbnails($interval);

        $this->assertEquals(2, count($response));

        $this->assertEquals('strangeId_1.jpg', $response[0]['url']);
        $this->assertEquals(0, $response[0]['time']);
        $this->assertEquals('strangeId_2.jpg', $response[1]['url']);
        $this->assertEquals(30, $response[1]['time']);
    }
}