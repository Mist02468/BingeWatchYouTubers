<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Application\Model\State;
use Application\Model\City;
use Application\Model\Company;

use Application\Model\StateTable;
use Application\Model\CityTable;
use Application\Model\CompanyTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Application\Model\StateTable' =>  function($sm) {
                    $tableGateway = $sm->get('StateTableGateway');
                    $table = new StateTable($tableGateway);
                    return $table;
                },
                'Application\Model\CityTable' =>  function($sm) {
                    $tableGateway = $sm->get('CityTableGateway');
                    $table = new CityTable($tableGateway);
                    return $table;
                },
                'Application\Model\CompanyTable' =>  function($sm) {
                    $tableGateway = $sm->get('CompanyTableGateway');
                    $table = new CompanyTable($tableGateway);
                    return $table;
                },
                'StateTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new State());
                    return new TableGateway('state', $dbAdapter, null, $resultSetPrototype);
                },
                'CityTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new City());
                    return new TableGateway('city', $dbAdapter, null, $resultSetPrototype);
                },
                'CompanyTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Company());
                    return new TableGateway('company', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
