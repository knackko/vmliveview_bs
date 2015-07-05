<?php
   $pageNameIO = "inc.mysqli.conn.php";
   
   // -- connect to mysql server
   $mySQLiConn = @new mysqli("p:" . $dbHost, $dbReader, $dbReaderPass, $dbName, $dbPort);
   
   $mySQLiConnError = "";
   if($mySQLiConn->connect_error){
      $mySQLiConnError = "<div class='break6'></div>\n" .
                         "<div class='red'>Database connection error.</div>\n" .
                         "<div class='break6'></div>\n" .
                         "<a href='" . $siteURL . "/monitor.php'>Try again</a>\n";
      
      writeErrorLog($pageNameIO, "Connect to SQL Server", "Error No: (" . $mySQLiConn->connect_errno . ") Error Msg: (" . $mySQLiConn->connect_error . ")");
   }else{
      if(!$mySQLiConn->set_charset($dbCharSet)){
         writeErrorLog($pageNameIO, "Set connection character set", "Error Msg: (" . $mySQLiConn->error . ")");
      }
   }
?>
