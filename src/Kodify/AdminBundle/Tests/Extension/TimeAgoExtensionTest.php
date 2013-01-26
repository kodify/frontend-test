<?php

namespace Kodify\AdminBundle\Extension;


function time()
{
    return 1358328904;
}

namespace Kodify\AdminBundle\Tests\Extension;

use Kodify\AdminBundle\Extension\TimeAgoExtension;

class TimeAgoExtensionTest extends \PHPUnit_Framework_TestCase
{
    public static $currentTime = null;

    public function testGetFilters()
    {
        $ext = new TimeAgoExtension();
        $response = $ext->getFilters();

        $this->assertArrayHasKey('timeAgo', $response);
    }

    public function testGetName()
    {
        $ext = new TimeAgoExtension();
        $response = $ext->getName();

        $this->assertTrue(is_string($response));
    }

    public function timeAgoDataProvider()
    {
        $currentTime = 1358328904;

        $dt1 = new \DateTime();
        $dt1->setTimestamp($currentTime - 10);

        $dt2 = new \DateTime();
        $dt2->setTimestamp($currentTime - 60);

        $dt3 = new \DateTime();
        $dt3->setTimestamp($currentTime - 90);

        $dt4 = new \DateTime();
        $dt4->setTimestamp($currentTime - 120);

        $dt5 = new \DateTime();
        $dt5->setTimestamp($currentTime - 3600);

        $dt6 = new \DateTime();
        $dt6->setTimestamp($currentTime - 31536000);

        $dt7 = new \DateTime();
        $dt7->setTimestamp($currentTime);

        return array(
            array($dt1, '10 seconds'),
            array($dt2, '1 minute'),
            array($dt3, '1 minute'),
            array($dt4, '2 minutes'),
            array($dt5, '1 hour'),
            array($dt6, '1 year'),
            array($dt7, '')
        );
    }

    /**
     * @dataProvider timeAgoDataProvider
     */
    public function testTimeAgo($input, $expected)
    {
        $ext = new TimeAgoExtension();
        $response = $ext->timeAgo($input);

        $this->assertEquals($expected, $response);
    }
}