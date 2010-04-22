<?php
/**
 * @author Echolibre ltd. 2009 <freedom@echolibre.com>
 * @copyright Echolibre ltd. 2009
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDefaultAutoloader()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Default',
            'basePath'  => APPLICATION_PATH . '/modules/default',
        ));
    }

    protected function _initConfig()
    {
        Zend_Registry::set('config', new Zend_Config($this->getOptions()));
        
        $localConfigPath = APPLICATION_PATH . '/config/app/';
        Zend_Registry::set('localConfigPath', $localConfigPath);
    }

    protected function _initRoutes()
    {
        // Ensure front controller instance is present, and fetch it
        $this->bootstrap('FrontController');
        $fc = $this->getResource('FrontController');

        $this->bootstrap('Config');

        //add routing
        $routes = new Zend_Config_Ini(APPLICATION_PATH . '/config/routes.ini');
        $fc->getRouter()->addConfig($routes);
    }

    protected function _initExtraHelpers()
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->initView();
        $viewRenderer->view->addHelperPath('echolibre/View/Helper', 'Zend_View_Helper_FormStatic');
    }

    protected function _initAcl()
    {
        $acl = new Lupin_Acl;
        $aclHelper = new Lupin_Controller_Action_Helper_Acl(null, array('acl' => $acl));
        Zend_Controller_Action_HelperBroker::addHelper($aclHelper);

        //$user = Zend_Auth::getInstance()->getIdentity();
        //if (!is_null($user)) {
        //    $this->bootstrap('FrontController');
        //    $fc = $this->getResource('FrontController');
        //    $fc->registerPlugin(new Lupin_Plugin_TimeoutHandler);
        //}
    }

    protected function _initSsl()
    {
        // Add a config option to enforce ssl
        //$sslHelper = new Lupin_Controller_Action_Helper_Ssl();
        //Zend_Controller_Action_HelperBroker::addHelper($sslHelper);
    }

    protected function _initNavigation()
    {
        // Figure out if we want this.
        return;
        $this->bootstrap('view');
        $view = $this->getResource('view');

        $user = Zend_Auth::getInstance()->getIdentity();
        include APPLICATION_PATH . '/configs/navigation.php';
        $navigation = new Zend_Navigation($items);
        $view->navigation($navigation)->menu()->setUlClass('nav');
    }

    protected function _initDb()
    {
        $this->bootstrap('Config');
        Zend_Registry::set('db', $this->getPluginResource('db')->getDbAdapter());
    }
}