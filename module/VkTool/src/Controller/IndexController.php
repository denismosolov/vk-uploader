<?php

namespace VkTool\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use VkTool\Form\VideoUpload;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        $config = $this->getServiceLocator()->get('config');

                $form = new VideoUpload();
                if ($this->getRequest()->isPost()) {
                    $form->setData($this->getRequest()->getPost()->toArray());
                    if ($form->isValid()) {
                        // post
                        $data = $form->getData();

                        $uploadVideoRequest = new Request();
                        $uploadVideoRequest->setMethod(Request::METHOD_GET);
                        $uploadVideoRequest->setUri('https://api.vk.com/method/video.save');
                        $uploadVideoRequest->getQuery()->set('name', 'test');
                        $uploadVideoRequest->getQuery()->set('description', $data['message']);
                        $uploadVideoRequest->getQuery()->set('wallpost', '0');
                        $uploadVideoRequest->getQuery()->set('link', $data['youtube_url']);
                        $uploadVideoRequest->getQuery()->set('group_id', $config['owner_id']);
                        $uploadVideoRequest->getQuery()->set('access_token', $this->getRequest()->getPost('access_token'));

                        $client = new Client('https://api.vkontakte.ru', $config['client']);

                        $uploadVideoRequestResponce = $client->dispatch($uploadVideoRequest);
                        $video = \Zend\Json\Json::decode($uploadVideoRequestResponce->getContent(), \Zend\Json\Json::TYPE_ARRAY);
                        $client->reset();

                        $pingVideoRequest = new Request();
                        $pingVideoRequest->setMethod(Request::METHOD_GET);
                        $pingVideoRequest->setUri($video['response']['upload_url']);
                        var_dump($client->send($pingVideoRequest));
                        $client->reset();

                        $addVideoRequest = new Request();
                        $addVideoRequest->setMethod(Request::METHOD_GET);
                        $addVideoRequest->setUri('https://api.vk.com/method/video.add');
                        $addVideoRequest->getQuery()->set('video_id', $video['response']['vid']);
                        $addVideoRequest->getQuery()->set('owner_id', '-'.$config['owner_id']);
                        $addVideoRequest->getQuery()->set('access_token', $this->getRequest()->getPost('access_token'));
                        $a = $client->dispatch($addVideoRequest);
                        var_dump($a->getContent());
                    }
                }

                return new ViewModel(array(
                    'url' => 'https://oauth.vk.com/authorize?client_id='.$config['app_id'].
                                '&scope=groups,video,wall,oauth&redirect_uri='.
                                urldecode('https://oauth.vk.com/blank.html').
                                '&display=popup&response_type=token',
                    'form' => $form,
                    'access_token' => $this->getRequest()->getPost('access_token', ''),
                ));
    }

}
