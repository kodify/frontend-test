<?php

namespace Kodify\AdminBundle\Tests\Controller;

use \Mockery as M;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Scope;

use Kodify\AdminBundle\Controller\PornstarController;
use Kodify\TestsBundle\Tests\ControllerBaseClass;

/**
 * @group pornstar
 */
class PornstarControllerTest extends ControllerBaseClass
{
    public function testGetAction()
    {
        $className = 'Kodify\AdminBundle\Controller\PornstarController';

        $class = new \ReflectionClass($className);
        $method = $class->getMethod('getAction');
        $method->setAccessible(true);

        $controller = M::mock($className);
        $controller->shouldReceive('renderTable')->once();

        $result = $method->invokeArgs($controller, array(new Request()));

        $this->assertNotNull(\PHPUnit_Framework_Assert::readAttribute($controller, 'indexKey'));
        $this->assertTrue(\PHPUnit_Framework_Assert::readAttribute($controller, 'addAction'));

        $actions = \PHPUnit_Framework_Assert::readAttribute($controller, 'actions');
        $this->assertEquals($actions[0], 'edit');
    }

    public function testDefineTableHeader()
    {
        $cnt = new PornstarController();
        $result = $cnt->defineTableHeader();

        $this->assertTrue(is_array($result));

        foreach ($result as $column) {
            $this->assertArrayHasKey('label', $column);
            $this->assertArrayHasKey('key', $column);
        }
    }

    public function testGetDefaultSort()
    {
        $cnt = new PornstarController();
        $result = $cnt->getDefaultSort();
        $this->assertTrue(is_array($result));
    }

    public function testGetSortWithDefault()
    {
        $className = 'Kodify\AdminBundle\Controller\PornstarController';

        $class = new \ReflectionClass($className);
        $method = $class->getMethod('getSort');
        $method->setAccessible(true);

        $controller = new PornstarController();

        $container = new Container();
        $container->addScope(new Scope('request'));
        $container->enterScope('request');
        $container->set('request', new Request(), 'request');

        $controller->setContainer($container);

        $result = $method->invokeArgs($controller, array());
        $default = $controller->getDefaultSort();

        foreach ($default as $field => $dir) {
            $this->assertArrayHasKey($field, $result);
            $this->assertArrayHasKey('field', $result[$field]);
            $this->assertArrayHasKey('direction', $result[$field]);
            $this->assertEquals($field, $result[$field]['field']);
            $this->assertEquals($dir, $result[$field]['direction']);
        }
    }

    public function testAddPornstarAction()
    {
        $controller = new PornstarController();
        $request = new Request();
        $request->setMethod('POST');

        $routerMock = M::mock();
        $routerMock->shouldReceive('generate')->once()->andReturn('test');

        $formMock = M::mock('Symfony\Tests\Component\Form\FormInterface')
            ->shouldReceive('createView')->once()
            ->shouldReceive('bind')->once()
            ->shouldReceive('getName')->once()
            ->shouldReceive('isValid')->once()->andReturn(false)
            ->getMock();


        $formFactoryMock = M::mock('Symfony\Component\Form\FormFactoryInterface');
        $formFactoryMock->shouldReceive('create')->once()->andReturn($formMock);

        $templatingMock = M::mock('Symfony\Component\Templating\EngineInterface');
        $templatingMock->shouldReceive('renderResponse')->once();

        $emMock = M::mock('Doctrine\ORM\EntityManager');

        $doctrineMock = M::mock('Doctrine');

        $sessionMock =  new Session(new MockArraySessionStorage());

        $container = new Container();
        $container->addScope(new Scope('request'));
        $container->enterScope('request');
        $container->set('request', $request, 'request');
        $container->set('router', $routerMock);
        $container->set('templating', $templatingMock);
        $container->set('form.factory', $formFactoryMock);
        $container->set('doctrine', $doctrineMock);
        $container->set('session', $sessionMock);

        $controller->setContainer($container);
        $controller->addAction($request);

        $this->assertTrue(true, 'If we arrive here everything was called in the correct order');
    }

    public function testAutocompleteAction()
    {
        $requestMock = M::mock('Symfony\Component\HttpFoundation\Request');
        $requestMock->shouldReceive('get')->with('term')->andReturn('aaa');

        $responseObjMock = M::mock()
            ->shouldReceive('getId')->times(1)->andReturn('1')
            ->shouldReceive('getName')->times(1)->andReturn('test')
            ->shouldReceive('getThumbnailUrl')->times(1)->andReturn('url')
            ->getMock();

        $repoMock = M::mock('')
            ->shouldReceive('getPornstarList')->with('aaa')->andReturn(array($responseObjMock))
            ->getMock();
        $doctrineMock = M::mock('')->shouldReceive('getRepository')->andReturn($repoMock)->getMock();

        $controllerMock = M::mock('Kodify\AdminBundle\Controller\PornstarController[getDoctrine]');
        $controllerMock->shouldReceive('getDoctrine')->andReturn($doctrineMock);

        $response = $controllerMock->autocompleteAction($requestMock);

        $expectedResponse = array(array('id' => '1', 'label' => 'test', 'imgsrc' => 'url'));
        $this->assertEquals($response->getContent(), json_encode($expectedResponse));
    }

    public function testValidateAction()
    {
        $requestMock = M::mock('Symfony\Component\HttpFoundation\Request');
        $requestMock->shouldReceive('get')->with('list')->andReturn('p1,p2,p3');
        $expectedResponse = array(1,2,3);

        $repoMock = M::mock('')->shouldReceive('validatePornstarList')->andReturn($expectedResponse)->getMock();
        $doctrineMock = M::mock('')->shouldReceive('getRepository')->andReturn($repoMock)->getMock();

        $controllerMock = M::mock('Kodify\AdminBundle\Controller\PornstarController[getDoctrine]');
        $controllerMock->shouldReceive('getDoctrine')->andReturn($doctrineMock);

        $response = $controllerMock->validateAction($requestMock);
        $this->assertEquals($response->getContent(), json_encode($expectedResponse));
    }
}
