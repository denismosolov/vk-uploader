<?php

namespace YoutubeToolTest\Model;

use YoutubeTool\Model\YoutubeVideo;
use PHPUnit_Framework_TestCase;

class YoutubeVideoTest extends PHPUnit_Framework_TestCase
{
    public function testYoutubeVideoInitialState()
    {
        $video = new YoutubeVideo();

        $this->assertNull($video->id, '"id" should initially be null');
        $this->assertNull($video->description, '"description" should initially be null');
        $this->assertNull($video->playlist_title, '"playlist_title" should initially be null');
        $this->assertNull($video->video_title, '"video_title" should initially be null');
        $this->assertNull($video->sitename, '"sitename" should initially be null');
    }

    public function testExchangeArraySetsPropertiesCorrectly()
    {
        $video = new YoutubeVideo();
        $data  = array('description' => 'some description',
                       'id'     => 'Y_dasj2as',
                       'playlist_title' => 'some title',
                       'video_title' => 'some video title',
                       'sitename' => 'russianpod101');

        $video->exchangeArray($data);

        $this->assertSame($data['description'], $video->description, '"" was not set correctly');
        $this->assertSame($data['id'], $video->id, '"id" was not set correctly');
        $this->assertSame($data['playlist_title'], $video->playlist_title, '"playlist_title" was not set correctly');
        $this->assertSame($data['video_title'], $video->video_title, '"video_title" was not set correctly');
        $this->assertSame($data['sitename'], $video->sitename, '"sitename" was not set correctly');
    }

    public function testExchangeArraySetsPropertiesToDefaultIfKeysAreNotPresent()
    {
        $video = new YoutubeVideo();

        $video->exchangeArray(array('description' => 'some description',
                                    'id'     => 'Y_dasj2as',
                                    'playlist_title' => 'some title',
                                    'video_title' => 'some video title',
                                    'sitename' => 'russianpod101'));
        $video->exchangeArray(array());

        $this->assertNull($video->description, '"description" should have defaulted to null');
        $this->assertNull($video->id, '"id" should have defaulted to null');
        $this->assertNull($video->playlist_title, '"playlist_title" should have defaulted to null');
        $this->assertNull($video->video_title, '"video_title" should have defaulted to null');
        $this->assertNull($video->sitename, '"sitename" should have defaulted to null');
    }

}
