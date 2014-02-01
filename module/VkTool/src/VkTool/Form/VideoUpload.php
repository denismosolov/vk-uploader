<?php

namespace VkTool\Form;

use Zend\Form\Form;

/**
 * Description of AccessToken
 *
 * @author denis
 */
class VideoUpload extends Form {

    protected $captcha;
    
    public function __construct() {
        parent::__construct();
        
        $this->setAttribute('method', 'post');
        
        $this->add(array(
            'name' => 'access_token',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'type' => 'text',
                'required' => true,
                'placeholder' => 'access_token...',
            ),
            'options' => array(
                'label' => 'Your access token',
            ),
        ));
        
        $this->add(array(
            'name' => 'youtube_url',
            'type' => 'Zend\Form\Element\Text',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'http://www.youtube.com/watch?v=eBMmwbLV1eI',
            ),
            'options' => array(
                'label' => 'Youtube link',
            ),
        ));
        
        $this->add(array(
            'name' => 'message',
            'type' => 'Zend\Form\Element\Textarea',
            'options' => array(
                'label' => 'Video description',
            )
        ));
        
        $this->add(array(
            'name' => 'csrf',
            'type' => 'Zend\Form\Element\Csrf',
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'value' => 'Submit',
            ),
        ));
    }
}
