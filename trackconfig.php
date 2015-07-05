<?php
   $pageName = "trackconfig.php";
   $pageErrors = array();
   $pageSuccess = array();
   
   include "inc.config.php";
   error_reporting($reportLevel);
   include "inc.common.php";
   
   // -- as a precaution only allow localhost to request this page
   $illegalRemoteAddr = false;
   if($_SERVER["REMOTE_ADDR"] != "127.0.0.1" && $_SERVER["REMOTE_ADDR"] != "::1"){
      $illegalRemoteAddr = true;
   }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='<?php echo $siteCharSet; ?>' />
<title>VM LiveView Lite (Setup)</title>
<link rel='stylesheet' type='text/css' href='./styles.css' />
</head>
<body>
<div class='center'>
<div class='break4'></div>
<?php
try{
   echo "<div class='sitename'>VM LiveView Lite Track Configuration</div>\n";
   
   echo "<div class='break6'></div>\n";
   
   if($illegalRemoteAddr === true){
      echo "<div class='red'>Sorry, but this page can only be requested by the localhost.</div>\n";
   }else{
      $mysqlHost = "localhost";
      $mysqlPort = "3306";
      $mysqlAdminUser = "root";
      $mysqlAdminPass = "";
      
      if(isset($_POST["MysqlHost"])){ $mysqlHost = $_POST["MysqlHost"]; }
      if(isset($_POST["MysqlPort"])){ $mysqlPort = $_POST["MysqlPort"]; }
      if(isset($_POST["MysqlAdminUser"])){ $mysqlAdminUser = $_POST["MysqlAdminUser"]; }
      if(isset($_POST["MysqlAdminPass"])){ $mysqlAdminPass = $_POST["MysqlAdminPass"]; }
      
      if(isset($_POST["Update"]) && $_POST["Update"] == "Update"){
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
         
         if($_POST["MapScale"] == ""){
            $pageErrors[] = "Map Scale must not be empty.";
         }elseif(is_numeric($_POST["MapScale"]) === false){
            $pageErrors[] = "The current value of Map Scale is not a number.";
         }elseif($_POST["MapScale"] < 0.01 || $_POST["MapScale"] > 1){
            $pageErrors[] = "Map Scale out of range. Value must be between '0.01' and '1.0'.";
         }
         
         if($_POST["MapRotation"] == ""){
            $pageErrors[] = "Map Rotation must not be empty.";
         }elseif(is_numeric($_POST["MapRotation"]) === false){
            $pageErrors[] = "The current value of Map Rotation is not a number.";
         }elseif($_POST["MapRotation"] < -360 || $_POST["MapRotation"] > 360){
            $pageErrors[] = "Map Rotation out of range. Value must be between '-360' and '360'.";
         }
         
         if($_POST["MapMirrorX"] == ""){
            $pageErrors[] = "Map Mirror X must not be empty.";
         }elseif(is_numeric($_POST["MapMirrorX"]) === false){
            $pageErrors[] = "The current value of Map Mirror X is not a number.";
         }elseif($_POST["MapMirrorX"] != 1 && $_POST["MapMirrorX"] != -1){
            $pageErrors[] = "Map Mirror X out of range. Value must be '1' or '-1'.";
         }
         
         if(count($pageErrors) == 0){
            $mySQLiConn = new mysqli("p:" . $mysqlHost, $mysqlAdminUser, $mysqlAdminPass, $dbName, $mysqlPort);
            
            if(!$mySQLiConn->connect_error){
               // -- update track configuration
               $updateTrackConfig = "UPDATE `trackconfig` " .
                                       "SET `MapScale` = '" . $_POST["MapScale"] . "', " .
                                           "`MapRotation` = '" . $_POST["MapRotation"] . "', " .
                                           "`MapMirrorX` = '" . $_POST["MapMirrorX"] . "' " .
                                     "WHERE `TrackName` = '" . $_POST["TrackNames"] . "'";
               
               if($mySQLiConn->query($updateTrackConfig)){
                  $pageSuccess[] = "Successfully updated track configuration for `" . $_POST["TrackNames"] . "`.";
                  
               }else{
                  $pageErrors[] = "Error track configuration: (" . $mySQLiConn->error . ")";
               }
               $mySQLiConn->close();
            }else{
               $pageErrors[] = "Error connecting to database: (" . $mySQLiConn->connect_error . ")";
            }
         }
      }
      
      $trackconfigTrackName = "";
      $trackconfigMapScale = 1.0;
      $trackconfigMapRotation = 0.0;
      $trackconfigMapMirrorX = 1;
      $trackconfigPosXMin = 0;
      $trackconfigPosXMax = 0;
      $trackconfigPosYMin = 0;
      $trackconfigPosYMax = 0;
      $optionTrackNames = "<select class='trackconfig' name='TrackNames' onchange='TrackConfig.submit();'>\n";
      
      $mySQLiConn = new mysqli("p:" . $dbHost, $dbReader, $dbReaderPass, $dbName, $dbPort);
      
      if(isset($_POST["TrackNames"])){
         $trackconfigTrackName = $_POST["TrackNames"];
      }
      
      if(!$mySQLiConn->connect_error){
         $selectTrackNames = "SELECT `TrackName` FROM `trackconfig`";
         if($resultTrackNames = $mySQLiConn->query($selectTrackNames)){
            if($resultTrackNames->num_rows > 0){
               while($rowTrackNames = $resultTrackNames->fetch_assoc()){
                  $optionSelected = "";
                  if($trackconfigTrackName == $rowTrackNames["TrackName"]){
                     $optionSelected = "selected='selected'";
                  }
                  $optionTrackNames .= "<option value='" . $rowTrackNames["TrackName"] . "' " . $optionSelected . ">" . $rowTrackNames["TrackName"] . "</option>\n";
                  if($trackconfigTrackName == ""){
                     $trackconfigTrackName = $rowTrackNames["TrackName"];
                  }
               }
            }
            $resultTrackNames->free();
         }
         
         $selectTrackConfig = "SELECT * FROM `trackconfig` WHERE `trackname` = '" . $trackconfigTrackName . "'";
         if($resultTrackConfig = $mySQLiConn->query($selectTrackConfig)){
            if($resultTrackConfig->num_rows > 0){
               while($rowTrackConfig = $resultTrackConfig->fetch_assoc()){
                  $trackconfigMapScale = $rowTrackConfig["MapScale"];
                  $trackconfigMapRotation = $rowTrackConfig["MapRotation"];
                  $trackconfigMapMirrorX = $rowTrackConfig["MapMirrorX"];
                  $trackconfigPosXMin = $rowTrackConfig["TrackPosXMin"];
                  $trackconfigPosXMax = $rowTrackConfig["TrackPosXMax"];
                  $trackconfigPosYMin = $rowTrackConfig["TrackPosYMin"];
                  $trackconfigPosYMax = $rowTrackConfig["TrackPosYMax"];
               }
            }
            $resultTrackConfig->free();
         }
         
         $mySQLiConn->close();
      }else{
         $pageErrors[] = "Error connecting to database: (" . $mySQLiConn->connect_error . ")";
      }
      
      $optionTrackNames .= "</select>\n";
      
      if(isset($_POST["Update"]) && $_POST["Update"] == "Update"){
         if(isset($_POST["MapScale"])){ $trackconfigMapScale = $_POST["MapScale"]; }
         if(isset($_POST["MapRotation"])){ $trackconfigMapRotation = $_POST["MapRotation"]; }
         if(isset($_POST["MapMirrorX"])){ $trackconfigMapMirrorX = $_POST["MapMirrorX"]; }
      }
      
      echo "<form name='TrackConfig' action='./trackconfig.php' method='post'>\n";
      echo "<fieldset class='setupleft'>\n";
      echo "<legend>Track Configuration</legend>\n";
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
      echo "<div>Track Names:</div>\n";
      echo $optionTrackNames;
      echo "<div class='break12'></div>\n";
      echo "<div>Map Scale:</div>\n";
      echo "<div><input class='setuptext' type='text' name='MapScale' value='" . $trackconfigMapScale . "' maxlength='16' autocomplete='off' /></div>\n";
      echo "<div>Map Rotation:</div>\n";
      echo "<div><input class='setuptext' type='text' name='MapRotation' value='" . $trackconfigMapRotation . "' maxlength='64' autocomplete='off' /></div>\n";
      echo "<div>Map Mirror X:</div>\n";
      echo "<div><input class='setuptext' type='text' name='MapMirrorX' value='" . $trackconfigMapMirrorX. "' maxlength='20' autocomplete='off' /></div>\n";
      echo "<div class='break12'></div>\n";
      echo "<div>Track PosX Min:</div>\n";
      echo "<div><input class='setuptextreadonly' type='text' name='TrackPosXMin' value='" . $trackconfigPosXMin. "' maxlength='20' autocomplete='off' readonly='readonly' /></div>\n";
      echo "<div>Track PosX Max:</div>\n";
      echo "<div><input class='setuptextreadonly' type='text' name='TrackPosXMax' value='" . $trackconfigPosXMax. "' maxlength='20' autocomplete='off' readonly='readonly' /></div>\n";
      echo "<div>Track PosY Min:</div>\n";
      echo "<div><input class='setuptextreadonly' type='text' name='TrackPosYMin' value='" . $trackconfigPosYMin. "' maxlength='20' autocomplete='off' readonly='readonly' /></div>\n";
      echo "<div>Track PosY Max:</div>\n";
      echo "<div><input class='setuptextreadonly' type='text' name='TrackPosYMax' value='" . $trackconfigPosYMax. "' maxlength='20' autocomplete='off' readonly='readonly' /></div>\n";
      echo "<div class='break12'></div>\n";
      echo "<div><input class='setupbutton' type='submit' name='Update' value='Update'></div>\n";
      echo "<div class='break12'></div>\n";
      echo "<div><span class='red'>*</span> Required fields.</div>\n";
      echo "</fieldset>\n";
      echo "</form>\n";
            
      echo "<table class='setupmsgs'>\n";
         if(count($pageSuccess)){
            echo "<tr><td>&nbsp;</td></tr>\n";
            foreach($pageSuccess as $successMsg){
               echo "<tr><td class='setupmsgsgreen'>&bull;&nbsp;" . $successMsg . "</td></tr>\n";
            }
         }
      echo "</table>\n";
      
      echo "<table class='setupmsgs'>\n";
         if(count($pageErrors)){
            echo "<tr><td>&nbsp;</td></tr>\n";
            foreach($pageErrors as $errorMsg){
               echo "<tr><td class='setupmsgsred'>&bull;&nbsp;" . $errorMsg . "</td></tr>\n";
            }
         }
      echo "</table>\n";
   }
   
   include "inc.copyright.php";
}catch(Exception $ex){
   writeErrorLog($pageName, "General Exception", "Exception Msg: (" . $ex->getMessage() . ")");
}
?>
</div>
</body>
</html>
