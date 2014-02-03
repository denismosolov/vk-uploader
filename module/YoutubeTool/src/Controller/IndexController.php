<?php

namespace YoutubeTool\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Google_Client;
use Google_YouTubeService;
use Zend\Form\Form;

class IndexController extends AbstractActionController
{

    private $selectPlaylistForm;

    public function indexAction()
    {
        return new ViewModel();
    }

    private function getSelectPlaylistForm()
    {
        if ($this->selectPlaylistForm instanceof Form) {
            return $this->selectPlaylistForm;
        } else {
            // @todo: move to Form class, but not sure how to inject dependency
            $sm = $this->getServiceLocator();
            $tableGateway = $sm->get('YoutubeVideoTableGateway');

            // building select for form
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

            $form = new Form();
            $form->setAttribute('method', 'get');
            $form->setAttribute('action', $this->url()->fromRoute('youtubetool/playlist'));
            $form->add(array(
                'name' => 'playlist_id',
                'type' => 'Select',
                'options' => array(
                    'empty_option' => 'Please Select Playlist',
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

            return $form;
        }
    }

    public function listAction()
    {
        return new ViewModel(array('form' => $this->getSelectPlaylistForm()));
    }

    public function playlistAction()
    {
        // matching route
        $playlist_id_r = $this->params()->fromRoute('playlist_id');
        if (!empty($playlist_id_r)) {
            $sm = $this->getServiceLocator();
            $youtubeVideoTable = $sm->get('YoutubeTool\Model\YoutubeVideoTable');
            $videos = $youtubeVideoTable->fetchVideos(array('playlist_id' => $playlist_id_r));
            // @todo: append pagination
            // at this point we cannot detect does category empty or wrong url
            // @todo: separate behaviour for above cases
            if ($videos->count() > 0) {
                $sm = $this->getServiceLocator();
                $youtubeVideoTable = $sm->get('YoutubeTool\Model\YoutubeVideoTable');
                // just for page title building
                $first_video = $videos->current();

                return new ViewModel(array(
                    'videos' => $videos,
                    'form' => $this->getSelectPlaylistForm(),
                    'playlist_title' => $first_video->playlist_title,
                    'sitename101' => $first_video->sitename
                ));
            }
        }

        // select route submitted - redirect to match route
        $playlist_id_q = $this->params()->fromQuery('playlist_id');
        if (!empty($playlist_id_q)) {
            return $this->redirect()->toRoute('youtubetool/playlist', array('playlist_id' => $playlist_id_q));
        }

        // when user clicks "Go" button without selecting a playlist
        // it redirects him back to /list
        // @todo: 404
        return $this->redirect()->toRoute('youtubetool/list');
    }

    public function editAction()
    {
        $youtube_id_r = $this->params()->fromRoute('youtube_id');
        if (!empty($youtube_id_r)) {
            $sm = $this->getServiceLocator();
            $youtubeVideoTable = $sm->get('YoutubeTool\Model\YoutubeVideoTable');
            try {
                $video = $youtubeVideoTable->getVideo($youtube_id_r);
                // @todo: submit form
                return new ViewModel(array(
                    'video' => $video
                ));
            } catch (\Exception $ex) {
                // exeptions seems to be bad idea
                // @todo: remove the method throw exception
                return $this->redirect()->toRoute('youtubetool/list'); // @todo: 404
            }
        } else {
            return $this->redirect()->toRoute('youtubetool/list');
        }
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
