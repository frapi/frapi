<?php
/**
 */
class Lupin_View extends Zend_View
{
    public function renderJson($var)
    {
        $view = new Zend_View();
        $view->json = json_encode($var);

        $view->setScriptPath(ROOT_PATH . '/admin/application/views/scripts');

        echo $view->render('jsonRendered.phtml');
        exit;
    }

    public function renderText($var, $escape = true)
    {
        $view = new Zend_View();
        $view->result = $escape ? htmlspecialchars($var) : $var;
        $view->setScriptPath(ROOT_PATH . '/admin/application/views/scripts');

        echo $view->render('textRendered.phtml');
        exit;
    }

    public function renderXml($var)
    {
        $this->renderText($var, false);
    }

    public function renderJsonError($msg, $code = 1)
    {
        $error = array();
        $error['error'] = $msg;
        $error['errorCode'] = $code;

        $this->renderJson($error);
    }

    public function renderExcel($data, $filename, $template = 'excelRendered.phtml')
    {
        $view = new Zend_View();

        header("Pragma: public");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: pre-check=0, post-check=0, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Content-Transfer-Encoding: none");
        header("Content-Type: application/vnd.ms-excel;");
        header("Content-type: application/x-msexcel");
        header("Content-Disposition: attachment; filename=$filename.xls");

        $view->data = $data;
        $view->setScriptPath(ROOT_PATH . '/application/views/scripts');

        echo $view->render($template);
        exit;
    }

    public function downloadHtml($data, $filename)
    {
        $view = new Zend_View();

        header("Pragma: public");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: pre-check=0, post-check=0, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Content-Transfer-Encoding: none");
        header("Content-Type: text/html;");
        header("Content-Disposition: attachment; filename=$filename.html");

        $view->result = $data;
        $view->setScriptPath(ROOT_PATH . '/application/views/scripts');

        echo $view->render('textRendered.phtml');
        exit;
    }
}