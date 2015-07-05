<?php
   include_once "inc.config.php";
   error_log(date("d.m.Y H:i:s") . " Message: (" . $_GET["msg"] . ") Severity: (" . $_GET["sev"] . ") URL: (" . $_GET["url"] . ")\r\n" , 3, "logs/css.errors.log");
?>
