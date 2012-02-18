<?php
error_reporting(E_ALL);
set_error_handler("error_function");

function error_function($error_level, $error_message, $error_file, $error_line, $error_context) {
  $res = "<h1>Error</h1><table>";
  $res .= "<tr><th>error_level:</th><td>$error_level</td></tr>";
  $res .= "<tr><th>error_message:</th><td>$error_message</td></tr>";
  $res .= "<tr><th>error_file:</th><td>$error_file</td></tr>";
  $res .= "<tr><th>error_line:</th><td>$error_line</td></tr>";
  $res .= "<tr><th>error_context:</th><td>$error_context</td></tr></table>";
  echo $res;
  die();
}

require_once('../markdown_extended.php');

if(isset($_POST["markdown"]) && !empty($_POST["markdown"])){
  $markdown = $_POST["markdown"];  
  // Always add a 'prettyprint' to <pre> elements  
  echo MarkdownExtended($markdown, array('pre' => 'prettyprint'));
}
die();
?>