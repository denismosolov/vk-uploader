<?php

namespace VkTool\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of Database
 *
 * @author denis
 */
class Database implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');

        return new \Zend\Db\Adapter\Adapter($config['db']);
    }
}
