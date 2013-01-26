<?php

namespace Kodify\AdminBundle\Tests\Controller;

use \Mockery as M;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\DependencyInjection\Container;

use Kodify\AdminBundle\Controller\TagController;
use Kodify\TestsBundle\Tests\ControllerBaseClass;

/**
 * @group tag
 */
class TagControllerTest extends ControllerBaseClass
{
    public function testDefineTableHeader()
    {
        $cnt = new TagController();
        $result = $cnt->defineTableHeader();

        $this->assertTrue(is_array($result));

        foreach ($result as $column) {
            $this->assertArrayHasKey('label', $column);
            $this->assertArrayHasKey('key', $column);
        }
    }

    public function testAutocompleteAction()
    {
        $requestMock = M::mock('Symfony\Component\HttpFoundation\Request');
        $requestMock->shouldReceive('get')->with('term')->andReturn('aaa');

        $responseObjMock = M::mock()
            ->shouldReceive('getId')->times(1)->andReturn('1')
            ->shouldReceive('getName')->times(1)->andReturn('test')
            ->getMock();

        $repoMock = M::mock('')
            ->shouldReceive('getTagList')->with('aaa')->andReturn(array($responseObjMock))
            ->getMock();
        $doctrineMock = M::mock('')->shouldReceive('getRepository')->andReturn($repoMock)->getMock();

        $controllerMock = M::mock('Kodify\AdminBundle\Controller\TagController[getDoctrine]');
        $controllerMock->shouldReceive('getDoctrine')->andReturn($doctrineMock);

        $response = $controllerMock->autocompleteAction($requestMock);

        $expectedResponse = array(array('id' => '1', 'label' => 'test'));
        $this->assertEquals($response->getContent(), json_encode($expectedResponse));
    }

    public function testValidateAction()
    {
        $requestMock = M::mock('Symfony\Component\HttpFoundation\Request');
        $requestMock->shouldReceive('get')->with('list')->andReturn('p1,p2,p3');
        $expectedResponse = array(1,2,3);

        $repoMock = M::mock('')->shouldReceive('validateTagList')->andReturn($expectedResponse)->getMock();
        $doctrineMock = M::mock('')->shouldReceive('getRepository')->andReturn($repoMock)->getMock();

        $controllerMock = M::mock('Kodify\AdminBundle\Controller\TagController[getDoctrine]');
        $controllerMock->shouldReceive('getDoctrine')->andReturn($doctrineMock);

        $response = $controllerMock->validateAction($requestMock);
        $this->assertEquals($response->getContent(), json_encode($expectedResponse));
    }


}
