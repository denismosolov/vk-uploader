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
        // @todo: append pagination

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

        $playlist_id = $this->params()->fromQuery('playlist_id');
        if ($playlist_id) {
            $this->redirect()->toRoute('youtubetool/list', array('playlist_id' => $playlist_id));
        }

        $form = new Form();
        $form->setAttribute('method', 'get');
        $form->setAttribute('action', $this->url()->fromRoute('youtubetool/list'));
        $form->add(array(
            'name' => 'playlist_id',
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

        $playlist_id = $this->params()->fromRoute('playlist_id');
        if ($playlist_id) {
            $videos = $youtubeVideoTable->fetchVideos(array('playlist_id' => $playlist_id));
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
