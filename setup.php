<?php
   $pageName = "setup.php";
   $pageErrors = array();
   $pageSuccess = array();
   
   include "inc.config.php";
   error_reporting($reportLevel);
   include "inc.common.php";
   include "inc.mysqli.create.php";
   
   // -- as a precaution only allow localhost to request this page
   $illegalRemoteAddr = false;
   if($_SERVER["REMOTE_ADDR"] != "127.0.0.1" && $_SERVER["REMOTE_ADDR"] != "::1"){
      $illegalRemoteAddr = true;
   }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8' />
<title>VM LiveView Lite (Setup)</title>
<link rel='stylesheet' type='text/css' href='./styles.css' />
</head>
<body>
<div class='center'>
<div class='break4'></div>
<?php
try{
   echo "<div class='sitename'>VM LiveView Lite Setup</div>\n";
   
   echo "<div class='break6'></div>\n";
   
   if($illegalRemoteAddr === true){
      echo "<div class='red'>Sorry, but this page can only be requested by the localhost.</div>\n";
   }else{
      $mysqlHost = "localhost";
      $mysqlPort = "3306";
      $mysqlAdminUser = "root";
      $mysqlAdminPass = "";
      $databaseName = "vmliveview01";
      $databaseWriter = "vmliveview01dbw";
      $databaseWriterHost = "localhost";
      $databaseWriterPass = genPassword(20);
      
      if(isset($_POST["MysqlHost"])){ $mysqlHost = $_POST["MysqlHost"]; }
      if(isset($_POST["MysqlPort"])){ $mysqlPort = $_POST["MysqlPort"]; }
      if(isset($_POST["MysqlAdminUser"])){ $mysqlAdminUser = $_POST["MysqlAdminUser"]; }
      if(isset($_POST["MysqlAdminPass"])){ $mysqlAdminPass = $_POST["MysqlAdminPass"]; }
      if(isset($_POST["DatabaseName"])){ $databaseName = $_POST["DatabaseName"]; }
      if(isset($_POST["DatabaseWriter"])){ $databaseWriter = $_POST["DatabaseWriter"]; }
      if(isset($_POST["DatabaseWriterHost"])){ $databaseWriterHost = $_POST["DatabaseWriterHost"]; }
      if(isset($_POST["DatabaseWriterPass"])){ $databaseWriterPass = $_POST["DatabaseWriterPass"]; }
      
      if(isset($_POST["Setup"]) && $_POST["Setup"] == "Setup"){
         if($_POST["MysqlHost"] == ""){
            $pageErrors[] = "MySQL Host must not be empty.";
         }elseif(strlen($_POST["MysqlHost"]) > 64){
            $pageErrors[] = "MySQL Host must not be greater than 64 characters.";
         }
         
         if($_POST["MysqlPort"] == ""){
            $pageErrors[] = "MySQL Port must not be empty.";
         }elseif(strlen($_POST["MysqlPort"]) > 5){
            $pageErrors[] = "MySQL Port must not be greater than 5 characters.";
         }
         
         if($_POST["MysqlAdminUser"] == ""){
            $pageErrors[] = "MySQL Admin User must not be empty.";
         }elseif(strlen($_POST["MysqlAdminUser"]) > 16){
            $pageErrors[] = "MySQL Admin User must not be greater than 16 characters.";
         }
         
         if($_POST["MysqlAdminPass"] == ""){
            $pageErrors[] = "MySQL Admin Password must not be empty.";
         }elseif(strlen($_POST["MysqlAdminPass"]) > 20){
            $pageErrors[] = "MySQL Admin Password must not be greater than 20 characters.";
         }
         
         if($_POST["DatabaseName"] == ""){
            $pageErrors[] = "Database Name must not be empty.";
         }elseif(strlen($_POST["DatabaseName"]) > 20){
            $pageErrors[] = "Database Name must not be greater than 20 characters.";
         }
         
         if($_POST["DatabaseWriter"] == ""){
            $pageErrors[] = "Database Writer must not be empty.";
         }elseif(strlen($_POST["DatabaseWriter"]) > 16){
            $pageErrors[] = "Database Writer must not be greater than 16 characters.";
         }
         
         if($_POST["DatabaseWriterHost"] == ""){
            $pageErrors[] = "Database Writer Connectivity Host must not be empty.";
         }elseif(strlen($_POST["DatabaseWriterHost"]) > 64){
            $pageErrors[] = "Database Writer Connectivity Host must not be greater than 64 characters.";
         }
         
         if($_POST["DatabaseWriterPass"] == ""){
            $pageErrors[] = "Database Writer Password must not be empty.";
         }elseif(strlen($_POST["DatabaseWriterPass"]) > 20){
            $pageErrors[] = "Database Writer Password must not be greater than 20 characters.";
         }
         
         if(count($pageErrors) == 0){
            $mySQLiConn = new mysqli("p:" . $mysqlHost, $mysqlAdminUser, $mysqlAdminPass, "mysql", $mysqlPort);
            
            if(!$mySQLiConn->connect_error){
               // -- create database
               $createDatabase = "CREATE DATABASE `" . $databaseName . "` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin";
               if($mySQLiConn->query($createDatabase)){
                  $pageSuccess[] = "Successfully created database `" . $databaseName . "`.";
                  
                  $mySQLiConn->change_user($mysqlAdminUser, $mysqlAdminPass, $databaseName);
                  
                  if(!$mySQLiConn->connect_error){
                     // -- create table `hotlaps`
                     if($mySQLiConn->query($createTableHotlaps)){
                        $pageSuccess[] = "Successfully created table `hotlaps`.";
                     }else{
                        $pageErrors[] = "Error creating table `hotlaps`: (" . $mySQLiConn->error .  ")";
                     }
                     
                     // -- create table `racelaps`
                     if($mySQLiConn->query($createTableRacelaps)){
                        $pageSuccess[] = "Successfully created table `racelaps`.";
                     }else{
                        $pageErrors[] = "Error creating table `racelaps`: (" . $mySQLiConn->error .  ")";
                     }
                     
                     // -- create table `sessioninfos`
                     if($mySQLiConn->query($createTableSessioninfos)){
                        $pageSuccess[] = "Successfully created table `sessioninfos`.";
                     }else{
                        $pageErrors[] = "Error creating table `sessioninfos`: (" . $mySQLiConn->error .  ")";
                     }
                     
                     // -- insert default session into table `sessioninfos`
                     if($mySQLiConn->query($insertDefaultSessionInfo)){
                        $pageSuccess[] = "Successfully inserted default session into table `sessioninfos`.";
                     }else{
                        $pageErrors[] = "Error inserting default session into table `sessioninfos`: (" . $mySQLiConn->error .  ")";
                     }
                     
                     // -- create table `slots`
                     if($mySQLiConn->query($createTableSlots)){
                        $pageSuccess[] = "Successfully created table `slots`.";
                     }else{
                        $pageErrors[] = "Error creating table `slots`: (" . $mySQLiConn->error .  ")";
                     }
                     
                     // -- create table `trackconfig`
                     if($mySQLiConn->query($createTableTrackConfig)){
                        $pageSuccess[] = "Successfully created table `trackconfig`.";
                     }else{
                        $pageErrors[] = "Error creating table `trackconfig`: (" . $mySQLiConn->error .  ")";
                     }
                     
                     // -- create table `trackdata`
                     if($mySQLiConn->query($createTableTrackData)){
                        $pageSuccess[] = "Successfully created table `trackdata`.";
                     }else{
                        $pageErrors[] = "Error creating table `trackdata`: (" . $mySQLiConn->error .  ")";
                     }
                                          
                     // -- create table `xlaps`
                     if($mySQLiConn->query($createTableXlaps)){
                        $pageSuccess[] = "Successfully created table `xlaps`.";
                     }else{
                        $pageErrors[] = "Error creating table `xlaps`: (" . $mySQLiConn->error .  ")";
                     }
                     
                     // -- create database writer
                     $createDatabaseWriter = "CREATE USER '" . $databaseWriter . "'@'" . $databaseWriterHost . "' IDENTIFIED BY '" . $databaseWriterPass . "'";
                     if($mySQLiConn->query($createDatabaseWriter)){
                        $pageSuccess[] = "Successfully created user `" . $databaseWriter . "`@`" . $databaseWriterHost . "`.";
                     }else{
                        $pageErrors[] = "Error creating user `" . $databaseWriter . "`: (" . $mySQLiConn->error . ")";
                     }
                  
                     // -- grant privileges to database writer on all tables
                     $createDatabaseReader = "GRANT SELECT, INSERT, UPDATE, DELETE ON `" . $databaseName . "`.* TO '" . $databaseWriter . "'@'" . $databaseWriterHost . "'";
                     if($mySQLiConn->query($createDatabaseReader)){
                        $pageSuccess[] = "Successfully granted privileges to user `" . $databaseWriter . "`@`" . $databaseWriterHost . "` on all tables: SELECT, INSERT, UPDATE, DELETE";
                     }else{
                        $pageErrors[] = "Error granting privileges to user `" . $databaseWriter . "`@`" . $databaseWriterHost . "` on all tables: (" . $mySQLiConn->error .  ")";
                     }
                  }
               }else{
                  $pageErrors[] = "Error creating database: (" . $mySQLiConn->error . ")";
               }
               $mySQLiConn->close();
            }else{
               $pageErrors[] = "Error connecting to database: (" . $mySQLiConn->connect_error . ")";
            }
         }
      }
      
      echo "<form name='SetupDatabase' action='./setup.php' method='post'>\n";
      echo "<fieldset class='setupleft'>\n";
      echo "<legend>Setup Database</legend>\n";
      echo "<div>MySQL Host:<span class='red'>*</span></div>\n";
      echo "<div><input class='setuptextreadonly' type='text' name='MysqlHost' value='" . $mysqlHost . "' maxlength='64' autocomplete='off' readonly='readonly'/></div>\n";
      echo "<div>MySQL Port:<span class='red'>*</span></div>\n";
      echo "<div><input class='setuptext' type='text' name='MysqlPort' value='" . $mysqlPort . "' maxlength='5' autocomplete='off' /></div>\n";
      echo "<div class='break12'></div>\n";
      echo "<div>MySQL Admin User:<span class='red'>*</span></div>\n";
      echo "<div><input class='setuptext' type='text' name='MysqlAdminUser' value='" . $mysqlAdminUser . "' maxlength='16' autocomplete='off' /></div>\n";
      echo "<div>MySQL Admin Password:<span class='red'>*</span></div>\n";
      echo "<div><input class='setuptext' type='text' name='MysqlAdminPass' value='" . $mysqlAdminPass . "' maxlength='20' autocomplete='off' /></div>\n";
      echo "<div class='break12'></div>\n";
      echo "<div>Database Name:<span class='red'>*</span></div>\n";
      echo "<div><input class='setuptext' type='text' name='DatabaseName' value='" . $databaseName . "' maxlength='20' autocomplete='off' /></div>\n";
      echo "<div class='break12'></div>\n";
      echo "<div>Database Writer Login Name:<span class='red'>*</span></div>\n";
      echo "<div><input class='setuptext' type='text' name='DatabaseWriter' value='" . $databaseWriter . "' maxlength='16' autocomplete='off' /></div>\n";
      echo "<div>Database Writer Connectivity Host:<span class='red'>*</span></div>\n";
      echo "<div><input class='setuptext' type='text' name='DatabaseWriterHost' value='" . $databaseWriterHost . "' maxlength='64' autocomplete='off' /></div>\n";
      echo "<div>Database Writer Login Name Password:<span class='red'>*</span></div>\n";
      echo "<div><input class='setuptext' type='text' name='DatabaseWriterPass' value='" . $databaseWriterPass. "' maxlength='20' autocomplete='off' /></div>\n";
      echo "<div class='break12'></div>\n";
      echo "<div><span class='red'>### WARNING ###<br/>Never ever use a root user account for<br/>the `vmliveview` database connectivity.</span></div>\n";
      echo "<div class='break12'></div>\n";
      echo "<div><input class='setupbutton' type='submit' name='Setup' value='Setup'></div>\n";
      echo "<div class='break12'></div>\n";
      echo "<div><span class='red'>*</span> Required fields.</div>\n";
      echo "</fieldset>\n";
      echo "</form>\n";
      
      echo "<br>\n";
      
      echo "<fieldset>\n";
      echo "<h3>Setup Messages</h3>\n";
         if(count($pageSuccess)){
            foreach($pageSuccess as $successMsg){
               echo "<div class='setupmsgsgreen'>&bull;&nbsp;" . $successMsg . "</div>\n";
            }
            echo "<div class='info'>&bull;&nbsp;Please update the configuration files `inc.config.php` and `rFactorSLC.cfg` accordingly.</div>\n";
         }
         
         if(count($pageErrors)){
            foreach($pageErrors as $errorMsg){
               echo "<div class='setupmsgsred'>&bull;&nbsp;" . $errorMsg . "</div>\n";
            }
         }
      echo "</fieldset>\n";
   }
   
   include "inc.copyright.php";
}catch(Exception $ex){
   writeErrorLog($pageName, "General Exception", "Exception Msg: (" . $ex->getMessage() . ")");
}
?>
</div>
</body>
</html>
