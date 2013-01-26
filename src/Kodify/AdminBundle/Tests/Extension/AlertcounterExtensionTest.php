<?php

namespace Kodify\AdminBundle\Tests\Extension;

use Kodify\AdminBundle\Extension\AlertCounterExtension;

class AlertCounterExtensionTest extends \PHPUnit_Framework_TestCase
{
    public static $currentTime = null;
    private $extension = null;

    public function setUp()
    {
        $controllerName = 'Kodify\AdminBundle\Controller\VideoController::getAction';

        $putioRepoMock = \Mockery::mock()
            ->shouldReceive('getPutioNotDeletedFilesCount')->andReturn(11)
            ->getMock();

        $videoRepoMock = \Mockery::mock()
            ->shouldReceive('getReadyToCutVideosCount')->andReturn(15)
            ->getMock();

        $doctrineMock = \Mockery::mock('Symfony\Bridge\Doctrine\RegistryInterface')
            ->shouldReceive('getRepository')->with('KodifyAdminBundle:PutIoFile')->andReturn($putioRepoMock)
            ->shouldReceive('getRepository')->with('KodifyAdminBundle:Video')->andReturn($videoRepoMock)
            ->getMock();

        $this->extension = new AlertCounterExtension($doctrineMock);

        parent::setUp();
    }

    public function testGetFunctions()
    {
        $response = $this->extension->getFunctions();

        $this->assertArrayHasKey('get_putio_alert', $response);
        $this->assertArrayHasKey('get_ready_alert', $response);
    }

    public function testGetName()
    {
        $response = $this->extension->getName();
        $this->assertTrue(is_string($response));
    }

    public function testGetPutioAlert()
    {
        $response = $this->extension->getPutioAlert();
        $this->assertEquals($response, '11');
    }

    public function testGetReadyAlert()
    {
        $response = $this->extension->getReadyAlert();
        $this->assertEquals($response, '15');
    }
}

