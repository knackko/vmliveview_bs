<?php
   $pageName = "trackdata.php";
   
   include "inc.config.php";
   error_reporting($reportLevel);
   include "inc.common.php";
   include "inc.mysqli.conn.php";
   include "inc.requests.php";
   include "inc.lang.php";
   
   try{
      $TrackData = "";
      
      $screenWidth = 1920;
      if(isset($_POST["ScreenWidth"]) && is_numeric($_POST["ScreenWidth"])){
         $screenWidth = $_POST["ScreenWidth"];
      }
      
      $screenHeight = 1080;
      if(isset($_POST["ScreenHeight"]) && is_numeric($_POST["ScreenHeight"])){
         $screenHeight = $_POST["ScreenHeight"];
      }
      
      $innerWidth = 1920;
      if(isset($_POST["InnerWidth"]) && is_numeric($_POST["InnerWidth"])){
         $innerWidth = $_POST["InnerWidth"];
      }
      
      $innerHeight = 924;
      if(isset($_POST["InnerHeight"]) && is_numeric($_POST["InnerHeight"])){
         $innerHeight = $_POST["InnerHeight"];
      }
      
      $postMapMinLeft = 0;
      if(isset($_POST["MapMinLeft"]) && is_numeric($_POST["MapMinLeft"])){
         $postMapMinLeft = $_POST["MapMinLeft"];
      }
      
      $postMapMinTop = 0;
      if(isset($_POST["MapMinTop"]) && is_numeric($_POST["MapMinTop"])){
         $postMapMinTop = $_POST["MapMinTop"];
      }
      
      $i = 1;
      
      $drawWidth = 0;
      $drawHeight = 0;
      
      $zIndex = 210;
      $zIndexSub = 0;
      
      $trackName = "";
      $trackLength = "";
      $trackLayout = "";
      $slotPositions = "";
      
      if($innerWidth > 0 && $innerHeight > 0){
         $mapOriginX = $innerWidth / 2;
         $mapOriginY = $innerHeight / 2;
         
         // -- echo "Using inner values ...";
         
         $drawWidth = $innerWidth;
         $drawHeight = $innerHeight;
      }else{
         $mapOriginX = $screenWidth / 2;
         $mapOriginY = $screenHeight / 2;
         
         // -- echo "Using screen values ...";
         
         $drawWidth = $screenWidth;
         $drawHeight = $screenHeight;
      }
      
      if(!$mySQLiConn->connect_error){
         // -- get session infos
         $selectSessionInfos = "SELECT SQL_CACHE * FROM `sessioninfos` WHERE `ID` = '1' LIMIT 1";
         if($resultSessionInfos = $mySQLiConn->query($selectSessionInfos)){
            if($resultSessionInfos->num_rows == 1){
               while($rowSessionInfos = $resultSessionInfos->fetch_assoc()){
                  $trackName = $rowSessionInfos["TrackName"];
                  $trackLength = $rowSessionInfos["TrackLength"];
               }
            }
            $resultSessionInfos->free();
         }
         
         // -- get, set and calc track config
         $mapScale = 0.75;
         $mapRotate = 0.01;
         $mapMirrorX = -1;
         $mapMinLeft = 2147483647;
         $mapMinTop = 2147483647;
         $mapOriginXOffset = 0;
         $mapOriginYOffset = 0;
         $trackMinLeft = -1.0;
         $trackMaxLeft = 1.0;
         $trackMinTop = -1.0;
         $trackMaxTop = 1.0;
         
         $selectTrackConfig = "SELECT SQL_CACHE * FROM `trackconfig` WHERE `trackname` = '" . $trackName . "' LIMIT 1";
         if($resultTrackConfig = $mySQLiConn->query($selectTrackConfig)){
            if($resultTrackConfig->num_rows == 1){
               while($rowTrackConfig = $resultTrackConfig->fetch_assoc()){
                  $mapScale = $rowTrackConfig["MapScale"];
                  $mapRotate = $rowTrackConfig["MapRotation"];
                  $mapMirrorX = $rowTrackConfig["MapMirrorX"];
                  $trackMinLeft = $rowTrackConfig["TrackPosXMin"];
                  $trackMaxLeft = $rowTrackConfig["TrackPosXMax"];
                  $trackMinTop = $rowTrackConfig["TrackPosYMin"];
                  $trackMaxTop = $rowTrackConfig["TrackPosYMax"];
               }
            }
            $resultTrackConfig->free();
         }
         
         // -- comment before flight
         if($reportLevel == -1){
            // -- $mapMirrorX = -1;
            // -- $mapRotate = -57.5;
         }
         
         // -- validate configuration on map rotation
         if($mapRotate == 0){$mapRotate = 0.01;}
         if($mapRotate < -360.0){$mapRotate = -360.0;}
         if($mapRotate > 360.0){$mapRotate = 360.0;}
         
         // -- validate configuration on map mirror x
         if($mapMirrorX == 1 || $mapMirrorX == -1){
            // -- configuration ok - do nothing
         }else{
            $mapMirrorX == -1;
         }
         
         $mapRotation90 = mapRotate90($mapRotate);
         
         $trackExtentLeft = $trackMinLeft * -1 + $trackMaxLeft;
         $trackExtentTop = $trackMinTop * -1 + $trackMaxTop;
         
         $trackExtentGap = $trackExtentLeft - $trackExtentTop;
         if($trackExtentGap < 0.0){$trackExtentGap *= -1;}
         
         if($reportLevel == -1){
            echo "<div style='margin-left:5px'>\n";
            echo "<div>Gap at 90&deg;: " . $trackExtentGap . "</div>";
            echo "<div>Gap at " . $mapRotation90 . "&deg;: " . (($trackExtentGap / 90) * $mapRotation90) . "</div>";
            
            echo "<div>Max Extent Left at 0&deg;: " . $trackExtentLeft . "</div>";
            echo "<div>Max Extent Left at " . $mapRotation90 . "&deg;: " . ($trackExtentLeft + (($trackExtentGap / 90) * $mapRotation90)) . "</div>";
            
            echo "<div>Max Extent Top at 0&deg;: " . $trackExtentTop . "</div>";
            echo "<div>Max Extent Top at " . $mapRotation90 . "&deg;: " . ($trackExtentTop - (($trackExtentGap / 90) * $mapRotation90)) . "</div>";
            echo "<div class='break6'></div>\n";
            echo "</div>\n";
         }
         
         $trackExtentLeft = ($trackExtentLeft + (($trackExtentGap / 90) * $mapRotation90));
         $trackExtentTop = ($trackExtentTop - (($trackExtentGap / 90) * $mapRotation90));
         
         $trackScaleLeft = ((100 / $trackExtentLeft) * ($drawWidth * 0.75) / 100);
         $trackScaleTop = ((100 / $trackExtentTop) * ($drawHeight * 0.75) / 100);
         
         $mapScale = $trackScaleLeft;
         if($trackScaleTop < $trackScaleLeft){
            $mapScale = $trackScaleTop;
         }
         
         if($mapScale > 1.0){$mapScale = 1.0;}
         
         if($postMapMinLeft != 0 && $postMapMinTop != 0){
            $mapOriginXOffset = $postMapMinLeft - 300;
            $mapOriginYOffset = $postMapMinTop - 75;
         }
         
         $mapOriginX = ($mapOriginX - $mapOriginXOffset) * $mapMirrorX;
         $mapOriginY = $mapOriginY - $mapOriginYOffset;
         
         echo "<span style='position:absolute; left:" . $mapOriginX . "px; top:" . $mapOriginY . "px; color:red;' title='origin'>+</span>\n";
         
         $trackModulo = 2;
         if($mapScale <= 0.75){$trackModulo = 4;}
         if($mapScale <= 0.5){$trackModulo = 6;}
         if($mapScale <= 0.25){$trackModulo = 8;}
         if($mapScale <= 0.1){$trackModulo = 10;}
         
         // -- get and process track data
         $selectTrackData = "SELECT SQL_CACHE * FROM `trackdata` WHERE `trackname` = '" . $trackName . "' AND BranchID = '0' ORDER BY `LapDist` ASC";
         if($resultTrackData = $mySQLiConn->query($selectTrackData)){
            if($resultTrackData->num_rows > 0){
               while($rowTrackData = $resultTrackData->fetch_assoc()){
                  if(($i % $trackModulo) == 0){
                     $trackPosX = (($mapOriginX + (($rowTrackData["PosX"] * cos(deg2rad($mapRotate)) - $rowTrackData["PosY"] * sin(deg2rad($mapRotate))) * $mapScale)) * $mapMirrorX);
                     $trackPosY = (($mapOriginY + (($rowTrackData["PosX"] * sin(deg2rad($mapRotate)) + $rowTrackData["PosY"] * cos(deg2rad($mapRotate))) * $mapScale)));
                     
                     $sectorColor = "black";
                     if($rowTrackData["Sector"] == 0){$sectorColor = "green";}
                     if($rowTrackData["Sector"] == 1){$sectorColor = "blue";}
                     if($rowTrackData["Sector"] == 2){$sectorColor = "red";}
                     
                     $trackLayout .= "<span style='position:absolute; left:" . $trackPosX . "px; top:" . $trackPosY . "px; font-size:12px; color:" . $sectorColor . ";'>&bull;</span>\n";
                     
                     if($trackPosX < $mapMinLeft){$mapMinLeft = $trackPosX;}
                     if($trackPosY < $mapMinTop){$mapMinTop = $trackPosY;}
                  }
                  
                  $i ++;
               }
            }
            $resultTrackData->free();
         }

         if($reportLevel == -1){
            echo "<div style='margin-left:5px'>\n";
            echo "<div>Map min Left: " . $mapMinLeft . "</div>";
            echo "<div>Map min Top: " . $mapMinTop . "</div>";
            echo "<div class='break6'></div>\n";
            echo "</div>\n";
         }
                  
         if($postMapMinLeft != 0){
            $mapMinLeft = $postMapMinLeft;
         }
         
         if($postMapMinTop != 0){
            $mapMinTop = $postMapMinTop;
         }
         
         echo "<input type='hidden' name='MapMinLeft' value='" . $mapMinLeft . "'>\n";
         echo "<input type='hidden' name='MapMinTop' value='" . $mapMinTop . "'>\n";
         
         echo $trackLayout;
         
         // -- get and process slot data
         $selectSlotsPos = "SELECT SQL_CACHE * FROM `slots` ORDER BY `Place` ASC";
         if($resultSlotsPos = $mySQLiConn->query($selectSlotsPos)){
            if($resultSlotsPos->num_rows > 0){
               $i = 1;
               $vehicleClass = "";
               $cpClass1 = 1;
               $cpClass2 = 1;
               $cpClass3 = 1;
               $cpClass4 = 1;
               
               echo "<div style='margin-left:5px'>\n";
               echo "<fieldset>\n";
               echo "<legend>" . $langStandings . "</legend>\n";
               echo "<table>\n";
               echo "<tr><th class='black'>" . $langPos . "</th>" .
                        "<th class='black'>" . $langCP . "</th>" .
                        "<th class='black'>" . $langDriver . "</th>" .
                    "</tr>\n";
               
                  while($rowSlotPos = $resultSlotsPos->fetch_assoc()){
                     $slotDriverName = $rowSlotPos["DriverName"];
                     
                     $slotPosXCorr = 6;
                     if($rowSlotPos["Place"] < 10){
                        $slotPosXCorr = 10;
                     }
                     
                     $vehicleClass = $rowSlotPos["VehicleClass"];
                     switch($vehicleClass){
                        case $class1RealName:
                           $vehicleClass = $class1DisplayName;
                           $classPos = "<span style='color:" . $class1Color . ";'>" . $cpClass1 . "</span>";
                           $classImg = $class1ProgressImg;
                           $cpClass1 ++;
                           break;
                        
                        case $class2RealName:
                           $vehicleClass = $class2DisplayName;
                           $classPos = "<span style='color:" . $class2Color . ";'>" . $cpClass2 . "</span>";
                           $classImg = $class2ProgressImg;
                           $cpClass2 ++;
                           break;
                           
                        case $class3RealName:
                           $vehicleClass = $class3DisplayName;
                           $classPos = "<span style='color:" . $class3Color . ";'>" . $cpClass3 . "</span>";
                           $classImg = $class3ProgressImg;
                           $cpClass3 ++;
                           break;
                        
                        case $class4RealName:
                           $vehicleClass = $class4DisplayName;
                           $classPos = "<span style='color:" . $class4Color . ";'>" . $cpClass4 . "</span>";
                           $classImg = $class4ProgressImg;
                           $cpClass4 ++;
                           break;
                           
                        default:
                           $vehicleClass = $rowSlotPos["VehicleClass"];
                           $classPos = "<span class='classUC'>" . $i . "</span>";
                           $classImg = "./img/CarUC.png";
                           break;
                     }
                     
                     if($rowSlotPos["InPits"] == 1){
                        $slotDriverName = "<span class='blue'>" . $rowSlotPos["DriverName"] . "</span>";
                        $classImg = "./img/CarInPits.png";
                        $zIndexSub = 105;
                     }
                     
                     if($rowSlotPos["FinishStatus"] != 0){
                        $slotDriverName = "<span class='red'>" . $rowSlotPos["DriverName"] . "</span>";
                        $classImg = "./img/CarIsOut.png";
                        $zIndexSub = 105;
                     }
                     
                     if($rowSlotPos["FinishStatus"] == 1){
                        $slotDriverName = "<span class='finished'>" . $rowSlotPos["DriverName"] . "</span>";
                        $classImg = "./img/CarIsOut.png";
                        $zIndexSub = 105;
                     }
                     
                     $trClass = "'bg0'";
                     if(($i % 2) == 0){$trClass = "'bg1'";}
                     
                     echo "<tr class=".$trClass."><td class='center'>" . $rowSlotPos["Place"] . "</td>" .
                                                 "<td class='center'>" . $classPos . "</td>" .
                                                 "<td>" . $slotDriverName . "</td>" .
                          "</tr>\n";
                     
                     $slotPosX = (($mapOriginX + (($rowSlotPos["PosX"] * cos(deg2rad($mapRotate)) - $rowSlotPos["PosY"] * sin(deg2rad($mapRotate))) * $mapScale)) * $mapMirrorX);
                     $slotPosY = (($mapOriginY + (($rowSlotPos["PosX"] * sin(deg2rad($mapRotate)) + $rowSlotPos["PosY"] * cos(deg2rad($mapRotate))) * $mapScale)));
                     
                     $slotPositions .= "<span style='position:absolute; left:" . $slotPosX . "px; top:" . $slotPosY . "px; z-index:" . ($zIndex - $zIndexSub) . "; color:white;'>" . $rowSlotPos["Place"] . "</span>\n";
                     $zIndex -= 1;
                     $slotPositions .= "<img src='" . $classImg . "' alt='ts' style='position:absolute; left:" . ($slotPosX - $slotPosXCorr) . "px; top:" . ($slotPosY - 3) . "px; z-index:". ($zIndex - $zIndexSub) .";'>\n";
                     $zIndex -= 1;
                     
                     $zIndexSub = 0;
                     
                     $i ++;
                  }
               
               echo "</table>\n";
               echo "</fieldset>\n";
               echo "</div>\n";
               
               echo $slotPositions;
               
               echo "<div style='margin-left:5px'>\n";
               echo "<div class='break6'></div>\n";
               echo "<input type='button' value='" . $langTrackRefit . "' onClick='window.location.reload();'></div>\n";
               echo "<div class='break6'></div>\n";
               echo "</div>\n";
               
               if($reportLevel == -1){
                  echo "<div style='margin-left:5px'>\n";
                  echo "<div class='break6'></div>\n";
                  
                  echo "<div>Track Name: " . $trackName . "</div>\n";
                  echo "<div>Track Length: " . number_format($trackLength, 1, ".", "") . "</div>\n";
                  
                  echo "<div class='break6'></div>\n";
                  
                  echo "<div>Screen Width: " . $screenWidth . " px</div>\n";
                  echo "<div>Screen Height: " . $screenHeight . " px</div>\n";
                  
                  echo "<div>Inner Width: " . $innerWidth . " px</div>\n";
                  echo "<div>Inner Height: " . $innerHeight . " px</div>\n";
                  
                  echo "<div>Drawing Width: " . $drawWidth . " px</div>\n";
                  echo "<div>Drawing Height: " . $drawHeight . " px</div>\n";
                  
                  echo "<div>Origin Left: " . $mapOriginX . " px</div>\n";
                  echo "<div>Origin Top: " . $mapOriginY . " px</div>\n";
                  
                  echo "<div>Map Scale Left: " . $trackScaleLeft . "</div>\n";
                  echo "<div>Map Scale Top: " . $trackScaleTop . "</div>\n";
                  echo "<div>Map Scale: " . $mapScale . "</div>\n";
                  
                  echo "<div>Map Rotation: " . $mapRotate . " &deg;</div>\n";
                  if($mapMirrorX == -1){
                     echo "<div>Map Mirror X: True</div>\n";
                  }else{
                     echo "<div>Map Mirror X: False</div>\n";
                  }
                  echo "<div>Map Min. Left: " . $mapMinLeft . " px</div>\n";
                  echo "<div>Map Min. Top: " . $mapMinTop . " px</div>\n";
                  echo "</div>\n";
               }
            }else{
               echo "<div style='margin-left:5px'>\n";
               echo "<div class='break12'></div>\n";
               echo "<div class='red'>No drivers on track.</div>\n";
               echo "</div>\n";
            }
            $resultSlotsPos->free();
         }
         $mySQLiConn->close();
      }else{
         echo $mySQLiConnError;
      }
   }catch(Exception $ex){
      writeErrorLog($pageName, "General Exception", "Exception Msg: (" . $ex->getMessage() . ")");
   }
?>
