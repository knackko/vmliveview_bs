<?php
   $pageName = "monitordata.php";
   
   include "inc.config.php";
   error_reporting($reportLevel);
   include "inc.common.php";
   include "inc.mysqli.conn.php";
   include "inc.requests.php";
   include "inc.lang.php";
?>
<?php
   try{
      $MonitorData = "";
      $siSessionStart = 0;
      $siSessionLaps = 0;
      $siMaxLaps = 2147483647;
      $siTrackLength = 0;
      $siTopS1 = 0;
      $siTopS2 = 0;
      $siTopLap = 0;
      
      $displayConnections = "0";
      if(isset($currentConnections)){
         $displayConnections = $currentConnections;
      }
      
      $displayServerLoad = "0";
      if(isset($currentServerLoad)){
         $displayServerLoad = $currentServerLoad;
      }
      
      if(!$mySQLiConn->connect_error){
         // -- print session infos
         $selectSessionInfos = "SELECT * FROM `sessioninfos` WHERE `ID` = '1' LIMIT 1";
         if($resultSessionInfos = $mySQLiConn->query($selectSessionInfos)){
            if($resultSessionInfos->num_rows == 1){
               $MonitorData .= "<form name='CurrentScoring' action='' method='get'>\n";
               $MonitorData .= "<fieldset class='sessioninfo'>\n";
               $MonitorData .= "<table class='table'>\n";
               
               while($rowSessionInfos = $resultSessionInfos->fetch_assoc()){
                  // -- set session infos
                  $siLoaderVersion = $rowSessionInfos["LoaderVersion"];
                  $siGameID = $rowSessionInfos["GameID"];
                  $siGameVersion = $rowSessionInfos["GameVersion"];
                  $siPluginVersion = $rowSessionInfos["PluginVersion"];
                  $siServerStart = $rowSessionInfos["ServerStart"];
                  $siServerState = $rowSessionInfos["ServerState"];
                  $siSessionStart = $rowSessionInfos["SessionStart"];
                  $siSessionID = $rowSessionInfos["SessionID"];
                  $siSessionState = $rowSessionInfos["SessionState"];
                  $siSessionTime = $rowSessionInfos["SessionTime"];
                  $siSessionEnd = $rowSessionInfos["SessionEnd"];
                  $siSessionTimeLeft = formatSessionTime($siSessionEnd - $siSessionTime);
                  $siSessionName = setSessionName($siSessionID, $siGameID);
                  $siSessionLaps = $rowSessionInfos["CurLaps"];
                  $siMaxLaps = $rowSessionInfos["MaxLaps"];
                  $siTrackName = $rowSessionInfos["TrackName"];
                  $siTrackLength = $rowSessionInfos["TrackLength"];
                  $siInGameStart = $rowSessionInfos["InGameStart"];
                  $siAmbientTemp = formatTemp($rowSessionInfos["AmbientTemp"]);
                  $siTrackTemp = formatTemp($rowSessionInfos["TrackTemp"]);
                  $siDarkClouds = formatWetness($rowSessionInfos["DarkCloud"]);
                  $siRaining = formatWetness($rowSessionInfos["Raining"]);
                  $siWetOnTrack = formatWetness($rowSessionInfos["OnPathWetness"]);
                  $siTrackState = setTrackState($rowSessionInfos["YellowFlagState"]);
                  
                  // -- set session top lap times
                  $siTopS1 = $rowSessionInfos["TopS1"];
                  if($siTopS1 >= 2147483647){$siTopS1 = 0;}
                  $siTopS2 = $rowSessionInfos["TopS2"];
                  if($siTopS2 >= 2147483647){$siTopS2 = 0;}
                  $siTopLap = $rowSessionInfos["TopLap"];
                  if($siTopLap >= 2147483647){$siTopLap = 0;}
                  
                  // -- set session server connection infos
                  $siConnPrefix = $rowSessionInfos["ConnPrefix"];
                  $siConnAddress = $rowSessionInfos["ConnAddress"];
                  $siConnPort = $rowSessionInfos["ConnPort"];
                  
                  // -- format sector lights
                  $arrSectorFlags[0] = 0;
                  $arrSectorFlags[1] = 0;
                  $arrSectorFlags[2] = 0;
                  $arrSectorFlags = explode("::", $rowSessionInfos["SectorFlags"]);
                  
                  $sectorState1 = " <span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>";
                  $sectorState2 = " <span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>";
                  $sectorState3 = " <span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>";
                  
                  $sectorClearID = 0;
                  if($siGameID == 2){
                     $sectorClearID = 11;
                  }
                  if($arrSectorFlags[1] != $sectorClearID){
                     $sectorState1 = " <span class='slitey'>&bull;</span>&nbsp;<span class='slitey'>&bull;</span>&nbsp;<span class='slitey'>&bull;</span>&nbsp;<span class='slitey'>&bull;</span>&nbsp;<span class='slitey'>&bull;</span>";
                  }
                  
                  if($arrSectorFlags[2] != $sectorClearID){
                     $sectorState2 = " <span class='slitey'>&bull;</span>&nbsp;<span class='slitey'>&bull;</span>&nbsp;<span class='slitey'>&bull;</span>&nbsp;<span class='slitey'>&bull;</span>&nbsp;<span class='slitey'>&bull;</span>";
                  }
                  
                  if($arrSectorFlags[0] != $sectorClearID){
                     $sectorState3 = " <span class='slitey'>&bull;</span>&nbsp;<span class='slitey'>&bull;</span>&nbsp;<span class='slitey'>&bull;</span>&nbsp;<span class='slitey'>&bull;</span>&nbsp;<span class='slitey'>&bull;</span>";
                  }
                  
                  if($siSessionState <= 3 || $siSessionState == 6 || $siSessionState >= 8){
                     $sectorState1 = " <span class='slitey'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>";
                     $sectorState2 = " <span class='slitey'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>";
                     $sectorState3 = " <span class='slitey'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>";
                  }
                  
                  if($siSessionState == 4 || $siSessionState == 7){
                     $sectorState1 = " <span class='sliter'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>";
                     $sectorState2 = " <span class='sliter'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>";
                     $sectorState3 = " <span class='sliter'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>&nbsp;<span class='sliteg'>&bull;</span>";
                  }
                  
                  // -- display session laps (completed / max / remaining)
                  $leftLaps = 0;
                  if($siMaxLaps < 2147483647){
                     $leftLaps = $siMaxLaps - $siSessionLaps;
                     switch($leftLaps){
                        case 1: $leftLaps = "Final Lap"; break;
                        case 0: $leftLaps = "-"; break;
                     }
                  }else{
                     $siMaxLaps = "-";
                     $leftLaps = "-";
                  }
                  
                  $spanInGameTime = "";
                  $inGameTime = 0;
                  
                  if($siSessionStart == "0"){
                     $siSessionName = "<span class='greenbold'>Server Online</span>";
                     switch($siServerState){
                        case 0: $siSessionName = "<span class='redbold'>Server Offline</span>"; break;
                        case 1: $siSessionName = "<span class='greenbold'>Starting up ...</span>"; break;
                        case 2: $siSessionName = "<span class='greenbold'>Changing Session ...</span>"; break;
                        case 3: $siSessionName = "<span class='greenbold'>Changing Session ...</span>"; break;
                     }
                     
                     $spanInGameTime = "";
                     
                     $siSessionEnd = "-";
                     $siSessionTimeLeft = "-";
                     $siMaxLaps = "-";
                     $siSessionLaps = "-";
                     $leftLaps = "-";
                     
                     $siSessionState = "-";
                     $siTrackState = "-";
                     $sectorState1 = " <span class='sliter'>&bull;</span>&nbsp;<span class='sliter'>&bull;</span>&nbsp;<span class='sliter'>&bull;</span>&nbsp;<span class='sliter'>&bull;</span>&nbsp;<span class='sliter'>&bull;</span>";
                     $sectorState2 = " <span class='sliter'>&bull;</span>&nbsp;<span class='sliter'>&bull;</span>&nbsp;<span class='sliter'>&bull;</span>&nbsp;<span class='sliter'>&bull;</span>&nbsp;<span class='sliter'>&bull;</span>";
                     $sectorState3 = " <span class='sliter'>&bull;</span>&nbsp;<span class='sliter'>&bull;</span>&nbsp;<span class='sliter'>&bull;</span>&nbsp;<span class='sliter'>&bull;</span>&nbsp;<span class='sliter'>&bull;</span>";
                     
                     $siAmbientTemp = "-";
                     $siTrackTemp = "-";
                     $siDarkClouds = "-";
                     $siRaining = "-";
                     $siWetOnTrack = "-";
                     $siTrackLength = "-";
                  }
                  
                  if($siInGameStart >= 0 && $siSessionStart != "0"){
                     $inGameTime = $siInGameStart + ($siSessionTime / 60);
                     if($inGameTime >= 1439){
                        $inGameTime -= 1439;
                     }
                     $spanInGameTime = "<span class='gray'> (" . formatInGameTime($inGameTime) . ")</span>";
                  }else{
                     $spanInGameTime = "";
                  }
		  $MonitorData .= "<div><div><h1>" . $siTrackName . "</h1></div><div>  (" . $siTrackLength . "m)</div></div>";
		  $MonitorData .= "<div>".
		  " " . $langAmbientTemp . "</b><b>:</b> " .  $siAmbientTemp . "&deg;C" .
			" " . $langTrackTemp . "</b><b>:</b> " . $siTrackTemp . "&deg;C" .
			" " . $langClouds . "</b><b>:</b> " . $siDarkClouds . "%" .
			" " . $langRain . "</b><b>:</b> " . $siRaining . "%" .
			" " . $langWetOnTrack . "</b><b>:</b> " . $siWetOnTrack . " %" .
			" " . $langWind . "</b></td><b>:</b> - &deg;" .
			"</div>\n";
		  $MonitorData .= "<tr><td class='sessioninfo'><b>" . $langSession . "</b></td><td class='sessioninfo'><b>:</b> " . $siSessionName . "</td>" .
			  "<td class='sessioninfo'><td class='sessioninfo'>(" . $siTrackState . "</td>" .
			  "<td class='sessioninfo'>S1" . $sectorState1 . "" .
			  "S2" . $sectorState2 . "" .
			  "S3" . $sectorState3 . ")</td>" .
			  "</tr>\n";
		  $MonitorData .= "<tr>".
		  "<td class='sessioninfo'><b>" . $langDuration . "</b></td><td class='sessioninfo'><b>:</b> " . formatSessionTime($siSessionEnd) . "</td>" .
		  "<td class='sessioninfo'><b>" . $langTimeLeft . "</b></td><td class='sessioninfo'><b>:</b> " . $siSessionTimeLeft . "</td>" .
		  "</tr>\n";
               }
               $MonitorData .= "</table>\n";
               $MonitorData .= "</fieldset>\n";
               
               $checkedICB = "";
               if(isset($_POST["showICB"]) && $_POST["showICB"] == "1"){$checkedICB = "checked='checked'";}
               $checkedVPOT = "";
               if(isset($_POST["showVPOT"]) && $_POST["showVPOT"] == "1"){$checkedVPOT = "checked='checked'";}
               $checkedSecNum = "";
               if(isset($_POST["showSecNum"]) && $_POST["showSecNum"] == "1"){$checkedSecNum = "checked='checked'";}
               $checkedStops = "";
               if(isset($_POST["showStops"]) && $_POST["showStops"] == "1"){$checkedStops = "checked='checked'";}
               $checkedPenalties = "";
               if(isset($_POST["showPenalties"]) && $_POST["showPenalties"] == "1"){$checkedPenalties = "checked='checked'";}
            }else{
               $MonitorData .= "<div class='break12'></div>\n";
               $MonitorData .= "<div class='red'>No session data found.</div>\n";
            }
            $resultSessionInfos->free();
         }
         
         // -- print current scoring
         if($siSessionStart != "0"){
            $selectSlots = "SELECT * FROM `slots` ORDER BY `Place` ASC";
            if($resultSlots = $mySQLiConn->query($selectSlots)){
               if($resultSlots->num_rows > 0){
                  $MonitorData .= "<div></div>\n";
                  
                  $prevBestLap = 0;
                  
                  $thICB = "";
                  if($checkedICB != ""){$thICB = "<th class='monitor'>". $langICB . "</th>";}
                  
                  $thVPOT = "";
                  if($checkedVPOT != ""){$thVPOT = "<th class='monitor'>". $langVPOT . "</th>";}
                  
                  $thSecNum = "";
                  if($checkedSecNum != ""){$thSecNum = "<th class='monitor'>". $langSecNo . "</th>";}
                  
                  $thPenalties = "";
                  if($checkedPenalties != ""){$thPenalties = "<th class='monitor'>". $langPenalties . "</th>";}
                  
                  $thStops = "<th class='monitor'>". $langStops . "</th>";
                  
                  $MonitorData .= "<fieldset>\n";
                  $MonitorData .= "<div class='table-responsive'><table class='table table-hover table-condensed table-striped'>\n";
                  $MonitorData .= "<tr><th class='monitor'>". $langState . "</th>" .
                                      "<th class='monitor'>". $langPos . "</th>" .
				      "<th class='monitor'>". $langCP . "</th>" .
                                      "<th class='monitor'>". $langDriver . "</th>" .
                                      "<th class='monitor'>". $langClass . "</th>" .
                                      "<th class='monitor'>". $langVehicle . "</th>" .
                                      $thICB .
                                      "<th class='monitor'>". $langLaps . "</th>" .
                                      "<th class='monitor'>". $langGap . "</th>" .
                                      "<th class='monitor'>". $langInterval . "</th>" .
                                      $thVPOT .
                                      $thSecNum .
                                      "<th class='monitor'>". $langSector1 . "</th>" .
                                      "<th class='monitor'>". $langSector2 . "</th>" .
                                      "<th class='monitor'>". $langLastLap . "</th>" .
                                      "<th class='monitor'>". $langBestLap . "</th>" .
                                      $thStops .
                                      $thPenalties .
                                  "</tr>\n";
                  
                  $i = 1;
                  $classPos = "";
                  $cpClass1 = 1;
                  $cpClass2 = 1;
                  $cpClass3 = 1;
                  $cpClass4 = 1;
                  $absBestLap = "";
                  
                  while($rowSlots = $resultSlots->fetch_assoc()){
                     // -- is slot row highlighted
                     $slotSelected = "";
                     if(isset($_POST["SlotID" . $rowSlots["SlotID"]]) && $_POST["SlotID" . $rowSlots["SlotID"]] == "1"){
                        $slotSelected = "checked='checked'";
                     }
                     
                     // -- format driver name
                     $driverName = $rowSlots["DriverName"];
                     if($rowSlots["InPits"] == 0 && $rowSlots["VehicleSpeed"] < 14.1){
                        $driverName = "<span class='slowontrack'>" . $rowSlots["DriverName"] . "</span>";
                     }elseif($rowSlots["InPits"] == 1 && $rowSlots["VehicleSpeed"] > 0.0){
                        $driverName = "<span class='slowinpits'>" . $rowSlots["DriverName"] . "</span>";
                     }elseif($rowSlots["InPits"] == 1){
                        $driverName = "<span class='blue'>" . $rowSlots["DriverName"] . "</span>";
                     }
                     
                     // -- format vehicle classes
                     $vehicleClass = $rowSlots["VehicleClass"];
                     switch($vehicleClass){
                        case $class1RealName:
                           $vehicleClass = "<span style='color:" . $class1Color . ";'>" . $class1DisplayName . "</span>";
                           $classPos = "<span style='color:" . $class1Color . ";'>" . $cpClass1 . "</span>";
                           $classImg = $class1ProgressImg;
                           $cpClass1 ++;
                           break;
                        
                        case $class2RealName:
                           $vehicleClass = "<span style='color:" . $class2Color . ";'>" . $class2DisplayName . "</span>";
                           $classPos = "<span style='color:" . $class2Color . ";'>" . $cpClass2 . "</span>";
                           $classImg = $class2ProgressImg;
                           $cpClass2 ++;
                           break;
                           
                        case $class3RealName:
                           $vehicleClass = "<span style='color:" . $class3Color . ";'>" . $class3DisplayName . "</span>";
                           $classPos = "<span style='color:" . $class3Color . ";'>" . $cpClass3 . "</span>";
                           $classImg = $class3ProgressImg;
                           $cpClass3 ++;
                           break;
                        
                        case $class4RealName:
                           $vehicleClass = "<span style='color:" . $class4Color . ";'>" . $class4DisplayName . "</span>";
                           $classPos = "<span style='color:" . $class4Color . ";'>" . $cpClass4 . "</span>";
                           $classImg = $class4ProgressImg;
                           $cpClass4 ++;
                           break;
                           
                        default:
                           $vehicleClass = "<span class='classUC'>" . $rowSlots["VehicleClass"] . "</span>";
                           $classPos = "<span class='classUC'>" . $i . "</span>";
                           $classImg = "./img/CarUC.png";
                           break;
                     }
                     
                     // -- format session laps
                     $sessionLaps = $rowSlots["Laps"];
                     if($siSessionID == 7 || $siSessionID == 10 || $siSessionID == 11 || $siSessionID == 12 || $siSessionID == 13){
                        $sessionLaps = "<a href='" . $siteURL . "/laps.php?SlotID=" . $rowSlots["SlotID"] . "&amp;Lang=" . $queryLang . "' target='_blank'>" . $rowSlots["Laps"] . "</a>";
                     }else{
                        $sessionLaps = "<a href='" . $siteURL . "/laps.php?DriverName=" . urlencode($rowSlots["DriverName"]) . "&amp;Lang=" . $queryLang . "' target='_blank'>" . $rowSlots["Laps"] . "</a>";
                     }
                     
                     // -- format vehicle in control by
                     $tdICB = "";
                     if($checkedICB != ""){$tdICB = "<td class='center'>" . setVehicleControl($rowSlots["Control"]) . "</td>";}

                     // -- format gap and interval on given values
                     $spanGapLead = "";
                     $spanGapNext = "";
                     
                     if($siSessionID == 7 || $siSessionID == 10 || $siSessionID == 11 || $siSessionID == 12 || $siSessionID == 13){
                        // -- for race sessions
                        if($rowSlots["Place"] == 1){
                           $spanGapLead = "-";
                           $spanGapNext = "-";
                        }else{
                           if($rowSlots["GapTime"] < 0){$spanGapLead = number_format($rowSlots["GapTime"], 3);}else{$spanGapLead = "+" . number_format($rowSlots["GapTime"], 3);}
                           if($rowSlots["GapLaps"] > 0){$spanGapLead =  "<span class='gray'>" . $rowSlots["GapLaps"] . " " . $langLapGap . "</span>";}
   
                           if($rowSlots["IntTime"] < 0){$spanGapNext = "<span class='infight'>+0.000</span>";}else{$spanGapNext = "+" . number_format($rowSlots["IntTime"], 3);}
                           if($rowSlots["IntTime"] > 0 && $rowSlots["IntTime"] < 5){$spanGapNext = "<span class='infight'>+" . number_format($rowSlots["IntTime"], 3) . "</span>";}
                           if($rowSlots["IntLaps"] > 0){$spanGapNext = "<span class='gray'>" . $rowSlots["IntLaps"] . " " . $langLapGap . "</span>";}
                        }
                     }else{
                        // -- for non race sessions
                        if($rowSlots["Place"] == 1){
                           $absBestLap = $rowSlots["BestLap"];
                           $prevBestLap = $rowSlots["BestLap"];
                        }
                        
                        $gapToBestLap = "";
                        $gapToBestNext = "";
                        
                        if($rowSlots["BestLap"] > 0){
                           $gapToBestLap = number_format($rowSlots["BestLap"] - $absBestLap, 3);
                           $gapToBestNext = number_format($rowSlots["BestLap"] - $prevBestLap, 3);
                        }
                        
                        if($gapToBestLap > 0){$spanGapLead = "<span class='rightgap'>+" . $gapToBestLap . "</span>";}else{$spanGapLead = "<span class='rightgap'>-</span>";}
                        if($gapToBestNext > 0){$spanGapNext = "<span class='rightgap'>+" . $gapToBestNext . "</span>";}else{$spanGapNext = "<span class='rightgap'>-</span>";}
                        
                        $prevBestLap = $rowSlots["BestLap"];
                     }
                     
                     // -- format vehicles position on track
                     $tdVPOT = "";
                     if($checkedVPOT != ""){$tdVPOT = "<td class='right'>" . formatLapDist($rowSlots["LapDist"]) . " m</td>";}
                     
                     // -- format sector numbers
                     $curSector = $rowSlots["Sector"];
                     if($curSector == 0){$curSector = 3;}
                     $tdSecNum = "";
                     if($checkedSecNum != ""){$tdSecNum = "<td class='center'>" . $curSector . "</td>";}
                     
                     // -- format current sector times 1 and 2
                     $curS1 = $rowSlots["CurS1"];
                     if($rowSlots["CurS1"] <= 0){$curS1 = "-";}
                     if($rowSlots["CurS1"] > 0 && $rowSlots["CurS1"] <= $rowSlots["BestS1"]){$curS1 = "<span class='green'>" . formatLapTime($rowSlots["CurS1"]) . "</span>";}
                     if($rowSlots["CurS1"] > 0 && $rowSlots["CurS1"] > $rowSlots["BestS1"]){$curS1 = "<span class='red'>" . formatLapTime($rowSlots["CurS1"]) . "</span>";}
                     if($rowSlots["CurS1"] > 0 && $rowSlots["CurS1"] <= $siTopS1){$curS1 = "<span class='toptime'>" . formatLapTime($rowSlots["CurS1"]) . "</span>";}
                     
                     $curS2 = $rowSlots["CurS2"];
                     if($rowSlots["CurS2"] <= 0){$curS2 = "-";}
                     if($rowSlots["CurS2"] > 0 && $rowSlots["CurS2"] <= $rowSlots["BestS2"]){$curS2 = "<span class='green'>" . formatLapTime($rowSlots["CurS2"]) . "</span>";}
                     if($rowSlots["CurS2"] > 0 && $rowSlots["CurS2"] > $rowSlots["BestS2"]){$curS2 = "<span class='red'>" . formatLapTime($rowSlots["CurS2"]) . "</span>";}
                     if($rowSlots["CurS2"] > 0 && $rowSlots["CurS2"] <= $siTopS2){$curS2 = "<span class='toptime'>" . formatLapTime($rowSlots["CurS2"]) . "</span>";}
                     
                     // -- format last lap and display split times
                     $lastLap = formatLapTime($rowSlots["LastLap"]);
                     if($rowSlots["LastLap"] <= 0){$lastLap = "-";}
                     if($rowSlots["CurS1"] > 0 && $siTopS1 > 0){$lastLap = number_format($rowSlots["CurS1"] - $siTopS1, 3);}
                     if($rowSlots["CurS2"] > 0 && $siTopS2 > 0){$lastLap = number_format($rowSlots["CurS2"] - $siTopS2, 3);}
                     if($rowSlots["CurS1"] > 0 && $lastLap > 0){$lastLap = "<span class='red'>+" . $lastLap . "</span>";}
                     if($rowSlots["CurS1"] > 0 && $lastLap < 0){$lastLap = "<span class='green'>" . $lastLap . "</span>";}
                     if($rowSlots["CurS2"] > 0 && $lastLap > 0){$lastLap = "<span class='red'>+" . $lastLap . "</span>";}
                     if($rowSlots["CurS2"] > 0 && $lastLap < 0){$lastLap = "<span class='green'>" . $lastLap . "</span>";}
                     
                     // -- format best lap
                     $bestLap = formatLapTime($rowSlots["BestLap"]);
                     if($rowSlots["BestLap"] <= 0){$bestLap = "-";}
                     if($rowSlots["BestLap"] > 0 && $rowSlots["BestLap"] <= $siTopLap){$bestLap = "<span class='toptime'>" . formatLapTime($rowSlots["BestLap"]) . "</span>";}
                     if($rowSlots["BestLap"] > 0 && $rowSlots["CurS1"] < 0 && $rowSlots["CurS2"] < 0 && $rowSlots["LastLap"] > 0 && $rowSlots["InPits"] == 0 && $rowSlots["FinishStatus"] == 0){
                        $bestLap = $rowSlots["LastLap"] - $siTopLap;
                        if($bestLap == 0){$bestLap = number_format($bestLap, 3);}
                        if($bestLap > 0){$bestLap = "<span class='red'>+" . number_format($bestLap, 3) . "</span>";}
                        if($bestLap < 0){$bestLap = "<span class='green'>" . number_format($bestLap, 3) . "</span>";}
                     }
                     
                     // -- format pit lane
                     $Status = "<span class='indicator indicator-running'>R</span>";
                     if(($rowSlots["InBox"] == 1) && ($rowSlots["FinishStatus"] == 0)){
                        $Status = "<span class='indicator indicator-in-pits'>BOX</span>";
                     }else{
                        if($rowSlots["InPits"] == 1 && ($rowSlots["FinishStatus"] == 0)){
                           $Status = "<span class='indicator indicator-in-pits'>P</span>";
                        }
                     }
                     
                     // -- format total pit stops
                     $totalStops = "-";
                     if($rowSlots["Pitstops"] > 0){$totalStops = "<span>" . $rowSlots["Pitstops"] . "</span>";}
                     $tdStops = "<td class='center'>" . $totalStops . "</td>";
                                          
                     // -- format current penalties to serve
                     $totalPenalties = "-";
                     if($rowSlots["Penalties"] > 0){$totalPenalties = $rowSlots["Penalties"];}
                     $tdPenalties = "";
                     if($checkedPenalties != ""){$tdPenalties = "<td class='center'>" . $totalPenalties . "</td>";}

                     // -- format finish status
                     if($rowSlots["FinishStatus"] != 0){ $Status = setFinishStatus($rowSlots["FinishStatus"]);}
                     
                     $trClass = "'bg0'";
                     if(($i % 2) == 0){$trClass = "'bg1'";}
                     if($slotSelected == "checked='checked'"){$trClass = "'bg2'";}
                     
                     $MonitorData .= "<tr class=".$trClass.">".
							    "<td class='center'>" . $Status . "</td>" .
                                                            "<td class='center'>" . $rowSlots["Place"] . "</td>" .
							    "<td class='center'>" . $classPos . "</td>" .
                                                            "<td>" . $driverName . "</td>" .
                                                            "<td class='center'>" . $vehicleClass . "</td>" .
                                                            "<td>" . $rowSlots["Vehicle"] . "</td>" .
                                                            $tdICB .
                                                            "<td class='center'>" . $sessionLaps . "</td>" .
                                                            "<td class='righttime'>" . $spanGapLead . "</td>" .
                                                            "<td class='righttime'>" . $spanGapNext . "</td>" .
                                                            $tdVPOT .
                                                            $tdSecNum .
                                                            "<td class='righttime'>" . $curS1 . "</td>" .
                                                            "<td class='righttime'>" . $curS2 . "</td>" .
                                                            "<td class='righttime'>" . $lastLap . "</td>" .
                                                            "<td class='righttime'>" . $bestLap . "</td>" .
                                                            $tdStops .
                                                            $tdPenalties .
                                     "</tr>\n";
                     
                     $i ++;
                  }
                  
                  $MonitorData .= "</table>\n";
                  $MonitorData .= "</fieldset>\n";
                  $MonitorData .= "</form>\n";
               }
               $resultSlots->free();
            }
         }else{
            $MonitorData .= "</form>\n";
         }
         
         $mySQLiConn->close();
         
         $MonitorData .= "<div>" .
                         "[" . $siGameVersion . " with " . $siPluginVersion . " and " . $siLoaderVersion . "]" .
                         "[rFactor DS: <a href='" . $siConnPrefix . "://" . $siConnAddress . ":" . $siConnPort . "' target='_blank'>" . $siConnAddress . ":" . $siConnPort . "</a>]" .
                         "[Conn: " . $currentConnections . "]" .
                         "[Data: " . sprintf("%.1F" ,((strlen($MonitorData) * 8) / 1000)) . " kBit]" .
                         "[Load: " . $currentServerLoad . "]" .
                         "</div>\n";
         
         echo $MonitorData;
         
         // if(file_exists("./monitordata.html") == false){
            // $fh = fopen("./monitordata.html", 'wt');
               // fwrite($fh, "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>\n");
               // fwrite($fh, "<html>\n");
               // fwrite($fh, "<head>\n");
               // fwrite($fh, "<meta http-equiv='content-type' content='text/html; charset=UTF-8'>\n");
               // fwrite($fh, "<title>VM LiveView Lite (Monitor)</title>\n");
               // fwrite($fh, "<link rel='stylesheet' type='text/css' href='http://ftgx.dyndns.org/vmliveview/styles.css'>\n");
               // fwrite($fh, "</head>\n");
               // fwrite($fh, "<body>\n");
               // fwrite($fh, $MonitorData);
               // fwrite($fh, "</body>\n");
               // fwrite($fh, "</html>\n");
            // fclose($fh);
         // }
      }else{
         echo $mySQLiConnError;
      }
      
      include "inc.copyright.php";
   }catch(Exception $ex){
      writeErrorLog($pageName, "General Exception", "Exception Msg: (" . $ex->getMessage() . ")");
   }
?>
