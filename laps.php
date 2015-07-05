<?php
   $pageName = "laps.php";
   
   include "inc.config.php";
   error_reporting($reportLevel);
   include "inc.common.php";
   include "inc.mysqli.conn.php";
   include "inc.requests.php";
   
   $IncludeLang = "./lang/en.php";
   if(isset($_GET["Lang"])){
      if(strlen($_GET["Lang"]) == 2 && file_exists("./lang/" . $_GET["Lang"] . ".php")){
         $IncludeLang = "./lang/" . $_GET["Lang"] . ".php";
      }
   }
   include $IncludeLang;
   
   $SlotID = -1;
   if(isset($_GET["SlotID"])){
      if(is_numeric($_GET["SlotID"]) && $_GET["SlotID"] <= 105){
         $SlotID = $_GET["SlotID"];
      }else{
         writeErrorLog($pageName, "urlGetSlotID", "The value of URL GET parameter [SlotID] did not pass validation. Value: (" . $_GET["SlotID"] . ")");
      }
   }
   
   $DriverName = "";
   $TableName = "racelaps";
   if(isset($_GET["DriverName"])){
      $TableName = "xlaps";
      $DriverName = urldecode($_GET["DriverName"]);
      if(validateNameVars($DriverName) === true){
      }else{
         $DriverName = "";
         writeErrorLog($pageName, "urlGetDriverName", "The value of URL GET parameter [DriverName] did not pass validation. Value: (" . $_GET["DriverName"] . ")");
      }
   }
   
   $SelectLapsLimit = "";
   if(isset($displayMaxLaps) && $displayMaxLaps > 0){
      $SelectLapsLimit = " LIMIT " . $displayMaxLaps;
   }
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html>
<head>
<meta http-equiv='pragma' content='no-cache'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='content-type' content='text/html; charset=<?php echo $siteCharSet; ?>'>
<title>VM LiveView Lite (Laps)</title>
<link rel='stylesheet' type='text/css' href='<?php echo $siteURL; ?>/styles.css'>
</head>
<body>
<div class='center'>
<div class='break4'></div>
<?php
   echo "<div class='sitename'>" . $siteName . "</div>\n";
   try{
      if(!$mySQLiConn->connect_error){
         $SelectInPits = "SELECT SQL_CACHE COUNT(`InPits`) AS `TotalPitstops` FROM `" . $TableName . "` WHERE `SlotID` = '" . $mySQLiConn->real_escape_string($SlotID) . "' AND `InPits` = '1' LIMIT 1";
         
         if($TableName == "xlaps"){
            $SelectInPits = "SELECT SQL_CACHE COUNT(`InPits`) AS `TotalPitstops` FROM `" . $TableName . "` WHERE `DriverName` = '" . $mySQLiConn->real_escape_string($DriverName) . "' AND `InPits` = '1' LIMIT 1";
         }
         
         $TotalPitstops = 0;
         if($ResultInPits = $mySQLiConn->query($SelectInPits)){
            if($ResultInPits->num_rows > 0){
               while($Row = $ResultInPits->fetch_assoc()){
                  $TotalPitstops = $Row["TotalPitstops"];
               }
            }
            $ResultInPits->free();
         }
         
         $SelectLaps = "SELECT SQL_CACHE * FROM `" . $TableName . "` WHERE `SlotID` = '" . $mySQLiConn->real_escape_string($SlotID) . "' ORDER BY `LapNo` DESC" . $SelectLapsLimit;
         if($TableName == "xlaps"){
            $SelectLaps = "SELECT SQL_CACHE * FROM `" . $TableName . "` WHERE `DriverName` = '" . $mySQLiConn->real_escape_string($DriverName) . "' ORDER BY `LapNo` DESC" . $SelectLapsLimit;
         }
         
         if($ResultLaps = $mySQLiConn->query($SelectLaps)){
            if($ResultLaps->num_rows > 0){
               $i = 1;
               
               $AvarageLapTime = 0;
               while($Row = $ResultLaps->fetch_assoc()){
                  $AvarageLapTime += $Row["LapTime"];
               }
               if($AvarageLapTime > 0){
                  $AvarageLapTime = formatLapTime($AvarageLapTime / $ResultLaps->num_rows);
               }
               
               // -- reset result pointer to the first Row
               $ResultLaps->data_seek(0);
               
               echo "<fieldset>\n";
               echo "<legend>" . $langTimedLaps . " (" . $ResultLaps->num_rows . ") | " . $langAvgLapTime . " (" . $AvarageLapTime . ") | " . $langTotalStops . " (" . $TotalPitstops . ")</legend>\n";
               echo "<table>\n";
               echo "<tr><th class='laps'>" . $langLapNo . "</th>" .
                        "<th class='laps'>" . $langDriver . "</th>" .
                        "<th class='laps'>" . $langClass . "</th>" .
                        "<th class='laps'>" . $langVehicle . "</th>" .
                        "<th class='laps'>" . $langSector1 . "</th>" .
                        "<th class='laps'>" . $langSector2 . "</th>" .
                        "<th class='laps'>" . $langSector3 . "</th>" .
                        "<th class='laps'>" . $langLapTime . "</th>" .
                        "<th class='laps'>" . $langInPits . "</th>" .
                        "<th class='laps'>" . $langLastUpdate . "</th>" .
                    "</tr>\n";
                  
                  while($Row = $ResultLaps->fetch_assoc()){
                     $VehicleClass = $Row["VehicleClass"];
                     switch($VehicleClass){
                        case $class1RealName:
                           $VehicleClass = "<span style='color:" . $class1Color . ";'>" . $class1DisplayName . "</span>";
                           $LapTime = "<span style='color:" . $class1Color . ";'>" . formatLapTime($Row["LapTime"]) . "</span>";
                           break;
                        
                        case $class2RealName:
                           $VehicleClass = "<span style='color:" . $class2Color . ";'>" . $class2DisplayName . "</span>";
                           $LapTime = "<span style='color:" . $class2Color . ";'>" . formatLapTime($Row["LapTime"]) . "</span>";
                           break;
                           
                        case $class3RealName:
                           $VehicleClass = "<span style='color:" . $class3Color . ";'>" . $class3DisplayName . "</span>";
                           $LapTime = "<span style='color:" . $class3Color . ";'>" . formatLapTime($Row["LapTime"]) . "</span>";
                           break;
                        
                        case $class4RealName:
                           $VehicleClass = "<span style='color:" . $class4Color . ";'>" . $class4DisplayName . "</span>";
                           $LapTime = "<span style='color:" . $class4Color . ";'>" . formatLapTime($Row["LapTime"]) . "</span>";
                           break;
                           
                        default:
                           $VehicleClass = "<span class='classUC'>" . $Row["VehicleClass"] . "</span>";
                           $LapTime = "<span class='classUC'>" . formatLapTime($Row["LapTime"]) . "</span>";
                           break;
                     }
                     
                     $InPits = "-";
                     if($Row["InPits"] == 1){
                        $InPits = "<span class='blue'>PIT</span>";
                     }
                     
                     $trClass = "'bg0'";
                     if(($i % 2) == 0){$trClass = "'bg1'";}
                     
                     echo "<tr class=".$trClass."><td class='center'>" . $Row["LapNo"] . "</td>" .
                                                 "<td>" . $Row["DriverName"] . "</td>" .
                                                 "<td class='center'>" . $VehicleClass . "</td>" .
                                                 "<td>" . $Row["Vehicle"] . "</td>" .
                                                 "<td class='righttime'>" . formatLapTime($Row["Sec1"]) . "</td>" .
                                                 "<td class='righttime'>" . formatLapTime($Row["Sec2"]) . "</td>" .
                                                 "<td class='righttime'>" . formatLapTime($Row["Sec3"]) . "</td>" .
                                                 "<td class='righttime'>" . $LapTime . "</td>" .
                                                 "<td class='center'>" . $InPits . "</td>" .
                                                 "<td>" . $Row["LastUpdate"] . "</td>" .

                           
                          "</tr>\n";
                     
                     $i ++;
                  }
               
               echo "</table>\n";
               echo "</fieldset>\n";
            }else{
               echo "<div class='break12'></div>\n";
               echo "<div class='red'>No records found.</div>\n";
            }
            $ResultLaps->free();
         }
         $mySQLiConn->close();
      }else{
         echo $mySQLiConnError;
      }
      
      include "inc.copyright.php";
   }catch(Exception $ex){
      writeErrorLog($pageName, "General Exception", "Exception Msg: (" . $ex->getMessage() . ")");
   }
?>
</div>
</body>
</html>
