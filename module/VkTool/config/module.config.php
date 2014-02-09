<?php
return array(
    'client' => array(
        'adapter' => 'Zend\Http\Client\Adapter\Curl',
        'curloptions' => array(
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'VkTool\Controller\Index' => 'VkTool\Controller\IndexController'
        ),
    ),
    'router' => array(
        'routes' => array(
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'vktool' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/vktool',
                    'defaults' => array(
                        '__NAMESPACE__' => 'VkTool\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'database' => 'VkTool\Service\Factory\Database',
        ),
    ),
    'vk' => array(
        'publics' => array(
            'russianpod101' => '65210550', // group_id
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'vktool_entities' => array(
                'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/Entity/Vk'),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'VkTool\Entity\Vk' => 'vktool_entities',
                ),
            ),
        ),
    ),
);
