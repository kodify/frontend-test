<?php

namespace Kodify\AdminBundle\Tests\Extension;

use Kodify\AdminBundle\Extension\DuplicatedVideoExtension;

class DuplicatedVideoExtensionTest extends \PHPUnit_Framework_TestCase
{
    public static $currentTime = null;
    private $extension = null;

    public function setUp()
    {
        $putioRepoMock = \Mockery::mock()
            ->shouldReceive('getDuplicatedVideoInfo')->andReturn('supu')
            ->getMock();

        $doctrineMock = \Mockery::mock('Symfony\Bridge\Doctrine\RegistryInterface')
            ->shouldReceive('getRepository')->with('KodifyAdminBundle:PutIoFile')->andReturn($putioRepoMock)
            ->getMock();

        $this->extension = new DuplicatedVideoExtension($doctrineMock);

        parent::setUp();
    }

    public function testGetFunctions()
    {
        $response = $this->extension->getFunctions();

        $this->assertArrayHasKey('get_duplicated_video_info', $response);
    }

    public function testGetName()
    {
        $response = $this->extension->getName();
        $this->assertTrue(is_string($response));
    }

    public function testGetDuplicatedVideoInfo()
    {
        $this->assertSame('supu',$this->extension->getDuplicatedVideoInfo(1));
    }

}

