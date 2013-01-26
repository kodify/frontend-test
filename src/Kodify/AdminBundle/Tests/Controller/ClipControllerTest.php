<?php

namespace Kodify\AdminBundle\Tests\Controller;

use \Mockery as M;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\DependencyInjection\Container;

use Kodify\AdminBundle\Controller\ClipController;
use Kodify\AdminBundle\Entity\Video;
use Kodify\TestsBundle\Tests\ControllerBaseClass;

/**
 * @group clip
 */
class ClipControllerTest extends ControllerBaseClass
{
    public function testDefineTableHeader()
    {
        $cnt = new ClipController();
        $result = $cnt->defineTableHeader();

        $this->assertTrue(is_array($result));

        foreach ($result as $column) {
            $this->assertArrayHasKey('label', $column);
            $this->assertArrayHasKey('key', $column);
        }
    }

    public function testAddVideoClipsActionWithoutErrors()
    {
        $videoMock = M::mock()
            ->shouldReceive('setStatus')->with(Video::CUT)->times(1)->andReturn('test')
            ->getMock();

        $tagRepoMock = M::mock('')
            ->shouldReceive('validateTagList')->andReturn(array())
            ->getMock();

        $pornstarRepoMock = M::mock('')
            ->shouldReceive('validatePornstarList')->andReturn(array())
            ->getMock();

        $managerMock = M::mock('')
            ->shouldReceive('persist')->times(3)->andReturn(array($videoMock))
            ->shouldReceive('flush')->times(1)
            ->getMock();

        $sessionMock = M::mock('')
            ->shouldReceive('setFlash')
            ->times(0)
            ->getMock();

        $this->addVideoClipsAction($videoMock, $managerMock, $tagRepoMock, $pornstarRepoMock, $sessionMock);
    }

    public function testAddVideoClipsActionWithInvalidTags()
    {
        $videoMock = M::mock();

        $tagRepoMock = M::mock('')
            ->shouldReceive('validateTagList')->andReturn(array('t2'))
            ->times(1)
            ->getMock();

        $pornstarRepoMock = M::mock('')
            ->shouldReceive('validatePornstarList')->andReturn(array())
            ->times(0)
            ->getMock();

        $managerMock = M::mock('');


        $sessionMock = M::mock('')
            ->shouldReceive('setFlash')
            ->times(1)
            ->getMock();

        $this->addVideoClipsAction($videoMock, $managerMock, $tagRepoMock, $pornstarRepoMock, $sessionMock);
    }

    public function testAddVideoClipsActionWithInvalidPornstar()
    {
        $videoMock = M::mock();

        $tagRepoMock = M::mock('')
            ->shouldReceive('validateTagList')->andReturn(array())
            ->times(1)
            ->with(array('tag 1'))
            ->getMock();

        $pornstarRepoMock = M::mock('')
            ->shouldReceive('validatePornstarList')->andReturn(array('t2'))
            ->times(1)
            ->with(array('pornstar 1', 'pornstar 2'))
            ->getMock();

        $managerMock = M::mock('');

        $sessionMock = M::mock('')
            ->shouldReceive('setFlash')
            ->times(1)
            ->getMock();

        $this->addVideoClipsAction($videoMock, $managerMock, $tagRepoMock, $pornstarRepoMock, $sessionMock);
    }

    public function testAddVideoClipsActionWithNextVideoValue()
    {
        $videoMock = M::mock()
            ->shouldReceive('setStatus')->with(Video::CUT)->times(1)->andReturn('test')
            ->getMock();

        $tagRepoMock = M::mock('')
            ->shouldReceive('validateTagList')->andReturn(array())
            ->getMock();

        $pornstarRepoMock = M::mock('')
            ->shouldReceive('validatePornstarList')->andReturn(array())
            ->getMock();

        $managerMock = M::mock('')
            ->shouldReceive('persist')->times(3)->andReturn(array($videoMock))
            ->shouldReceive('flush')->times(1)
            ->getMock();

        $sessionMock = M::mock('')
            ->shouldReceive('setFlash')
            ->times(0)
            ->getMock();

        $getNextReadyVideoResponse = M::mock('Kodify\AdminBundle\Entity\Video')
            ->shouldReceive('getId')->times(1)->andReturn(555)
            ->getMock();

        $response = $this->addVideoClipsAction($videoMock, $managerMock, $tagRepoMock, $pornstarRepoMock, $sessionMock, $getNextReadyVideoResponse);
    }

    private function addVideoClipsAction($videoMock, $managerMock, $tagRepoMock, $pornstarRepoMock, $sessionMock, $getNextReadyVideoResponse = null)
    {
        $clips = array(
            'title' => array('title 1', 'title 2'),
            'start' => array('00:00:00', '00:00:10'),
            'end' => array('00:00:10', '00:00:20'),
            'pornstars' => array('pornstar 1,pornstar 2', 'pornstar 2'),
            'tags' => array('tag 1', 'tag 1,tag 2'),
        );
        $videoId = 10;

        $requestMock = M::mock('Symfony\Component\HttpFoundation\Request')
            ->shouldReceive('get')->with('clip')->andReturn($clips)
            ->shouldReceive('get')->with('video_id')->andReturn($videoId)
            ->shouldReceive('isMethod')->andReturn(true)
            ->getMock();

        $userMock = M::mock('')
            ->shouldReceive('getUsername')->andReturn('username')
            ->getMock();

        $videoRepoMock = M::mock('')
            ->shouldReceive('findOneById')->with($videoId)->andReturn($videoMock)
            ->shouldReceive('getNextReadyVideo')->andReturn($getNextReadyVideoResponse)
            ->getMock();

        $doctrineMock = M::mock('')
            ->shouldReceive('getRepository')->with('KodifyAdminBundle:Video')->andReturn($videoRepoMock)
            ->shouldReceive('getRepository')->with('KodifyAdminBundle:Tag')->andReturn($tagRepoMock)
            ->shouldReceive('getRepository')->with('KodifyAdminBundle:Pornstar')->andReturn($pornstarRepoMock)
            ->shouldReceive('getManager')->andReturn($managerMock)

            ->getMock();

        $controllerMock = M::mock('Kodify\AdminBundle\Controller\ClipController[get,getDoctrine,getUser,generateUrl]')
            ->shouldReceive('getDoctrine')->andReturn($doctrineMock)
            ->shouldReceive('getUser')->andReturn($userMock)
            ->shouldReceive('get')->andReturn($sessionMock)
            ->shouldReceive('generateUrl')->andReturn('redirect_url')
            ->getMock();

        $response = $controllerMock->addVideoClipsAction($requestMock);
        $this->assertEquals($response->getTargetUrl(), 'redirect_url');

        return $response;
    }
}
