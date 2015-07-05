<?php
   $pageName = "hotlaps.php";
   
   include "inc.config.php";
   error_reporting($reportLevel);
   include "inc.common.php";
   include "inc.mysqli.conn.php";
   include "inc.requests.php";
   
   $QueryLang = "en";
   $IncludeLang = "./lang/en.php";
   if(isset($_GET["Lang"])){
      if(strlen($_GET["Lang"]) == 2 && file_exists("./lang/" . $_GET["Lang"] . ".php")){
         $QueryLang = $_GET["Lang"];
         $IncludeLang = "./lang/" . $_GET["Lang"] . ".php";
      }
   }
   include $IncludeLang;
   
   $TrackName = "";
   $PrevTrackNameSelected = "";
   if(isset($_GET["TrackName"])){
      $TrackName = urldecode($_GET["TrackName"]);
      if(validateNameVars($TrackName) === true){
         $PrevTrackNameSelected = urldecode($_GET["TrackName"]);
      }else{
         $TrackName = "";
         writeErrorLog($pageName, "urlGetTrackName", "The value of URL GET parameter [TrackName] did not pass validation. Value: (" . $_GET["TrackName"] . ")");
      }
   }

   if(isset($_POST["TrackName"])){
      $TrackName = $_POST["TrackName"];
      if(validateNameVars($TrackName) === true){
      }else{
         $TrackName = "";
         writeErrorLog($pageName, "postTrackName", "The value of POST parameter [TrackName] did not pass validation. Value: (" . $_POST["TrackName"] . ")");
      }
   }
   
   $VehicleClass = "ALL";
   if(isset($_POST["VehicleClass"])){
      $VehicleClass = $_POST["VehicleClass"];
      if(validateNameVars($VehicleClass) === true){
      }else{
         $VehicleClass = "";
         writeErrorLog($pageName, "postVehicleClass", "The value of POST parameter [VehicleClass] did not pass validation. Value: (" . $_POST["VehicleClass"] . ")");
      }
   }
   
   if($TrackName != $PrevTrackNameSelected){
      $VehicleClass = "ALL";
   }
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html>
<head>
<meta http-equiv='pragma' content='no-cache'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='content-type' content='text/html; charset=<?php echo $siteCharSet; ?>'>
<title>VM LiveView Lite (Hotlaps)</title>
<link rel='stylesheet' type='text/css' href='<?php echo $siteURL; ?>/styles.css'>
</head>
<body>
<div class='center'>
<div class='break4'></div>
<?php
   echo "<div class='sitename'>" . $siteName . "</div>\n";
   try{
      $GameID = 0;
      
      if(!$mySQLiConn->connect_error){
         $SelectGameID = "SELECT SQL_CACHE `GameID` FROM `sessioninfos` WHERE `ID` = '1' LIMIT 1";
         if($ResultGameID = $mySQLiConn->query($SelectGameID)){
            if($ResultGameID->num_rows == 1){
               while($RowGameID = $ResultGameID->fetch_assoc()){
                  $GameID = $RowGameID["GameID"];
               }
            }
            $ResultGameID->free();
         }
         
         $ArrayTrackNames = array();
         $SelectTrackNames = "SELECT DISTINCT SQL_CACHE `TrackName` FROM `hotlaps`";
         if($ResultTrackNames = $mySQLiConn->query($SelectTrackNames)){
            if($ResultTrackNames->num_rows > 0){
               while($RowTrackNames = $ResultTrackNames->fetch_assoc()){
                  $ArrayTrackNames[] = $RowTrackNames["TrackName"];
               }
            }
            $ResultTrackNames->free();
         }
         
         $ArrayClasses = array();
         $SelectClasses = "SELECT DISTINCT SQL_CACHE `VehicleClass` FROM `hotlaps` WHERE `TrackName` = '" . $mySQLiConn->real_escape_string($TrackName) . "' ORDER BY `VehicleClass`";
         if($ResultClasses = $mySQLiConn->query($SelectClasses)){
            if($ResultClasses->num_rows > 0){
               while($RowClasses = $ResultClasses->fetch_assoc()){
                  $ArrayClasses[] = $RowClasses["VehicleClass"];
               }
            }
            $ResultClasses->free();
         }
         
         if($VehicleClass == "ALL"){
            $VehicleClass = "%";
         }
         
         $SelectHotlaps = "SELECT SQL_CACHE * FROM `hotlaps` WHERE `TrackName` = '" . $mySQLiConn->real_escape_string($TrackName) . "' AND `VehicleClass` LIKE '" . $mySQLiConn->real_escape_string($VehicleClass) . "' ORDER BY `TrackName` ASC, `LapTime`, `LastUpdate`";
         
         if($TrackName == "ALL"){
            $SelectHotlaps = "SELECT SQL_CACHE * FROM `hotlaps` ORDER BY `TrackName` ASC, `LapTime`, `LastUpdate`";
         }
         
         if($ResultHotlaps = $mySQLiConn->query($SelectHotlaps)){
            if($ResultHotlaps->num_rows > 0){
               $HotlapRank = 1;
               $PrevTrackName = "";
               $PrevLapTime = 0;
               
               $thTrackName = "";
               if($TrackName == "ALL"){
                  $thTrackName = "<th class='hotlaps'>Track</th>";
               }
               
               echo "<div class='break4'></div>\n";
               
               echo "<form name='Hotlaps' action='" . $siteURL . "/hotlaps.php?TrackName=" . urlencode($TrackName) . "&amp;Lang=" . $QueryLang . "' method='POST'>\n";
               echo "<fieldset>\n";
               echo "<legend>\n";
               echo $langHotlaps . " <select name='TrackName' onchange='document.Hotlaps.submit()'>\n";
               $SelectedTrackName = "";
               if($TrackName == "ALL"){
                  $SelectedTrackName = " selected='selected'";
               }
               echo "<option value='ALL'" . $SelectedTrackName . ">ALL</option>\n";
               foreach($ArrayTrackNames as $valueTrackName){
                  if($valueTrackName == $TrackName){
                     $SelectedTrackName = " selected='selected'";
                  }else{
                     $SelectedTrackName = "";
                  }
                  echo "<option value='" . $valueTrackName . "'" . $SelectedTrackName . ">" . $valueTrackName . "</option>\n";
               }
               echo "</select>\n";
               echo $langClass . " <select name='VehicleClass' onchange='document.Hotlaps.submit()'>\n";
               $SelectedClass = "";
               if($VehicleClass == "ALL"){
                  $SelectedClass = " selected='selected'";
               }
               echo "<option value='ALL'" . $SelectedClass . ">ALL</option>\n";
               foreach($ArrayClasses as $ValueClass){
                  if($ValueClass == $VehicleClass){
                     $SelectedClass = " selected='selected'";
                  }else{
                     $SelectedClass = "";
                  }
                  
                  $DisplayClass = $ValueClass;
                  switch($ValueClass){
                     case $class1RealName:
                        $DisplayClass = $class1DisplayName;
                        break;
                     
                     case $class2RealName:
                        $DisplayClass = $class2DisplayName;
                        break;
                        
                     case $class3RealName:
                        $DisplayClass = $class3DisplayName;
                        break;
                     
                     case $class4RealName:
                        $DisplayClass = $class4DisplayName;
                        break;
                  }
                  
                  echo "<option value='" . $ValueClass . "'" . $SelectedClass . ">" . $DisplayClass . "</option>\n";
               }
               echo "</select>\n";
               echo "</legend>\n";
               echo "<table>\n";
               echo "<tr>" . $thTrackName .
                        "<th class='hotlaps'>" . $langRank . "</th>" .
                        "<th class='hotlaps'>" . $langDriver . "</th>" .
                        "<th class='hotlaps'>" . $langClass . "</th>" .
                        "<th class='hotlaps'>" . $langVehicle . "</th>" .
                        "<th class='hotlaps'>" . $langLaps . "</th>" .
                        "<th class='hotlaps'>" . $langSector1 . "</th>" .
                        "<th class='hotlaps'>" . $langSector2 . "</th>" .
                        "<th class='hotlaps'>" . $langSector3 . "</th>" .
                        "<th class='hotlaps'>" . $langLapTime . "</th>" .
                        "<th class='hotlaps'>" . $langGap . "</th>" .
                        "<th class='hotlaps'>" . $langInterval . "</th>" .
                        "<th class='hotlaps'>" . $langSession . "</th>" .
                        "<th class='hotlaps'>" . $langLastUpdate . "</th>" .
                    "</tr>\n";
                  
                  $PrevClassName = "";
                  while($Row = $ResultHotlaps->fetch_assoc()){
                     $tdTrackName = "";
                     if($TrackName == "ALL"){
                        $tdTrackName = "<td class='center'>" . $Row["TrackName"] . "</td>";
                        
                        if($PrevTrackName != $Row["TrackName"]){
                           $HotlapRank = 1;
                        }
                     }
                     
                     if($PrevClassName != $Row["VehicleClass"]){
                        $PrevClassName = $Row["VehicleClass"];
                        $ClassBestLap = $Row["LapTime"];
                     }
                     
                     $GapToBestLap = 0;
                     if($HotlapRank == 1){
                        $AbsBestLap = $Row["LapTime"];
                        $PrevLapTime = $Row["LapTime"];
                     }
                     
                     $GapToBestLap = $GapToBestLap = number_format($Row["LapTime"] - $AbsBestLap, 3);
                     if($GapToBestLap <= 0){
                        $GapToBestLap = "-";
                     }else{
                        $GapToBestLap = "+" . $GapToBestLap;
                     }
                     
                     $GapToNextLap = number_format($Row["LapTime"] - $PrevLapTime, 3);
                     if($GapToNextLap <= 0){
                     $GapToNextLap = "-";
                     }else{
                        $GapToNextLap = "+" . $GapToNextLap;
                     }
                     
                     $ClassName = $Row["VehicleClass"];
                     switch($ClassName){
                        case $class1RealName:
                           $ClassName = "<span style='color:" . $class1Color . ";'>" . $class1DisplayName . "</span>";
                           $LapTime = "<span style='color:" . $class1Color . ";'>" . formatLapTime($Row["LapTime"]) . "</span>";
                           $GapToNextLap = "<span style='color:" . $class1Color . ";'>" . $GapToNextLap . "</span>";
                           break;
                        
                        case $class2RealName:
                           $ClassName = "<span style='color:" . $class2Color . ";'>" . $class2DisplayName . "</span>";
                           $LapTime = "<span style='color:" . $class2Color . ";'>" . formatLapTime($Row["LapTime"]) . "</span>";
                           $GapToNextLap = "<span style='color:" . $class2Color . ";'>" . $GapToNextLap . "</span>";
                           break;
                           
                        case $class3RealName:
                           $ClassName = "<span style='color:" . $class3Color . ";'>" . $class3DisplayName . "</span>";
                           $LapTime = "<span style='color:" . $class3Color . ";'>" . formatLapTime($Row["LapTime"]) . "</span>";
                           $GapToNextLap = "<span style='color:" . $class3Color . ";'>" . $GapToNextLap . "</span>";
                           break;
                        
                        case $class4RealName:
                           $ClassName = "<span style='color:" . $class4Color . ";'>" . $class4DisplayName . "</span>";
                           $LapTime = "<span style='color:" . $class4Color . ";'>" . formatLapTime($Row["LapTime"]) . "</span>";
                           $GapToNextLap = "<span style='color:" . $class4Color . ";'>" . $GapToNextLap . "</span>";
                           break;
                           
                        default:
                           $ClassName = "<span class='classUC'>" . $Row["VehicleClass"] . "</span>";
                           $LapTime = "<span class='classUC'>" . formatLapTime($Row["LapTime"]) . "</span>";
                           $GapToNextLap = "<span class='classUC'>" . $GapToNextLap . "</span>";
                           break;
                     }
                     
                     $SessionName = setSessionName($Row["SessionID"], $GameID);
                                          
                     $trClass = "'bg0'";
                     if(($HotlapRank % 2) == 0){$trClass = "'bg1'";}
                     
                     if(isset($admitLaps) && $admitLaps > 0){
                        if($Row["TimedLaps"] < $admitLaps){$trClass = "'bg2'";}
                     }
                     
                     if(isset($admitTime) && $admitTime > 0){
                        if(isset($admitTimePerClass) && $admitTimePerClass == 1){
                           if($Row["LapTime"] > $ClassBestLap * $admitTime) {$trClass = "'bg3'";}
                        }else{
                           if($Row["LapTime"] > $AbsBestLap * $admitTime) {$trClass = "'bg3'";}
                        }
                     }
                     
                     echo "<tr class=".$trClass.">" . $tdTrackName .
                              "<td class='center'>" . $HotlapRank . "</td>" .
                              "<td>" . $Row["DriverName"] . "</td>" .
                              "<td class='center'>" . $ClassName . "</td>" .
                              "<td>" . $Row["Vehicle"] . "</td>" .
                              "<td class='center'>" . $Row["TimedLaps"] . "</td>" .
                              "<td class='righttime'>" . formatLapTime($Row["Sec1"]) . "</td>" .
                              "<td class='righttime'>" . formatLapTime($Row["Sec2"]) . "</td>" .
                              "<td class='righttime'>" . formatLapTime($Row["Sec3"]) . "</td>" .
                              "<td class='righttime'>" . $LapTime . "</td>" .
                              "<td class='right'>" . $GapToBestLap . "</td>" .
                              "<td class='right'>" . $GapToNextLap . "</td>" .
                              "<td class='center'>" . $SessionName . "</td>" .
                              "<td>" . $Row["LastUpdate"] . "</td>" .
                          "</tr>\n";
                     
                     $HotlapRank ++;
                     $PrevTrackName = $Row["TrackName"];
                     $PrevLapTime = $Row["LapTime"];
                  }
               
               echo "</table>\n";
               echo "</fieldset>\n";
               echo "<input type='hidden' name='PreviousTrackName' value='" . $PrevTrackNameSelected . "' >\n";
               echo "</form>\n";
            }else{
               echo "<div class='break12'></div>\n";
               echo "<div class='red'>No records found.</div>\n";
            }
            $ResultHotlaps->free();
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
