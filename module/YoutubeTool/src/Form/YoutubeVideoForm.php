<?php

namespace YoutubeTool\Form;

use Zend\Form\Form;

class YoutubeVideoForm extends Form
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        // @todo: data for drop down must come from config and database

        $this->add(array(
            'name' => 'channel',
            'type' => 'Select',
            'options' => array(
                'empty_options' => 'Choose YouTube Channel',
                'value_options' => array(
                    'russianpod101' => 'RussianPod101',
                    'arabicpod101' => 'ArabicPod101',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'playlist',
            'type' => 'Select',
            'options' => array(
                'empty_options' => 'Choose Playlist',
                'value_options' => array(
                ),
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
            ),
        ));
    }
}
