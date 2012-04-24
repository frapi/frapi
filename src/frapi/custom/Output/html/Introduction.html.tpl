<?php
require_once CUSTOM_LIBRARY . DIRECTORY_SEPARATOR . 'markdown/markdown_extended.php';

function _makeAnchor($string)
{
    return preg_replace('/[^a-zA-Z0-9_\-]+/', '', $string);
}

function _sortActions($a, $b)
{
    if ($a['route'] == '/') {
       return -1;
    }
    return ($a['name'] < $b['name']) ? -1 : 1;
}

function _sortErrors($a, $b)
{
    return ($a['http_code'] . $a['name'] < $b['http_code'] . $b['name']) ? -1 : 1;
}

function _sortOutputs($a, $b)
{
    if ($a['default'] == '1') {
        return -1;
    }
    return ($a['name'] < $b['name']) ? -1 : 1;
}

function _sortMimetypes($a, $b)
{
    return ($a['output_format'] < $b['output_format']) ? -1 : 1;
}

$internal = new Frapi_Internal();
$actions = $internal->getConfiguration('actions')->getAll('action');
$errors = $internal->getConfiguration('errors')->getAll('error');
$outputs = $internal->getConfiguration('outputs')->getAll('output');
$mimetypes = $internal->getConfiguration('mimetypes')->getAll('mimetype');

usort($actions, '_sortActions');

usort($errors, '_sortErrors');

usort($outputs, '_sortOutputs');

usort($mimetypes, '_sortMimetypes');

foreach ($mimetypes as $mimetype) {
    $grouped[$mimetype['output_format']][] = $mimetype['mimetype'];
}

$mimetypes = $grouped;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>API Documentation</title>
        <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Droid+Sans">
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js"></script>
        <style type="text/css">
            body {
                font-family: 'Droid Sans', serif;
                padding-left: 10%;
                padding-right: 10%;
            }

            h2 {
                float: left;
                margin-top: 1.5em;
            }

            /**
            * HTML (admin inline) Documentation
            *
            */
            h3.desc {
                padding-top: 20px;
            }

            p.description, div.description {
                padding: 5px;
                padding-left:10px;
                margin-bottom: 10px;
                font-size: 16px;
                color: #000;
            }

            p.error-description, div.error-description {
                padding: 5px;
                padding-left:10px;
                border-left: 3px solid #a41e22;
                font-size: 16px;
                color: #000;
            }

            .doc-subdata{
                margin-left: 2em;
            }

            .doc-subdata h2 {
                float: none;
            }

            .action, .error {
                background: #e7f0f7;
                width: 100%;
                position: relative;
            }

            .action .top, .error .top {
                position: relative;
                float: left;
                width: 100%;
                height: 30px;
                margin-bottom:25px;
            }

            .action .top .name, .error .top .name, .mimetype .top .name, .endpoint .top .name {
                width: 20%;
                float: left;
                height: 30px;
                position: relative;
                background: #0F6AB4;
                -webkit-border-radius: 4px 0 0 4px;
                -moz-border-radius: 4px 0 0 4px;
                border-radius: 4px 0 0 4px;
                color: white;
                text-align: center;
                padding-top: 12px;
                cursor: pointer;
                clear: left;
            }

            .error .top .name {
                background: #a41e22;
            }

            .mimetype .top .name {
                background: #7d1fff;
                cursor: auto;
            }

            .endpoint .top .name {
                background: #A6A6A6;
                cursor: auto;
            }


            .action .top .route, .error .top .route, .mimetype .top .route, .endpoint .top .route  {
                width: 78%;
                height: 30px;
                background: #e7f0f7;
                border: 1px solid #c3d9ec;
                position: relative;
                float: left;
                padding-left: 1%;
                padding-top: 10px;
                font-size: 16px;
                cursor: pointer;
                -webkit-border-radius: 4px 0 0 4px;
                -moz-border-radius: 4px 0 0 4px;
                border-radius: 0 4px 4px 0;
            }

            .error .top .route {
                background: #f5e8e8;
                border: 1px solid #e8c6c7;
            }

            .mimetype .top .route {
                background: #e2cfff;
                border: 1px solid #7d1fff;
                margin-bottom: 1em;
                cursor: auto;
            }

            .endpoint .top .route {
                background: #e3e3e3;
                border: 1px solid #A6A6A6;
                margin-bottom: 1em;
                cursor: auto;
            }

            .action .stub{
                background: #e7f0f7;
                display: none;
                position: relative;
                float: left;
                border: 1px solid #c3d9ec;
                width: 98%;
                padding-left: 10px;
                margin-top: -15px;
                margin-bottom:25px;
            }

            .root .name {
                background-color: green !important;
            }

            .root .route, .root .stub {
                background-color: #ccffcc !important;
                border-color: #99ff99 !important;
            }

            .error .stub {
                background: #f5e8e8;
                display: none;
                position: relative;
                float: left;
                border: 1px solid #e8c6c7;
                margin-top: -15px;
                padding-left: 10px;
                width: 98%;
                margin-bottom:25px;
            }

            .action .stub .doc-subdata {
                color: #0F6AB4;
            }

            .error .stub .doc-subdata {
                color: #a41e22;
            }

            .doc-table tr th.param-name, .doc-table tr td.param-name {
                width: 100px;
                padding: 10px;
                text-align: center;
            }

            .action .stub .doc-subdata .doc-table tr th{
                border-bottom: 2px solid #0F6AB4;
            }

            .action .stub .doc-subdata .doc-table tr td {
                border-bottom: 1px solid #0F6AB4;
            }

            .error .stub .doc-subdata .doc-table tr td {
                border-bottom: 1px solid #a41e22;
            }

            .error .stub .doc-subdata .doc-table tr th{
                border-bottom: 2px solid #a41e22;
            }

            .doc-table tr th.param-required, .doc-table tr td.param-required {
                width: 70%;
                padding: 10px;
                text-align: center;
            }

            .doc-table tr td.param-name {
                color: #000;
            }

            blockquote {
                padding-left: 1em;
                border-left: 3px solid #0F6AB4;
            }
        </style>
    </head>
    <body>
        <h1>API Documentation</h1>

        <?php
        $protocol = !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
        $url = $protocol . '://' .$_SERVER['HTTP_HOST']. '/';
        ?>
        <div class="endpoint">
            <div class="top">
                <span class="name">Endpoint:</span>
                <span class="route"><?php echo '<a href="' .$url. '">' .$url. '</a>'; ?></span>
            </div>
        </div>

        <?php
        if (count($actions)) {
            foreach ($actions as $action) {
                if ($action['enabled'] != 1) {
                    continue;
                }
                ?>
                <div class="action <?php if ($action['route'] == '/') { echo 'root'; } ?>" hash="<?php echo $action['hash']; ?>">
                    <div class="top">
                        <span class="name" hash="<?php echo $action['hash']; ?>"><?php echo $action['name'] ?></span>
                        <span class="route"><?php echo (isset($action['route']) && $action['route'] != '/') ? $action['route'] : '<em>Click to show</em>'; ?></span>
                    </div>
                    <div class="stub" id="action-<?php echo $action['hash']; ?>">
                        <div class="doc-subdata">
                            <?php
                            if (isset($action['parameters']) && !empty($action['parameters']) && count($action['parameters'])) {
                                ?>
                                <h3>Parameters</h3>
                                <table class="doc-table">
                                    <tr>
                                        <th class="param-name">Name</th>
                                        <th class="param-required">Required</th>
                                    </tr>
                                    <?php
                                    foreach ($action['parameters'] as $key => $param) {
                                        if (is_array($param) && isset($param[0])) {
                                            foreach ($param as $subkey => $subparam) {
                                                ?>
                                                <tr>
                                                    <td class="param-name"><?php echo $subparam['name'] ?></td>
                                                    <td class="param-required">
                                                        <?php
                                                            echo isset($subparam['required']) && $subparam['required'] == '1'
                                                                ? '<strong>&#10003;</strong>' : '<strong>&#10007;</strong>'
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo $param['name']; ?></td>
                                                <td class="param-required">
                                                    <?php
                                                        echo isset($subparam['required']) && $subparam['required'] == '1'
                                                        ? '<strong>&#10003;</strong>' : '<strong>&#10007;</strong>'
                                                    ?>
                                                </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <?php
                            }
                            if (!empty($action['description'])) {
                                ?>
                                <div class="description"><?php echo MarkdownExtended($action['description']); ?></div>
                                <?php
                            } else {
                                ?>
                                <h3 class="desc">Description</h3>
                                <p class="description">This API call has no description :-(</p>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                if ($action['route'] == '/') {
                    echo '<h2>Actions</h2>';
                }
            }
        }
        ?>
        <h2>Errors</h2>
        <?php
        if (!empty($errors)) {
            foreach ($errors as $error) {
                ?>
                <div class="error" hash="<?php echo $error['hash']; ?>">
                    <div class="top">
                        <span class="name" hash="<?php echo $error['hash']; ?>"><?php echo ((!is_null($error['http_code']))?($error['http_code']):('400'))?></span>
                        <span class="route"><?php echo $error['name']; ?></span>
                    </div>
                    <div class="stub" id="error-<?php echo $error['hash']; ?>">
                        <div class="doc-subdata">
                            <?php
                            if (!empty($error['description'])) {
                                ?>
                                <h3>Description</h3>
                                <div class="error-description"><?php echo MarkdownExtended($error['description']) ?></div>
                                <?php
                            }
                            ?>
                            <h3>Error Message</h3>
                            <p class="error-description">
                                <?php echo $error['message']; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>

        <h2>Output Formats / Mimetypes</h2>
        <?php
        if (count($mimetypes)) {
            foreach ($mimetypes as $format => $mimetype) {
                ?>
                <div class="mimetype">
                    <div class="top">
                        <span class="name"><?php echo $format; ?></span>
                        <span class="route"><?php echo implode('<strong> | </strong>', $mimetype); ?></span>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <p>There is currently no mimetypes.</p>
            <?php
        }
        ?>

        <script type="text/javascript" charset="utf-8">
            $(document).ready(function() {
                $('.action .name').live('click', function() {
                    var hash = $(this).attr('hash');
                    $('#action-' + hash).slideToggle();
                });

                $('.action .route').live('click', function() {
                    var hash = $(this).parent().find('.name').attr('hash');
                    $('#action-' + hash).slideToggle();
                });

                $('.error .name').live('click', function() {
                    var hash = $(this).attr('hash');
                    $('#error-' + hash).slideToggle();
                });

                $('.error .route').live('click', function() {
                    var hash = $(this).parent().find('.name').attr('hash');
                    $('#error-' + hash).slideToggle();
                });
            });
        </script>
    </body>
</html>
