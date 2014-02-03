<?php

namespace YoutubeTool\Model;

/**
 * Description of YoutubeVideo
 *
 * @author denis
 */
class YoutubeVideo
{
    public $id;
    public $description;
    public $playlist_title;
    public $playlist_id;
    public $video_title;
    public $sitename;

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->playlist_id = (isset($data['playlist_id'])) ? $data['playlist_id'] : null;
        $this->playlist_title  = (isset($data['playlist_title'])) ? $data['playlist_title'] : null;
        $this->video_title  = (isset($data['video_title'])) ? $data['video_title'] : null;
        $this->sitename  = (isset($data['sitename'])) ? $data['sitename'] : null;
    }

}
