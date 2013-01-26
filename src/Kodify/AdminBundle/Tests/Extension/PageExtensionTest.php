<?php

namespace Kodify\AdminBundle\Tests\Extension;

use Kodify\AdminBundle\Extension\PageExtension;

class PageExtensionTest extends \PHPUnit_Framework_TestCase
{
    public static $currentTime = null;
    private $extension = null;

    public function setUp()
    {
        $controllerName = 'Kodify\AdminBundle\Controller\VideoController::getAction';

        $requestMock = \Mockery::mock('Symfony\Component\HttpFoundation\Request')
            ->shouldReceive('get')->with('_controller')->andReturn($controllerName)
            ->getMock();

        $this->extension = new PageExtension($requestMock);

        parent::setUp();
    }

    public function testGetFunctions()
    {
        $response = $this->extension->getFunctions();

        $this->assertArrayHasKey('get_controller_name', $response);
        $this->assertArrayHasKey('get_action_name', $response);
    }

    public function testGetName()
    {
        $response = $this->extension->getName();
        $this->assertTrue(is_string($response));
    }

    public function testGetControllerName()
    {
        $response = $this->extension->getControllerName();
        $this->assertEquals($response, 'video');
    }

    public function testGetActionName()
    {
        $response = $this->extension->getActionName();
        $this->assertEquals($response, 'get');
    }
}