<?php

namespace Kodify\AdminBundle\Tests\Repository;

use Kodify\AdminBundle\Repository\VideoRepository;
use Kodify\AdminBundle\Entity\Video;
use Kodify\TestsBundle\Tests\RepositoryBaseClass;

class VideoRepositoryTest extends RepositoryBaseClass
{
    public function testGetQueryDefaultStatusFilter()
    {
        $videoRepo = new VideoRepository($this->em, $this->metaData);
        $result = $videoRepo->getQuery(array('status' => ''));

        $this->assertTrue($result instanceof \Doctrine\ORM\QueryBuilder);

        $where = $result->getDQLPart('where')->getParts();
        $this->assertEquals(count($where), 1);
        $this->assertEquals($where[0], 'p.status in (:value_status)');

        $params = $result->getParameters();
        $this->assertEquals(count($params), 1);
        $this->assertEquals($params[0]->getName(), 'value_status');
        $this->assertEquals($params[0]->getValue(), array(Video::BLOCKED, Video::READY, Video::DUPLICATE_WARNING));
    }

    public function testGetNextReadyVideo()
    {
        $expected = 'test';
        $videoRepo = \Mockery::mock('Kodify\AdminBundle\Repository\VideoRepository[findOneBy]')
            ->shouldReceive('findOneBy')->andReturn($expected)
            ->getMock();

        $response = $videoRepo->getNextReadyVideo();
        $this->assertEquals($response, $expected);
    }

    public function testGetAllByStatus()
    {
        $expected = 'test';
        $input = 'input';

        $videoRepo = \Mockery::mock('Kodify\AdminBundle\Repository\VideoRepository[findBy]')
            ->shouldReceive('findBy')->with(array('status' => $input))->andReturn($expected)
            ->getMock();

        $response = $videoRepo->getAllByStatus($input);
        $this->assertEquals($response, $expected);
    }

    public function testGetThumbnailDeletePending()
    {
        $expected = 'result';

        $queryResultMock = \Mockery::mock()
            ->shouldReceive('getResult')->times(1)->andReturn($expected)
            ->getMock();

        $allowedStatuses = array(Video::CUT, Video::CANCELLED);

        $queryMock = \Mockery::mock()
            ->shouldReceive('select')->times(1)->andReturn(\Mockery::self())
            ->shouldReceive('where')->times(1)->andReturn(\Mockery::self())
            ->shouldReceive('andWhere')->times(1)->andReturn(\Mockery::self())
            ->shouldReceive('setParameter')->times(1)->with('param', $allowedStatuses)->andReturn(\Mockery::self())
            ->shouldReceive('getQuery')->times(1)->andReturn($queryResultMock)
            ->getMock();

        $videoRepo = \Mockery::mock('Kodify\AdminBundle\Repository\VideoRepository[createQueryBuilder]')
            ->shouldReceive('createQueryBuilder')->times(1)->andReturn($queryMock)
            ->getMock();

        $response = $videoRepo->getThumbnailDeletePending();
        $this->assertEquals($response, $expected);
    }

    public function testGetReadyToCutVideosCount()
    {
        $expected = 10;

        $queryResultMock = \Mockery::mock()
            ->shouldReceive('getSingleScalarResult')->times(1)->andReturn($expected)
            ->getMock();

        $queryMock = \Mockery::mock()
            ->shouldReceive('select')->times(1)->andReturn(\Mockery::self())
            ->shouldReceive('where')->times(1)->andReturn(\Mockery::self())
            ->shouldReceive('setParameter')->times(1)->with('status', Video::READY)->andReturn(\Mockery::self())
            ->shouldReceive('getQuery')->times(1)->andReturn($queryResultMock)
            ->getMock();

        $videoRepo = \Mockery::mock('Kodify\AdminBundle\Repository\VideoRepository[createQueryBuilder]')
            ->shouldReceive('createQueryBuilder')->times(1)->andReturn($queryMock)
            ->getMock();

        $response = $videoRepo->getReadyToCutVideosCount();
        $this->assertEquals($response, $expected);
    }
}