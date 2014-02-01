<?php

namespace YoutubeToolTest\Model;

use YoutubeTool\Model\YoutubeVideo;
use YoutubeTool\Model\YoutubeVideoTable;
use Zend\Db\ResultSet\ResultSet;
use PHPUnit_Framework_TestCase;

class YoutubeVideoTableTest extends PHPUnit_Framework_TestCase
{
    public function testFetchVideos()
    {
        $resultSet        = new ResultSet();
        $mockTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway',
                                           array('select'), array(), '', false);
        $mockTableGateway->expects($this->once())
                         ->method('select')
                         ->with()
                         ->will($this->returnValue($resultSet));

        $youtubeVideoTable = new YoutubeVideoTable($mockTableGateway);

        $this->assertSame($resultSet, $youtubeVideoTable->fetchVideos());
    }

    public function testCanRetrieveAnVideoByItsId()
    {
        $video = new YoutubeVideo();
        $video->exchangeArray(array('description' => 'some description',
                                    'id'     => 123,
                                    'video_id'  => 'Y_dasj2as',
                                    'playlist_title' => 'some title',
                                    'video_title' => 'some video title',
                                    'sitename' => 'russianpod101'));

        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new YoutubeVideo());
        $resultSet->initialize(array($video));

        $mockTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway', array('select'), array(), '', false);
        $mockTableGateway->expects($this->once())
                         ->method('select')
                         ->with(array('id' => 123))
                         ->will($this->returnValue($resultSet));

        $youtubeVideoTable = new YoutubeVideoTable($mockTableGateway);

        $this->assertSame($video, $youtubeVideoTable->getVideo(123));
    }

    public function testCanDeleteAnVideoByItsId()
    {
        $mockTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway', array('delete'), array(), '', false);
        $mockTableGateway->expects($this->once())
                         ->method('delete')
                         ->with(array('id' => 123));

        $youtubeVideoTable = new YoutubeVideoTable($mockTableGateway);
        $youtubeVideoTable->deleteVideo(123);
    }

    public function testSaveVideoWillInsertNewVideosIfTheyDontAlreadyHaveAnId()
    {
        $data = array('description' => 'some description',
                                    'video_id'  => 'Y_dasj2as',
                                    'playlist_title' => 'some title',
                                    'video_title' => 'some video title',
                                    'sitename' => 'russianpod101');
        $video     = new YoutubeVideo();
        $video->exchangeArray($data);

        $mockTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway', array('insert'), array(), '', false);
        $mockTableGateway->expects($this->once())
                         ->method('insert')
                         ->with($data);

        $youtubeVideoTable = new YoutubeVideoTable($mockTableGateway);
        $youtubeVideoTable->saveVideo($video);
    }

    public function testSaveVideoWillUpdateExistingVideosIfTheyAlreadyHaveAnId()
    {
        $data = array('description' => 'some description',
                                    'id' => 123,
                                    'video_id'  => 'Y_dasj2as',
                                    'playlist_title' => 'some title',
                                    'video_title' => 'some video title',
                                    'sitename' => 'russianpod101');
        $video     = new YoutubeVideo();
        $video->exchangeArray($data);

        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new YoutubeVideo());
        $resultSet->initialize(array($video));

        $mockTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway',
                                           array('select', 'update'), array(), '', false);
        $mockTableGateway->expects($this->once())
                         ->method('select')
                         ->with(array('id' => 123))
                         ->will($this->returnValue($resultSet));
        $mockTableGateway->expects($this->once())
                         ->method('update')
                         ->with(array('description' => 'some description', 'video_id'  => 'Y_dasj2as',
                                    'playlist_title' => 'some title',
                                    'video_title' => 'some video title',
                                    'sitename' => 'russianpod101'),
                                array('id' => 123));

        $youtubeVideoTable = new YoutubeVideoTable($mockTableGateway);
        $youtubeVideoTable->saveVideo($video);
    }

    public function testExceptionIsThrownWhenGettingNonexistentVideo()
    {
        $resultSet = new ResultSet();
        $resultSet->setArrayObjectPrototype(new \YoutubeTool\Model\YoutubeVideo());
        $resultSet->initialize(array());

        $mockTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway', array('select'), array(), '', false);
        $mockTableGateway->expects($this->once())
                         ->method('select')
                         ->with(array('id' => 123))
                         ->will($this->returnValue($resultSet));

        $youtubeVideoTable = new YoutubeVideoTable($mockTableGateway);

        try {
            $youtubeVideoTable->getVideo(123);
        } catch (\Exception $e) {
            $this->assertSame('Could not find row 123', $e->getMessage());

            return;
        }

        $this->fail('Expected exception was not thrown');
    }

}
