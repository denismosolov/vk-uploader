<?php

namespace VkTool\Model;

use Zend\Http\Request;
use Zend\Http\Client;

/**
 * Description of Vk
 *
 * @author denis
 */
class Vk
{
    public function __construct()
    {
        ;
    }

    // title, description
    public function upload($sitename101, $access_token, $video_title, $description, $youtube_id)
    {
        $module = \VkTool\Module();
        $config = $module->getConfig();

        $uploadVideoRequest = new Request();
        $uploadVideoRequest->setMethod(Request::METHOD_GET);
        $uploadVideoRequest->setUri('https://api.vk.com/method/video.save');
        $uploadVideoRequest->getQuery()->set('name', $video_title);
        $uploadVideoRequest->getQuery()->set('description', $description);
        $uploadVideoRequest->getQuery()->set('wallpost', '0');
        $uploadVideoRequest->getQuery()->set('link', 'http://www.youtube.com/watch?v=' . $youtube_id);
        $uploadVideoRequest->getQuery()->set('group_id', $config['vk']['publics'][$sitename101]);
        $uploadVideoRequest->getQuery()->set('access_token', $access_token);

        $client = new Client('https://api.vkontakte.ru', $config['client']);

        $uploadVideoRequestResponce = $client->dispatch($uploadVideoRequest);
        $video = \Zend\Json\Json::decode($uploadVideoRequestResponce->getContent(), \Zend\Json\Json::TYPE_ARRAY);
        $client->reset();

        $pingVideoRequest = new Request();
        $pingVideoRequest->setMethod(Request::METHOD_GET);
        $pingVideoRequest->setUri($video['response']['upload_url']);
        $client->reset();

        $addVideoRequest = new Request();
        $addVideoRequest->setMethod(Request::METHOD_GET);
        $addVideoRequest->setUri('https://api.vk.com/method/video.add');
        $addVideoRequest->getQuery()->set('video_id', $video['response']['vid']);
        $addVideoRequest->getQuery()->set('owner_id', '-'.$config['owner_id']);
        $addVideoRequest->getQuery()->set('access_token', $this->getRequest()->getPost('access_token'));
        $client->dispatch($addVideoRequest);
    }

}
