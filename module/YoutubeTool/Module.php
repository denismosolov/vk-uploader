<?php
namespace YoutubeTool;

use YoutubeTool\Model\YoutubeVideo;
use YoutubeTool\Model\YoutubeVideoTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\ModuleManager\Feature\ServiceProviderInterface;

class Module implements ServiceProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    // I am using Composer, so I could not implement getAutoloaderConfig() . Instead se psr-4 key in composer.json.
    public function getAutoloaderConfig()
    {
         return array(
             'Zend\Loader\ClassMapAutoloader' => array(
                 __DIR__ . '/autoload_classmap.php',
             ),
             'Zend\Loader\StandardAutoloader' => array(
                 'namespaces' => array(
                     __NAMESPACE__ => __DIR__ . '/src',
                 ),
             ),
         );
    }

    // @todo: remove anonymus functions and use classes
    //public function getServiceConfig()
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'YoutubeTool\Model\YoutubeVideoTable' =>  function ($sm) {
                    $tableGateway = $sm->get('YoutubeVideoTableGateway');
                    $table = new YoutubeVideoTable($tableGateway);

                    return $table;
                },
                'YoutubeVideoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new YoutubeVideo());

                    return new TableGateway('youtube_videos', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
