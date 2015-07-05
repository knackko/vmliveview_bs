<?php
   $pageNameIO = "inc.requests.php";
   
   $currentConnections = 0;
   $currentServerLoad = 0;
   
   if(!$mySQLiConn->connect_error){
      $selectCurrentConnections = "SELECT COUNT(`USER`) AS `Connections` FROM `INFORMATION_SCHEMA`.`PROCESSLIST` WHERE `USER` = '" . $dbReader . "'";
      if($resultCurrentConnections = $mySQLiConn->query($selectCurrentConnections)){
         if($resultCurrentConnections->num_rows > 0){
            while($rowCurrentConnections = $resultCurrentConnections->fetch_assoc()){
               $currentConnections = $rowCurrentConnections["Connections"];
            }
         }
         $resultCurrentConnections->free();
      }
      
      if($currentConnections > 1){
         $currentConnections --;
      }
      
      $mysqliStats = explode("  ", $mySQLiConn->stat());
      
      $currentServerLoad  = str_ireplace("Queries per second avg: ", "",$mysqliStats[7]);
   }else{
      writeErrorLog($pageNameIO, "Connect to sql server", "ERROR: (" . $mySQLiConn->connect_error . ")");
   }
?>
