<?php

namespace Kodify\AdminBundle\Tests\Repository;

use \Mockery as M;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Console\Application as App;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Kodify\AdminBundle\Repository\PutIoFileRepository;
use Kodify\AdminBundle\Entity\PutIoFile;

class PutIoFileRepositoryTest extends WebTestCase
{
    public function tearDown()
    {
        M::close();
    }

    public function testIsNewFileTrue()
    {
        $mockRepo = M::mock('Kodify\AdminBundle\Repository\PutIoFileRepository[findOneBy]');
        $mockRepo
            ->shouldReceive('findOneBy')
            ->once()
            ->with(array('putioId' => 10))
            ->andReturn(null);

        $this->assertTrue($mockRepo->isNewFile(10));
    }

    public function testIsNewFileFalse()
    {
        $mockRepo = M::mock('Kodify\AdminBundle\Repository\PutIoFileRepository[findOneBy]');
        $mockRepo
            ->shouldReceive('findOneBy')
            ->once()
            ->with(array('putioId' => 10))
            ->andReturn(new PutIoFile());

        $this->assertFalse($mockRepo->isNewFile(10));
    }

    public function testFileChangesToDownloaded()
    {
        $mockRepo = M::mock('Kodify\AdminBundle\Repository\PutIoFileRepository[findOneBy]');
        $mockRepo
            ->shouldReceive('findOneBy')
            ->once()
            ->with(array('putioId' => 10))
            ->andReturn(new PutIoFile());

        $this->assertFalse($mockRepo->isNewFile(10));
    }

    public function  testGetOneByStatusCall()
    {
        $testStatus = 'supu';
        $mockRepo = M::mock('Kodify\AdminBundle\Repository\PutIoFileRepository[findOneBy]');
        $mockRepo
            ->shouldReceive('findOneBy')
            ->once()
            ->with(array('status' => $testStatus));
        $mockRepo->getOneBystatus($testStatus);
        $this->assertTrue(true, 'If we arrive here everything was called in the correct order');
    }

    public function testGetPutioNotDeletedFilesCount()
    {
        $expected = 10;

        $queryResultMock = \Mockery::mock()
            ->shouldReceive('getSingleScalarResult')->times(1)->andReturn($expected)
            ->getMock();

        $queryMock = \Mockery::mock()
            ->shouldReceive('select')->times(1)->andReturn(\Mockery::self())
            ->shouldReceive('where')->times(1)->andReturn(\Mockery::self())
            ->shouldReceive('orWhere')->times(1)->andReturn(\Mockery::self())
            ->shouldReceive('leftJoin')->times(1)->andReturn(\Mockery::self())
            ->shouldReceive('getQuery')->times(1)->andReturn($queryResultMock)
            ->getMock();

        $videoRepo = \Mockery::mock('Kodify\AdminBundle\Repository\PutIoFileRepository[createQueryBuilder]')
            ->shouldReceive('createQueryBuilder')->times(1)->andReturn($queryMock)
            ->getMock();

        $response = $videoRepo->getPutioNotDeletedFilesCount();
        $this->assertEquals($response, $expected);
    }

    public function testFetchDuplicatedFile()
    {
        $testName = 'supu';
        $mockRepo = M::mock('Kodify\AdminBundle\Repository\PutIoFileRepository[findOneBy]');
        $mockRepo
            ->shouldReceive('findOneBy')
            ->once()
            ->with(array('name' => $testName));
        $mockRepo->fetchDuplicatedFile($testName);
        $this->assertTrue(true, 'If we arrive here everything was called in the correct order');
    }

    public function testGetNthByStatusNotFound()
    {
        $testStatus = 'supu';
        $position = 1;
        $mockRepo = M::mock('Kodify\AdminBundle\Repository\PutIoFileRepository[findBy]');
        $mockRepo
            ->shouldReceive('findBy')
            ->once()
            ->with(array('status' => $testStatus), null, $position);
        $this->assertSame(null, $mockRepo->getNthByStatus($testStatus, $position));
    }

    public function testGetNthByStatusFound()
    {
        $mockPutIoFile = new PutIoFile();
        $mockPutIoFile->setPutioId(1);
        $mockPutIoFile35 = new PutIoFile();
        $mockPutIoFile35->setPutioId(35);

        $testStatus = 'supu';
        $mockRepo = M::mock('Kodify\AdminBundle\Repository\PutIoFileRepository[findBy]');
        $mockRepo
            ->shouldReceive('findBy')
            ->once()
            ->with(array('status' => $testStatus), null, 1)
            ->andReturn(array($mockPutIoFile));
        $mockRepo
            ->shouldReceive('findBy')
            ->once()
            ->with(array('status' => $testStatus), null, 2)
            ->andReturn(array($mockPutIoFile,$mockPutIoFile35));
        $this->assertSame($mockPutIoFile, $mockRepo->getNthByStatus($testStatus, 1));

        $this->assertSame($mockPutIoFile35, $mockRepo->getNthByStatus($testStatus, 2));
    }

    public function testGetDuplicatedVideoInfoNotFound()
    {
        $mockRepo = M::mock('Kodify\AdminBundle\Repository\PutIoFileRepository[findOneBy]');
        $mockRepo
            ->shouldReceive('findOneBy')
            ->once()
            ->with(array('video'=>1))
            ->andReturn('supu');
        $this->assertFalse($mockRepo->getDuplicatedVideoInfo(1));
    }

    public function testGetDuplicatedVideoInfoFound()
    {
        $videoMock = M::mock();
        $videoMock->shouldReceive('getDurationInMilliseconds')->andReturn('duration');

        $originalMock = M::mock();
        $originalMock->shouldReceive('getName')->andReturn('name');
        $originalMock->shouldReceive('getVideo')->andReturn($videoMock);
        $originalMock->shouldReceive('getSize')->andReturn('sizeOriginal');



        $putIoFileMock = M::mock('Kodify\AdminBundle\Entity\PutIoFile');
        $putIoFileMock->shouldReceive('getDuplicated')->andReturn($originalMock);
        $putIoFileMock->shouldReceive('getSize')->andReturn('sizeDuplicated');

        $mockRepo = M::mock('Kodify\AdminBundle\Repository\PutIoFileRepository[findOneBy]');
        $mockRepo
            ->shouldReceive('findOneBy')
            ->once()
            ->with(array('video'=>1))
            ->andReturn($putIoFileMock);
        $this->assertSame(
            array('name' => 'name', 'duration' => 'duration', 'oldSize' => 'sizeOriginal','newSize' => 'sizeDuplicated'),
            $mockRepo->getDuplicatedVideoInfo(1)
        );
    }


}