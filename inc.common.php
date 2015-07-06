<?php
   function writeErrorLog($PageName, $Section, $Message){
      $LogDate = date("Ymd");
      $MsgDate = date("Y-m-d H:i:s");
      $RemoteAddr = "REMOTE_ADDR: ";
      if(isset($_SERVER["REMOTE_ADDR"])){
         $RemoteAddr .= $_SERVER["REMOTE_ADDR"];
      }
      $HttpUserAgent = "HTTP_USER_AGENT: ";
      if(isset($_SERVER["HTTP_USER_AGENT"])){
         $HttpUserAgent .= $_SERVER["HTTP_USER_AGENT"];
      }
      if(file_exists($GLOBALS["logfileFolder"]) === false){
         mkdir($GLOBALS["logfileFolder"], 0600, true);
      }
      error_log("\"" . $MsgDate . "\";\"" . $Section . "\";\"" . $Message . "\";\"" . $RemoteAddr . "\";\"" . $HttpUserAgent . "\"\r\n", 3, $GLOBALS["logfileFolder"] . "/" . $LogDate . "." . $PageName . ".error.log");
   }
   
   function dtmNow(){
      return date("l, d. F Y (G:i:s T)");
   }
   
   function formatLapTime($LapTime){
      if($LapTime > 0){
         return sprintf("%d:%06.3F", floor($LapTime / 60), fmod($LapTime, 60));
      }else{
         return $LapTime;
      }
   }
   
   function formatTemp($Temperatur){
      return number_format($Temperatur, 1, ".", "");
   }
   
   function formatLapDist($LapDist){
      return number_format($LapDist, 1, ".", "");
   }
   
   function formatWetness($Wetness){
      return number_format($Wetness * 100, 1, ".", "");
   }
   
   function formatStartTime($StartTime){
      if($StartTime != 0){
         $arrStartTime = explode("-", $StartTime);
         return $arrStartTime[2] . "." . $arrStartTime[1] . "." . $arrStartTime[0] . " (" . $arrStartTime[3] . ":" .$arrStartTime[4] . ":" .$arrStartTime[5] . ") " . date("T");
      }else{
         return "<span class='redbold'>No Session Data</span>";
      }
   }
   
   function formatUptime($ServerStart, $ServerStatus){
      if($ServerStart != "0" && $ServerStatus != "0"){
         $arrServerStart = explode("-", $ServerStart);
         $ServerUptime = strtotime("now") - strtotime($arrServerStart[2] . "." . $arrServerStart[1] . "." . $arrServerStart[0] . " " . $arrServerStart[3] . ":" .$arrServerStart[4] . ":" .$arrServerStart[5]);
         $upD = floor($ServerUptime / 86400);
         $upH = floor(($ServerUptime - (86400 * $upD)) / 3600);
         $upM = floor(($ServerUptime % 3600) / 60);
         $upS = floor($ServerUptime % 60);
         return sprintf("%02d:%02d:%02d", $upD, $upH, $upM);
      }else{
         return "<span class='redbold'>No Session Data</span>";
      }
   }
   
   function formatInGameTime($InGameTime){
      if($InGameTime > 0){
         $inH = floor($InGameTime / 60);
         $inM = floor($InGameTime % 60);
         $inS = floor(((fmod($InGameTime, 60)) - $inM) * 60);
         return sprintf("%02d:%02d:%02d", $inH, $inM, $inS);
      }else{
         return "00:00:00";
      }
   }
   
   function formatSessionTime($SessionTime){
      if($SessionTime > 0){
         $stH = floor($SessionTime / 3600);
         $stM = floor(($SessionTime % 3600) / 60);
         $stS = floor(((fmod($SessionTime, 3600) / 60) - $stM) * 60);
         return sprintf("%02d:%02d:%02d", $stH, $stM, $stS);
      }else{
         return "00:00:00";
      }
   }
   
   function setSessionName($SessionID, $GameID){
      $SessionName = "Unknown";
      
      if($GameID == 1){
         switch($SessionID){
            case  0: $SessionName = "Test Day"; break;
            case  1: $SessionName = "Practice 1"; break;
            case  2: $SessionName = "Practice 2"; break;
            case  3: $SessionName = "Practice 3"; break;
            case  4: $SessionName = "Practice 4"; break;
            case  5: $SessionName = "Qualifying"; break;
            case  6: $SessionName = "Warmup"; break;
            case  7: $SessionName = "Race"; break;
            default: $SessionName = "Unknown"; break;
         }
      }
      
      if($GameID == 2){
         switch($SessionID){
            case  0: $SessionName = "Test Day"; break;
            case  1: $SessionName = "Practice 1"; break;
            case  2: $SessionName = "Practice 2"; break;
            case  3: $SessionName = "Practice 3"; break;
            case  4: $SessionName = "Practice 4"; break;
            case  5: $SessionName = "Qualifying 1"; break;
            case  6: $SessionName = "Qualifying 2"; break;
            case  7: $SessionName = "Qualifying 3"; break;
            case  8: $SessionName = "Qualifying 4"; break;
            case  9: $SessionName = "Warmup"; break;
            case 10: $SessionName = "Race 1"; break;
            case 11: $SessionName = "Race 2"; break;
            case 12: $SessionName = "Race 3"; break;
            case 13: $SessionName = "Race 4"; break;
            default: $SessionName = "Unknown"; break;
         }
      }
      
      return $SessionName;
   }
   
   function setSessionState($SessionStateID){
      $SessionState = "Unknown";
      switch($SessionStateID){
         case  0: $SessionState = "Before Session"; break;
         case  1: $SessionState = "Recon Lap"; break;
         case  2: $SessionState = "Grid walk-through"; break;
         case  3: $SessionState = "Formation Lap"; break;
         case  4: $SessionState = "Countdown..."; break;
         case  5: $SessionState = "Green Flag"; break;
         case  6: $SessionState = "Full Course Yellow"; break;
         case  7: $SessionState = "Session Stopped"; break;
         case  8: $SessionState = "Finished"; break;
         default: $SessionState = "Unknown"; break;
      }
      return $SessionState;
   }
   
   function setTrackState($TrackStateID){
      $TrackState = "Unknown";
      switch($TrackStateID){
         case -1: $TrackState = "Invalid"; break;
         case  0: $TrackState = "Clear"; break;
         case  1: $TrackState = "Pending..."; break;
         case  2: $TrackState = "Pits closed"; break;
         case  3: $TrackState = "Pit lead lap"; break;
         case  4: $TrackState = "Pits open"; break;
         case  5: $TrackState = "Last Lap"; break;
         case  6: $TrackState = "Resume..."; break;
         case  7: $TrackState = "Race halted"; break;
         default: $TrackState = "Unknown"; break;
      }
      return $TrackState;
   }
   
   function setVehicleControl($VehicleControlID){
      $VehicleControl = "Unknown";
      switch($VehicleControlID){
         case -1: $VehicleControl = "Nobody"; break;
         case  0: $VehicleControl = "Local"; break;
         case  1: $VehicleControl = "AI"; break;
         case  2: $VehicleControl = "Remote"; break;
         case  3: $VehicleControl = "Replay"; break;
         default: $VehicleControl = "Unknown"; break;
      }
      return $VehicleControl;
   }
   
   function setFinishStatus($FinishStatusID){
      $FinishStatus = "Unknown";
      switch($FinishStatusID){
         case -1: $FinishStatus = "<span class='finish-status-out'>DISCO</span>"; break;
         case  0: $FinishStatus = "<span class=''></span>"; break;
         case  1: $FinishStatus = "<span class='finish-status-checkeredflag'>&nbsp;&nbsp;&nbsp;&nbsp;</span>"; break;
         case  2: $FinishStatus = "<span class='finish-status-out'>DNF</span>"; break;
         case  3: $FinishStatus = "<span class='finish-status-out'>DQ</span>"; break;
         default: $FinishStatus = "Unk"; break;
      }
      return $FinishStatus;
   }

   function validateNameVars($StringSubject){
      // -- $StringPattern = "([\"\$\/\*\'\<\>\;])";
      $StringPattern = "([\"\$\/\*\<\>\;])";
      if($StringSubject != ""){
         // -- echo preg_match($StringPattern, trim($StringSubject)); // -- debug only
         if(preg_match($StringPattern, trim($StringSubject)) == 1){
            return false;
         }else{
            return true;
         }
      }else{
         return true;
      }
   }
   
   function sqlGetServerStats($mySQLiConn){
      $ServerStats["GameID"] = 0;
      $ServerStats["ServerState"] = 0;
      $ServerStats["SessionStart"] = 0;
      $ServerStats["SessionID"] = 0;
      $ServerStats["SessionState"] = 0;
      $ServerStats["SessionTime"] = 0;
      $ServerStats["SessionEnd"] = 0;
      $ServerStats["CurLaps"] = 0;
      $ServerStats["MaxLaps"] = 0;
      $ServerStats["NumVehicles"] = 0;
      $ServerStats["TrackName"] = "No Session";
      $ServerStats["AmbientTemp"] = 0;
      $ServerStats["TrackTemp"] = 0;
      if(!$mySQLiConn->connect_error){
         $selectSessionInfos = "SELECT SQL_CACHE * FROM `sessioninfos` LIMIT 1";
         if($resultSessionInfos = $mySQLiConn->query($selectSessionInfos)){
            if($resultSessionInfos->num_rows == 1){
               while($rowSessionInfos = $resultSessionInfos->fetch_assoc()){
                  $ServerStats["GameID"] = $rowSessionInfos["GameID"];
                  $ServerStats["ServerState"] = $rowSessionInfos["ServerState"];
                  $ServerStats["SessionStart"] = $rowSessionInfos["SessionStart"];
                  $ServerStats["SessionID"] = $rowSessionInfos["SessionID"];
                  $ServerStats["SessionState"] = $rowSessionInfos["SessionState"];
                  $ServerStats["SessionTime"] = $rowSessionInfos["SessionTime"];
                  $ServerStats["SessionEnd"] = $rowSessionInfos["SessionEnd"];
                  $ServerStats["CurLaps"] = $rowSessionInfos["CurLaps"];
                  $ServerStats["MaxLaps"] = $rowSessionInfos["MaxLaps"];
                  $ServerStats["NumVehicles"] = $rowSessionInfos["NumVehicles"];
                  $ServerStats["TrackName"] = $rowSessionInfos["TrackName"];
                  $ServerStats["AmbientTemp"] = $rowSessionInfos["AmbientTemp"];
                  $ServerStats["TrackTemp"] = $rowSessionInfos["TrackTemp"];
               }
            }
            $resultSessionInfos->free();
         }else{
            writeErrorLog("inc.common.php", "sqlGetServerStats()", "Error: (" . $mySQLiConn->error . ") Query: (" . $selectSessionInfos . ")");
         }
      }else{
         writeErrorLog("inc.common.php", "sqlGetServerStats()", "Error: (" . $mySQLiConn->connect_error . ")");
      }
      return $ServerStats;
   }
   
   function genPassword($length = 8){
      // -- $dummy = array_merge(range("0", "9"), range("a", "z"), range("A", "Z"), array("#", "&" , "@", "$", "_", "%", "?", "+", "-"));
      $dummy = array_merge(range("0", "9"), range("a", "z"), range("A", "Z"));
      
      // -- shuffle array
      mt_srand((double)microtime() * 1000000);
      for($i = 1; $i <= (count($dummy) * 2); $i++){
         $swap = mt_rand(0, count($dummy) - 1);
         $tmp = $dummy[$swap];
         $dummy[$swap] = $dummy[0];
         $dummy[0] = $tmp;
      }
      
      // -- get password
      return substr(implode("", $dummy), 0, $length);
   }
   
   function mapRotate90($RotationAngle){
      if($RotationAngle < -360.0){$RotationAngle = -360.0;}
      if($RotationAngle > 360.0){$RotationAngle = 360.0;}
      if($RotationAngle < 0.0){$RotationAngle *= -1;}
      if($RotationAngle > 90.0 && $RotationAngle <= 180.0){$RotationAngle = 90 - ($RotationAngle - 90);}
      if($RotationAngle > 180.0 && $RotationAngle <= 270.0){$RotationAngle = ($RotationAngle - 180);}
      if($RotationAngle > 270.0 && $RotationAngle <= 360.0){$RotationAngle = 90 - ($RotationAngle - 270);}
      return $RotationAngle;
   }
?>
