<?php

namespace YoutubeTool\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Google_Client;
use Google_YouTubeService;
use YoutubeTool\Form\YoutubeVideoForm;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function listAction()
    {
        $form = new YoutubeVideoForm();
        $form->get('submit')->setValue('Go');

        $sm = $this->getServiceLocator();
        $youtubeVideoTable = $sm->get('YoutubeTool\Model\YoutubeVideoTable');

        $request = $this->getRequest();
        if ($request->isPost()) {
            // @todo: does form valid
            $videos = $youtubeVideoTable->fetchVideos($request->getPost('sitename', null), $request->getPost('playlist_title', null));
        } else {
            $videos = $youtubeVideoTable->fetchVideos();
        }

        return new ViewModel(array('form' => $form, 'videos' => $videos));
    }

    public function importAction()
    {
        $sm = $this->getServiceLocator();
        $youtubeVideoTable = $sm->get('YoutubeTool\Model\YoutubeVideoTable');
        $youtubeVideoTable->dropImportedVideos();
        $youtubeVideoTable->importVideos(new Google_YouTubeService(new Google_Client()));

        return new ViewModel(array('count' => $youtubeVideoTable->count()));
    }

}
