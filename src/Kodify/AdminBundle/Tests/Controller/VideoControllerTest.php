<?php

namespace Kodify\AdminBundle\Tests\Controller;

use \Mockery as M;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\Container;

use Kodify\AdminBundle\Controller\VideoController;
use Kodify\AdminBundle\Entity\Video;
use Kodify\TestsBundle\Tests\ControllerBaseClass;

/**
 * @group video
 */
class VideoControllerTest extends ControllerBaseClass
{
    protected $className = 'Kodify\AdminBundle\Controller\VideoController';

    public function testGetAction()
    {
        $method = $this->getMethod($this->className, 'getAction');

        $controller = M::mock($this->className);
        $controller->shouldReceive('renderTable')->once();

        $result = $method->invokeArgs($controller, array(new Request()));

        $this->assertNotNull(\PHPUnit_Framework_Assert::readAttribute($controller, 'indexKey'));
        $this->assertFalse(\PHPUnit_Framework_Assert::readAttribute($controller, 'addAction'));

        $actions = \PHPUnit_Framework_Assert::readAttribute($controller, 'actions');
        $this->assertArrayHasKey('route_name', $actions[0]);
        $this->assertArrayHasKey('ico', $actions[0]);
    }

    public function testCancelCutVideoAction()
    {
        $videoMock = M::mock()->shouldReceive('getStatus')->andReturn(0)->getMock();
        $userMock = M::mock()->shouldReceive('getUsername')->andReturn('admin')->getMock();

        $this->cancelCutVideoAction($videoMock, $userMock);
        $this->assertTrue(true, 'If we arrive here everything was called in the correct order');
    }

    public function testCancelCutVideoActionWithCorrectStatusAndWrongUser()
    {
        $videoMock = M::mock();
        $videoMock->shouldReceive('getStatus')->andReturn(Video::BLOCKED);
        $videoMock->shouldReceive('getBlockedBy')->andReturn('111');

        $userMock = M::mock()->shouldReceive('getUsername')->andReturn('admin')->getMock();

        $this->cancelCutVideoAction($videoMock, $userMock);
    }

    public function testCancelCutVideoActionWithCorrectStatusAndUser()
    {
        $videoMock = M::mock();
        $videoMock->shouldReceive('getStatus')->andReturn(Video::BLOCKED);
        $videoMock->shouldReceive('getBlockedBy')->andReturn('admin');
        $videoMock->shouldReceive('setStatus')->andReturn(0);
        $videoMock->shouldReceive('setBlockedBy')->andReturn(0);

        $managerMock = M::mock();
        $managerMock->shouldReceive('persist')->times(1);
        $managerMock->shouldReceive('flush')->times(1);

        $userMock = M::mock()->shouldReceive('getUsername')->andReturn('admin')->getMock();

        $this->cancelCutVideoAction($videoMock, $userMock, $managerMock);
        $this->assertTrue(true, 'If we arrive here everything was called in the correct order');
    }

    private function cancelCutVideoAction($videoMock, $userMock, $managerMock = null)
    {
        $videoId = 10;

        if ($managerMock == null) {
            $managerMock = M::mock();
        }

        $repoMock = M::mock();
        $repoMock->shouldReceive('findOneById')->with($videoId)->andReturn($videoMock);

        $managerMock->shouldReceive('getRepository')->andReturn($repoMock);

        $doctrineMock = M::mock();
        $doctrineMock->shouldReceive('getManager')->andReturn($managerMock);

        $controllerMock = M::mock($this->className . '[getDoctrine, getUser, generateUrl]');
        $controllerMock->shouldReceive('getDoctrine')->andReturn($doctrineMock);
        $controllerMock->shouldReceive('getUser')->andReturn($userMock);
        $controllerMock->shouldReceive('generateUrl')->andReturn('route_url');

        $controllerMock->cancelCutVideoAction($videoId);
        $this->assertTrue(true, 'If we arrive here everything was called in the correct order');
    }

    public function testMarkAsUnsuitableActionWithoutMoreVideos()
    {
        $response = $this->markAsUnsuitableAction(null);
        $this->assertEquals($response->getTargetUrl(), 'get_video');
    }

    public function testMarkAsUnsuitableActionWithMoreVideos()
    {
        $videoMock2 = M::mock('\Kodify\AdminBundle\Entity\Video')
            ->shouldReceive('getId')->times(1)->andReturn(1)
            ->getMock();

        $response = $this->markAsUnsuitableAction($videoMock2);
        $this->assertEquals($response->getTargetUrl(), 'cut_video');
    }

    private function markAsUnsuitableAction($videoMock2)
    {
        $videoId = 10;
        $videoMock = M::mock()
            ->shouldReceive('setStatus')->times(1)->andReturn(0)
            ->getMock();

        $managerMock = M::mock()
            ->shouldReceive('persist')->times(1)
            ->shouldReceive('flush')->times(1)
            ->getMock();

        $repoMock = M::mock()
            ->shouldReceive('findOneById')->with($videoId)->andReturn($videoMock)
            ->shouldReceive('getNextReadyVideo')->andReturn($videoMock2)
            ->getMock();

        $managerMock->shouldReceive('getRepository')->andReturn($repoMock);

        $doctrineMock = M::mock();
        $doctrineMock->shouldReceive('getManager')->andReturn($managerMock);

        $returnPathsMethod = function ($request_path, $params = array()) {
            return $request_path;
        };

        $controllerMock = M::mock($this->className . '[getDoctrine, generateUrl]')
            ->shouldReceive('getDoctrine')->andReturn($doctrineMock)
            ->shouldReceive('generateUrl')->andReturnUsing($returnPathsMethod)
            ->getMock();

        return $controllerMock->markAsUnsuitableAction($videoId);
    }

    public function testCutVideoActionBlockedByOtherUser()
    {
        $videoMock = M::mock('Kodify\AdminBundle\Entity\Video');
        $videoMock->shouldReceive('getStatus')->andReturn(Video::BLOCKED);
        $videoMock->shouldReceive('getBlockedBy')->andReturn('username');

        $sessionMock = M::mock()->shouldReceive('setFlash')->times(1)->with('error', 'This video is blocked by username')->getMock();

        $controllerMock = M::mock($this->className . '[getDoctrine, getUser, generateUrl, render, get]');
        $controllerMock->shouldReceive('get')->times(1)->andReturn($sessionMock);

        $this->cutVideoAction($controllerMock, $videoMock);
        $this->assertTrue(true, 'If we arrive here everything was called in the correct order');
    }

    public function testCutVideoActionDuplicatedWrongAction()
    {
        $videoMock = M::mock('Kodify\AdminBundle\Entity\Video');
        $videoMock->shouldReceive('getStatus')->andReturn(Video::DUPLICATE_WARNING);
        $videoMock->shouldReceive('getBlockedBy')->andReturn('username');

        $sessionMock = M::mock()
            ->shouldReceive('setFlash')->times(1)->with('error', 'This video can not be cut')->getMock();
        $requestMock = M::mock()
            ->shouldReceive('get')->times(1)->with('action')->andReturn('Idontknowwhichactionisthis')->getMock();

        $controllerMock = M::mock($this->className . '[getDoctrine, getUser, generateUrl, render, get,getRequest]');
        $controllerMock->shouldReceive('get')->times(1)->andReturn($sessionMock);
        $controllerMock->shouldReceive('getRequest')->times(1)->andReturn($requestMock);


        $this->cutVideoAction($controllerMock, $videoMock);
        $this->assertTrue(true, 'If we arrive here everything was called in the correct order');
    }

    public function testCutVideoActionDuplicatedActionDiscard()
    {
        $videoMock = M::mock('Kodify\AdminBundle\Entity\Video');
        $videoMock->shouldReceive('getStatus')->andReturn(Video::DUPLICATE_WARNING);
        $videoMock->shouldReceive('getBlockedBy')->andReturn('username');

        $sessionMock = M::mock()
            ->shouldReceive('setFlash')->times(1)->with('info', 'Okay, video will be deleted.')->getMock();
        $requestMock = M::mock()
            ->shouldReceive('get')->times(1)->with('action')->andReturn(VideoController::DUPLICATED_DISCARD)->getMock();

        $controllerMock = M::mock($this->className . '[getDoctrine, getUser, generateUrl, render, get,getRequest]');
        $controllerMock->shouldReceive('get')->times(1)->andReturn($sessionMock);
        $controllerMock->shouldReceive('getRequest')->times(1)->andReturn($requestMock);


        $this->cutVideoAction($controllerMock, $videoMock);
        $this->assertTrue(true, 'If we arrive here everything was called in the correct order');
    }

    public function testCutVideoActionDuplicatedActionDownload()
    {
        $videoMock = M::mock('Kodify\AdminBundle\Entity\Video');
        $videoMock->shouldReceive('getStatus')->andReturn(Video::DUPLICATE_WARNING);
        $videoMock->shouldReceive('getBlockedBy')->andReturn('username');

        $sessionMock = M::mock()
            ->shouldReceive('setFlash')->times(1)->with(
                'info',
                'Success! I\'ll download this video, will be available for cutting shortly.'
            )->getMock();
        $requestMock = M::mock()
            ->shouldReceive('get')->times(1)->with('action')->andReturn(VideoController::DUPLICATED_DOWNLOAD)->getMock();

        $controllerMock = M::mock($this->className . '[getDoctrine, getUser, generateUrl, render, get,getRequest]');
        $controllerMock->shouldReceive('get')->times(1)->andReturn($sessionMock);
        $controllerMock->shouldReceive('getRequest')->times(1)->andReturn($requestMock);


        $this->cutVideoAction($controllerMock, $videoMock);
        $this->assertTrue(true, 'If we arrive here everything was called in the correct order');
    }

    public function testCutVideoActionAlreadyCut()
    {
        $videoMock = M::mock('Kodify\AdminBundle\Entity\Video');
        $videoMock->shouldReceive('getStatus')->andReturn(Video::CUT);
        $videoMock->shouldReceive('getBlockedBy')->andReturn('username');

        $sessionMock = M::mock()->shouldReceive('setFlash')->times(1)->with('error', 'This video can not be cut')->getMock();

        $controllerMock = M::mock($this->className . '[getDoctrine, getUser, generateUrl, render, get]');
        $controllerMock->shouldReceive('get')->andReturn($sessionMock);

        $this->cutVideoAction($controllerMock, $videoMock);
        $this->assertTrue(true, 'If we arrive here everything was called in the correct order');
    }

    public function testCutVideoActionWithNonExistingVideo()
    {
        $videoMock = M::mock();
        $sessionMock = M::mock();
        $controllerMock = M::mock($this->className . '[getDoctrine, getUser, generateUrl, render, get]');
        $controllerMock->shouldReceive('get')->andReturn($sessionMock);

        $response = $this->cutVideoAction($controllerMock, $videoMock);
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);
        $this->assertEquals($response->getTargetUrl(), 'get_video');
    }

    public function testCutVideoActionNotBlocked()
    {
        $videoMock = M::mock('Kodify\AdminBundle\Entity\Video');
        $videoMock->shouldReceive('getStatus')->andReturn(Video::BLOCKED);
        $videoMock->shouldReceive('getBlockedBy')->andReturn('admin');

        $managerMock = M::mock();
        $managerMock->shouldReceive('persist')->times(1);
        $managerMock->shouldReceive('flush')->times(1);

        $videoId = 10;
        $controllerMock = M::mock($this->className . '[getDoctrine, getUser, generateUrl, render]');
        $controllerMock->shouldReceive('render')->times(1)->with(
            'KodifyAdminBundle:Video:cut_form.html.twig',
            array(
                'title' => 'originalFileName',
                'video_url' => 's3path/bucketName/test?test',
                'video_id' => $videoId,
                'thumbnail_list' => array(),
                'autocomplete_pornstars' => 'pornstars_autocomplete',
                'autocomplete_tags' => 'tags_autocomplete',
                'post_url' => 'add_video_clips',
                'cancel_url' => 'cancel_url',
                'validate_tags_url' => 'validate_tags',
                'validate_pornstars_url' => 'validate_pornstars',
                'unsuitable_url' => 'unsuitable_url',
                's3_bucket_path' => 's3path/bucketName/',
                'userName' => 'admin',
                'duration' => 10
            )
        );

        $this->cutVideoAction($controllerMock, $videoMock, $videoId, $managerMock);
        $this->assertTrue(true, 'If we arrive here everything was called in the correct order');
    }

    public function testDefineTableHeader()
    {
        $controller = new VideoController();

        $result = $controller->defineTableHeader();

        $this->assertTrue(is_array($result));

        foreach ($result as $column) {
            $this->assertArrayHasKey('label', $column);
            $this->assertArrayHasKey('key', $column);
        }
    }

    public function testGetCustomRowClassRenderer()
    {
        $controller = new VideoController();
        $this->assertSame('KodifyAdminBundle:Video/Crud:row_class_renderer.html.twig',
            $this->callProtected(
                'Kodify\AdminBundle\Controller\VideoController',
                'getcustom_row_class_renderer',
                array(),
                $controller
            )
        );
    }

    public function testGetCustomActionButtonRenderer()
    {
        $controller = new VideoController();
        $this->assertSame('KodifyAdminBundle:Video/Crud:duplicated_videos_action_renderer.html.twig',
            $this->callProtected(
                'Kodify\AdminBundle\Controller\VideoController',
                'getcustom_action_button_renderer',
                array(),
                $controller
            )
        );
    }

    public function testGetDefaultSort()
    {
        $controller = new VideoController();
        $this->assertSame(
            array('id' => 'ASC'),
            $controller->getDefaultSort()
        );
    }

    private function cutVideoAction($controllerMock, $videoMock, $videoId = 10, $managerMock = null)
    {
        if ($managerMock == null) {
            $managerMock = M::mock();
        }

        $intervalTime = 30;

        $videoMock = $videoMock
            ->shouldReceive('getId')->andReturn($videoId)
            ->shouldReceive('getOriginalName')->andReturn('originalFileName?aa')
            ->shouldReceive('getFilename')->andReturn('test?test')
            ->shouldReceive('setStatus')
            ->shouldReceive('setBlockedBy')
            ->shouldReceive('getThumbnails')->with($intervalTime)->andReturn(array())
            ->shouldReceive('getDurationInMilliseconds')->andReturn(10000)
            ->getMock();

        $userMock = M::mock()->shouldReceive('getUserName')->andReturn('admin')->getMock();

        $repoMock = M::mock();
        $repoMock->shouldReceive('findOneById')->with($videoId)->andReturn($videoMock);
        $managerMock->shouldReceive('getRepository')->andReturn($repoMock);
        $managerMock->shouldReceive('persist');
        $managerMock->shouldReceive('flush');


        $doctrineMock = M::mock();
        $doctrineMock->shouldReceive('getManager')->andReturn($managerMock);

        $returnPathsMethod = function ($request_path, $params = array()) {
            return $request_path;
        };

        $controllerMock->shouldReceive('getDoctrine')->andReturn($doctrineMock);
        $controllerMock->shouldReceive('getUser')->andReturn($userMock);
        $controllerMock->shouldReceive('generateUrl')->andReturnUsing($returnPathsMethod);

        $containerMock = M::mock('Symfony\Component\DependencyInjection\Container[getParameter]')
            ->shouldReceive('getParameter')->with('s3_bucket_path')->andReturn('s3path')
            ->shouldReceive('getParameter')->with('s3_bucket_name')->andReturn('bucketName')
            ->shouldReceive('getParameter')->with('video_thumbnail_interval_seconds')->andReturn($intervalTime)
            ->getMock();

        $controllerMock->setContainer($containerMock);

        return $controllerMock->cutVideoAction($videoId);
    }

}