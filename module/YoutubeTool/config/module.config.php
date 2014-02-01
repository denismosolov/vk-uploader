<?php
return array(
    'view_manager' => array(
        'template_map' => array(
            'youtube-tool/index/index' => __DIR__ . '/../view/index/index.phtml',
            'youtube-tool/index/import' => __DIR__ . '/../view/index/import.phtml',
            'youtube-tool/index/list' => __DIR__ . '/../view/index/list.phtml',
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'YoutubeTool\Controller\Index' => 'YoutubeTool\Controller\IndexController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'youtubetool' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/youtube',
                    'defaults' => array(
                        '__NAMESPACE__' => 'YoutubeTool\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'ud' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/index/:action/:youtube_id',
                            'constraints' => array(
                                'action'     => 'edit|delete',
                                'youtube_id' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                        ),
                    ),
                    'list' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/list',
                            'defaults' => array(
                                'controller' => 'Index',
                                'action' => 'list',
                            ),
                        ),
                    ),
                    'import' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/import',
                            'defaults' => array(
                                'controller' => 'Index',
                                'action' => 'import',
                            ),
                        ),
                    ),
                ),
            ),
            'youtube' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'    => '/youtube/:action/:youtube_id',
                    'constraints' => array(
                        'action'     => 'edit|delete',
                        'youtube_id' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'YoutubeTool\Controller\Index',
                    ),
                ),
            ),
            'youtube_list' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/youtube/index',
                ),
            ),
        ),
    ),
    'youtube' => array(
        'channels' => array(
            'arabicpod101' => 'UC5bjJ5x0i_XRGTMHF2IoL8w',
            'russianpod101' => 'UCUg_JDaHFAPEiFGDMddoIzA',
        ),
    ),
);
