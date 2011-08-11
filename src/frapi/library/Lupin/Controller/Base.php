<?php
/**
 */
class Lupin_Controller_Base extends Zend_Controller_Action
{
    public function init($styles = array())
    {
        // Init messages
        $this->view->message      = array();
        $this->view->infoMessage  = array();
        $this->view->errorMessage = array();

        $this->messenger = new Zend_Controller_Action_Helper_FlashMessenger;
        $this->messenger->setNamespace('messages');
        $this->_helper->addHelper($this->messenger);

        $this->errorMessenger = new Zend_Controller_Action_Helper_FlashMessenger;
        $this->errorMessenger->setNamespace('errorMessages');
        $this->_helper->addHelper($this->errorMessenger);

        $this->infoMessenger = new Zend_Controller_Action_Helper_FlashMessenger;
        $this->infoMessenger->setNamespace('infoMessages');
        $this->_helper->addHelper($this->infoMessenger);


        // Setup breadcrumbs
        $this->view->breadcrumbs = $this->buildBreadcrumbs($this->getRequest()->getRequestUri());

        $this->view->user = Zend_Auth::getInstance()->getIdentity();

        // Set the menu active element
        $uri = $this->getRequest()->getPathInfo();
        if (strrpos($uri, '/') === strlen($uri) - 1) {
            $uri = substr($uri, 0, -1);
        }
        
        if (!is_null($this->view->navigation()->findByUri($uri))) {
            $this->view->navigation()->findByUri($uri)->active = true;
        }

        $this->view->styleSheets = array_merge(array('css/styles.css'), $styles);
        
        $translate = Zend_Registry::get('tr');
        $this->view->tr = $translate;
        
        $this->view->setEscape(array('Lupin_Security', 'escape'));
    }

    private function buildBreadcrumbs($item)
    {
        $bread = explode('/', $item);
        array_shift($bread);
        $b = '';

        foreach ($bread as $key => $i) {
            $skip = false;
            if ($key === 1) {
                if ($bread[0] == 'subjects') {
                    $model = new Default_Model_Course;
                    $data  = $model->getSubjectInfoByCode($i);
                    $i     = $data['name'];
                }

                if ($bread[0] == 'courses') {
                    $model = new Default_Model_Course;
                    $data  = $model->getCourse($i);
                    $i     = $data['name'];
                }

                if ($bread[0] == 'teachers') {
                    $model = new Default_Model_Teacher;
                    $data  = $model->get($i);
                    $i     = $data['firstname'] . ' ' . $data['lastname'];
                }

                if ($bread[0] == 'lecture' && $bread[1] != 'faq' && is_numeric($bread[1])) {
                    $lectureId = (int)$bread[1];
                    $model = new Default_Model_Lecture;
                    $data  = $model->get($lectureId);
                    $i     = $data['lectureTitle'];
                }

                if ($bread[0] == 'lecture' && $bread[1] == 'faq') {
                    $lectureId = (int)$bread[2];
                    $model = new Default_Model_Lecture;
                    $data  = $model->get($lectureId);
                    $bread[0] = 'lecture';
                    $bread[1] = $lectureId;
                    $i     = $data['lectureTitle'];
                }

                if ($bread[0] == 'search') {
                    continue;
                }
            }

            if ($i == 'about') {
                $i = 'About Us';
            }

            if ($i == 'privacypolicy') {
                $i = 'Privacy Policy';
            }

            if ($i == 'terms-of-use') {
                $i = 'Terms of Use';
            }

            if ($i == 'faq') {
                $i = 'FAQ';
            }

            if ($i == 'id'  || $i == 'view' || $i == 'edit' || $i == 'lecture') {
                continue;
            }

            if (is_numeric($i)) {
                $i = 'FAQ';
            }

            if ((count($bread) - 1) === $key) {
                $crumb = ucfirst($i);
            } else {
                $url = $bread;
                while ((count($url) - 1) > $key) {
                     array_pop($url);
                }
                if ($skip === false) {
                    $crumb = '<a href="/' . implode('/', $url) . '">' . htmlspecialchars(ucfirst($i)) . '</a>';
                } else {
                    $crumb = htmlspecialchars(ucfirst($i));
                }
            }

            $b .= ' &gt; ' . stripslashes($crumb);
        }

        return $b;
    }

    public function addMessage($message, $persist = true)
    {
        if ($persist === true) {
            $this->messenger->addMessage($message);
        } else {
            $this->view->message[] = $message;
        }
    }

    public function addErrorMessage($message, $persist = false)
    {
        if ($persist === true) {
            $this->errorMessenger->addMessage($message);
        } else {
            $this->view->errorMessage[] = $message;
        }
    }

    public function addInfoMessage($message, $persist = true)
    {
        if ($persist === true) {
            $this->infoMessenger->addMessage($message);
        } else {
            $this->view->infoMessage[] = $message;
        }
    }

    public function postDispatch()
    {
        if ($this->messenger->hasMessages()) {
            $messages = $this->messenger->getMessages();
            $this->view->message = array_merge((array)$this->view->message, $messages);
            $this->messenger->clearMessages();
        }

        if ($this->infoMessenger->hasMessages()) {
            $messages = $this->infoMessenger->getMessages();
            $this->view->infoMessage = array_merge((array)$this->view->infoMessage, $messages);
            $this->infoMessenger->clearMessages();
        }

        if ($this->errorMessenger->hasCurrentMessages()) {
            $messages = $this->errorMessenger->getMessages();
            $this->view->errorMessage = array_merge((array)$this->view->errorMessage, $messages);
            $this->errorMessenger->clearCurrentMessages();
        }

        $badUrls = array(
            "/login",
            "/activation/*",
            "/password/reset/*",
            "/register(.*)?",
            "/favicon.ico",
        );

        $url = $this->getRequest()->getRequestUri();
        $redirect = true;
        foreach ($badUrls as $u) {
            $regex = '@' . $u . '@i';

            if (preg_match($regex, $url, $matches)) {
                $redirect = false;
            }
        }

        if ($redirect === true && $this->getResponse()->getHttpResponseCode() === 200) {
            $ns = new Zend_Session_Namespace('lastUrl');
            $ns->value = $url;
        }
    }
}
