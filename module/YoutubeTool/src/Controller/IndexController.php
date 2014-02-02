<?php

namespace YoutubeTool\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Google_Client;
use Google_YouTubeService;
use Zend\Form\Form;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }

    public function listAction()
    {
        $sm = $this->getServiceLocator();
        $youtubeVideoTable = $sm->get('YoutubeTool\Model\YoutubeVideoTable');
        $tableGateway = $sm->get('YoutubeVideoTableGateway');

        $selectSQL = new \Zend\Db\Sql\Select();
        $selectSQL->from($tableGateway->getTable());
        $selectSQL->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT);
        $selectSQL->columns(array('playlist_title', 'sitename', 'playlist_id'));
        $resultSet = $tableGateway->selectWith($selectSQL);

        $selectOptions = array();
        foreach ($resultSet as $row) {
            if (array_key_exists($row->sitename, $selectOptions)) {
                $selectOptions[$row->sitename]['options'][$row->playlist_id] = $row->playlist_title;
            } else {
                $selectOptions[$row->sitename] = array('label' => $row->sitename, 'options' => array($row->playlist_id => $row->playlist_title));
            }
        }

        $channels = array();
        foreach ($resultSet as $row) {
            // use playlist_id for key
            $channels[] = array(
                $row->playlist_title);
        }

        $form = new Form();
        $form->add(array(
            'name' => 'playlist',
            'type' => 'Select',
            'options' => array(
                'empty_options' => 'Choose Playlist',
                'value_options' => $selectOptions,
            ),
        ));
        $form->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
            ),
        ));
        $form->get('submit')->setValue('Go');

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
