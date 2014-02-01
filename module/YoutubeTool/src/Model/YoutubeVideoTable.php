<?php

namespace YoutubeTool\Model;

use Zend\Db\TableGateway\TableGateway;

/**
 * Description of YoutubeVideoTable
 *
 * @author denis
 */
class YoutubeVideoTable implements \Countable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function count()
    {
        return $this->tableGateway->select()->count();
    }

    /**
     *
     * @param  string    $sitename101
     * @param  string    $playlist_title
     * @return ResultSet
     */
    public function fetchVideos($sitename101 = null, $playlist_title = null)
    {
        $where = array();
        if (!is_null($sitename101)) {
            $where['sitename'] = $sitename101;
        }
        if (!is_null($playlist_title)) {
            $where['playlist_title'] = $playlist_title;
        }

        return $this->tableGateway->select($where);
    }

    public function getVideo($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }

        return $row;
    }

    public function saveVideo(YoutubeVideo $video)
    {
        $data = array(
            'description' => $video->description,
            'video_title'  => $video->video_title,
            'video_id' => $video->video_id,
            'playlist_title' => $video->playlist_title,
            'sitename' => $video->sitename,
        );

        $id = (int) $video->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getVideo($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Video does not exist');
            }
        }
    }

    public function deleteVideo($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }

    public function importVideos(\Google_YouTubeService $service)
    {
        $module = new \YoutubeTool\Module();
        $config = $module->getConfig(); // @todo: use any service manager/locator instead

        foreach ($config['youtube']['channels'] as $sitename101 => $channelId) {
            $playlistListResponse = $service->playlists->listPlaylists('id,snippet', array(
                'channelId' => $channelId,
                'maxResults' => 15));
            foreach ($playlistListResponse->getItems() as $googlePlaylist) {
                $playlist = $googlePlaylist->getSnippet();
                $playlistTitle = $playlist->getTitle();

                $playlistItemListResponse = $service->playlistItems->listPlaylistItems('snippet',  array(
                    'playlistId' => $googlePlaylist->getId(),
                    'maxResults' => 50));
                $playlistItemListResponseItems = $playlistItemListResponse->getItems();
                foreach ($playlistItemListResponseItems as $playlistItem) {
                    $playlistItemSnippet = $playlistItem->getSnippet();
                    $resourceId = $playlistItemSnippet->getResourceId();
                    $youtubeVideo = new YoutubeVideo();
                    $youtubeVideo->exchangeArray(array(
                        'video_title' => $playlistItemSnippet->getTitle(),
                        'description' => $playlistItemSnippet->getDescription(),
                        'video_id' => $resourceId->getVideoId(),
                        'playlist_title' => $playlistTitle,
                        'sitename' => $sitename101,
                    ));
                    $this->saveVideo($youtubeVideo);
                }
            }
        }
    }

    public function dropImportedVideos()
    {
        $this->tableGateway->delete(array());
    }

}
