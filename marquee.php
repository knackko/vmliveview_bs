<?php
   $pageName = "marquee.php";
   
   include "inc.config.php";
   error_reporting($reportLevel);
   include "inc.common.php";
   include "inc.mysqli.conn.php";
   include "inc.requests.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="content-type" content="text/html; charset=<?php echo $siteCharSet; ?>">
<title>VM LiveView Lite (Marquee)</title>
<style type="text/css">
   * { margin: 0; padding: 0; font-family: Verdana, Arial, Helvetica, sans-serif; white-space: nowrap; }
   div { font-size: 12px; color: #F8F8FF; }
   td { font-size: 16px; color: #F8F8FF; }
   td.DriverName { padding-bottom: 2px; border-bottom: 2px solid #FF8C00; }
   td.RowSpan2 { height: 46px; }
   td.NoData { color: #F00; font-weight: bold; height: 40px; }
   span { font-size: 16px; color: #F8F8FF; }
   span.Class1 { color: #F0F; font-weight: 900; }
   span.Class2 { color: #F0F; font-weight: 900; }
   span.Class3 { color: #F0F; font-weight: 900; }
   span.Class4 { color: #F0F; font-weight: 900; }
   span.Lead { font-weight: bold; }
   span.Next { font-weight: bold; }
   span.Best { font-weight: bold; }
   span.Gap { font-weight: bold; }
   span.InPits { color: #1E90FF; font-weight: bold; }
   span.DNF { color: #F08080; font-weight: bold; }
   span.Pos { font-weight: bold; }
   span.Laps { font-weight: bold; }
   span.Copyright { margin-left: 10px; font-weight: bold; }
   span.SessionType { font-weight: bold; }
   span.Blank5 { margin-left: 5px; }
   span.Blank10 { margin-left: 10px; }
</style>
</head>
<body>
<?php
   $siGameID = 0;
   $siSessionStart = "0";
   $siSessionName = "0";
   $siSessionState = "0";
   $siTrackName = "";
   
   $showClass = "ALL";
   $classToShow = $showClass;
   if(isset($_GET["showClass"])){
      if($_GET["showClass"] == "ALL" OR $_GET["showClass"] == "Class1" OR $_GET["showClass"] == "Class2" OR $_GET["showClass"] == "Class3" OR $_GET["showClass"] == "Class4"){
         $showClass = $_GET["showClass"];
      }
   }
   
   $MarqueeData = '<table border="0" cellpadding="1" cellspacing="0">';
   
   $tr1 = '';
   $tr2 = '';
   
   $cpClass1 = 1;
   $cpClass2 = 1;
   $cpClass3 = 1;
   $cpClass4 = 1;
   
   $bestLap = 0;
   $gapBest = 0;
   
   if(!$mySQLiConn->connect_error){
      $selectSessionInfos = "SELECT SQL_CACHE * FROM `sessioninfos` WHERE `ID` = '1' LIMIT 1";
      if($resultSessionInfos = $mySQLiConn->query($selectSessionInfos)){
         if($resultSessionInfos->num_rows == 1){
            while($rowSessionInfos = $resultSessionInfos->fetch_assoc()){
               $siGameID = $rowSessionInfos["GameID"];
               $siSessionStart = $rowSessionInfos["SessionStart"];
               $siTrackName = $mySQLiConn->real_escape_string($rowSessionInfos["TrackName"]);
               $siSessionName = setSessionName($rowSessionInfos["SessionID"], $siGameID);
               $siSessionState = setSessionState($rowSessionInfos["SessionState"], $siGameID);
            }
         }
         $resultSessionInfos->free();
      }else{
         writeErrorLog($pageName, "selectSessionInfos", "SQL ERROR: (" . $mySQLiConn->error . ") QUERY: (" . $selectSessionInfos . ")");
      }
      
      if($siSessionStart == "0"){
         $MarqueeData = '<table><tr><td class="NoData">No Session Data</td></tr></table>';
      }else{
         $selectSlots = "SELECT SQL_CACHE * FROM `slots` ORDER BY `Place` ASC";
         if($resultSlots = $mySQLiConn->query($selectSlots)){
            if($resultSlots->num_rows > 0){
               while($rowSlots = $resultSlots->fetch_assoc()){
                  $slotPos = $rowSlots["Place"];
                  $slotDriverName = $mySQLiConn->real_escape_string($rowSlots["DriverName"]);
                  $slotVehicle = $mySQLiConn->real_escape_string($rowSlots["Vehicle"]);
                  $slotVehicleClass = $mySQLiConn->real_escape_string($rowSlots["VehicleClass"]);
                  $slotLaps = $rowSlots["Laps"];
                  $slotGapTime = $rowSlots["GapTime"];
                  $slotGapLaps = $rowSlots["GapLaps"];
                  $slotIntTime = $rowSlots["IntTime"];
                  $slotIntLaps = $rowSlots["IntLaps"];
                  $slotBestLap = $rowSlots["BestLap"];
                  $slotInPits = $rowSlots["InPits"];
                  $slotFinishStatus = $rowSlots["FinishStatus"];
                  
                  if($slotPos == "1"){
                     $bestLap = $slotBestLap;
                  }
                  
                  $ClassImg = '<img src="./img/ClassUC.png" alt="UC">';
                  $ClassPos = '';
                  
                  switch($slotVehicleClass){
                     case $class1RealName:
                        $ClassImg = '<img src="' . $class1MarqueeImg . '" alt="' . $class1DisplayName . '">';
                        $ClassPos = ' (<span class="Class1">' . $cpClass1 . '.</span>)';
                        $cpClass1 += 1;
                        break;
                        
                     case $class2RealName:
                        $ClassImg = '<img src="' . $class2MarqueeImg . '" alt="' . $class2DisplayName . '">';
                        $ClassPos = ' (<span class="Class2">' . $cpClass2 . '.</span>)';
                        $cpClass2 += 1;
                        break;
                        
                     case $class3RealName:
                        $ClassImg = '<img src="' . $class3MarqueeImg . '" alt="' . $class3DisplayName . '">';
                        $ClassPos = ' (<span class="Class3">' . $cpClass3 . '.</span>)';
                        $cpClass3 += 1;
                        break;
                        
                     case $class4RealName:
                        $ClassImg = '<img src="' . $class4MarqueeImg . '" alt="' . $class4DisplayName . '">';
                        $ClassPos = ' (<span class="Class4">' . $cpClass4 . '.</span>)';
                        $cpClass4 += 1;
                        break;
                  }
                  
                  $Gap = ' (<span class="Lead">Lead:</span> \+' . $slotGapTime . ')';
                  if($slotGapTime == 0){$Gap = ' (<span class="Lead">Lead:</span> \+' . $slotGapLaps . ' L)';}
                  
                  $Interval = ' (<span class="Next">Next:</span> \+' . $slotIntTime . ')';
                  if($slotIntTime == 0){$Interval = ' (<span class="Next">Next:</span> \+' . $slotIntLaps . ' L)';}
                  
                  if($siSessionName != "Race"){
                     $Gap = ' (<span class="Best">Best:</span> ' . formatLapTime($slotBestLap) . ')';
                     if($slotBestLap > 0){
                        $gapBest = number_format($slotBestLap - $bestLap, 3);
                     }else{
                        $Gap = ' (<span class="Best">Best:</span> No Time)';
                        $gapBest = '0.000';
                     }
                     $Interval = ' (<span class="Gap">Gap:</span> \+' . $gapBest . ')';
                  }
                  
                  $inPits = '';
                  if($slotInPits == 1){$inPits = ' (<span class="InPits">PIT</span>)';}
                  
                  $finishStatus = '';
                  if($slotFinishStatus == 2){$finishStatus = ' (<span class="DNF">DNF</span>)';}
                  
                  // -- exclude the organisation class e.g. officals vehicle or live stream vehicle aso.
                  if($slotVehicleClass != "ES_TV"){
                     switch($showClass){
                        case "Class1":
                           if($slotVehicleClass == $class1RealName){
                              $tr1 .= '<td rowspan="2" class="RowSpan2">' . $ClassImg . '<span class="Blank5"></span></td><td class="DriverName"><span class="Pos">' . $slotPos . ".</span> " . $slotDriverName . $Gap . $ClassPos  . '</td><td rowspan="2" class="RowSpan2"><span class="Blank5"></span>' . $Interval . '<span class="Blank10"></span></td>';
                              $tr2 .= '<td>' . $slotVehicle . ' (<span class="Laps">Laps:</span> ' . $slotLaps . ') ' . $inPits . $finishStatus . '</td>';
                           }
                           $classToShow = $marqueeClassSequence2;
                           break;
                        
                        case "Class2":
                           if($slotVehicleClass == $class2RealName){
                              $tr1 .= '<td rowspan="2" class="RowSpan2">' . $ClassImg . '<span class="Blank5"></span></td><td class="DriverName"><span class="Pos">' . $slotPos . ".</span> " . $slotDriverName . $Gap . $ClassPos  . '</td><td rowspan="2" class="RowSpan2"><span class="Blank5"></span>' . $Interval . '<span class="Blank10"></span></td>';
                              $tr2 .= '<td>' . $slotVehicle . ' (<span class="Laps">Laps:</span> ' . $slotLaps . ') ' . $inPits . $finishStatus . '</td>';
                           }
                           $classToShow = $marqueeClassSequence3;
                           break;
                        
                        case "Class3":
                           if($slotVehicleClass == $class3RealName){
                              $tr1 .= '<td rowspan="2" class="RowSpan2">' . $ClassImg . '<span class="Blank5"></span></td><td class="DriverName"><span class="Pos">' . $slotPos . ".</span> " . $slotDriverName . $Gap . $ClassPos  . '</td><td rowspan="2" class="RowSpan2"><span class="Blank5"></span>' . $Interval . '<span class="Blank10"></span></td>';
                              $tr2 .= '<td>' . $slotVehicle . ' (<span class="Laps">Laps:</span> ' . $slotLaps . ') ' . $inPits . $finishStatus . '</td>';
                           }
                           $classToShow = $marqueeClassSequence4;
                           break;
                        
                        case "Class4":
                           if($slotVehicleClass == $class4RealName){
                              $tr1 .= '<td rowspan="2" class="RowSpan2">' . $ClassImg . '<span class="Blank5"></span></td><td class="DriverName"><span class="Pos">' . $slotPos . ".</span> " . $slotDriverName . $Gap . $ClassPos  . '</td><td rowspan="2" class="RowSpan2"><span class="Blank5"></span>' . $Interval . '<span class="Blank10"></span></td>';
                              $tr2 .= '<td>' . $slotVehicle . ' (<span class="Laps">Laps:</span> ' . $slotLaps . ') ' . $inPits . $finishStatus . '</td>';
                           }
                           $classToShow = $marqueeClassSequenceDefault;
                           break;
                        
                        default:
                           $tr1 .= '<td rowspan="2" class="RowSpan2">' . $ClassImg . '<span class="Blank5"></span></td><td class="DriverName"><span class="Pos">' . $slotPos . ".</span> " . $slotDriverName . $Gap . $ClassPos  . '</td><td rowspan="2" class="RowSpan2"><span class="Blank5"></span>' . $Interval . '<span class="Blank10"></span></td>';
                           $tr2 .= '<td>' . $slotVehicle . ' (<span class="Laps">Laps:</span> ' . $slotLaps . ') ' . $inPits . $finishStatus . '</td>';
                           $classToShow = $marqueeClassSequence1;
                           break;
                     }
                  }
               }
            }
            $resultSlots->free();
         }else{
            writeErrorLog($pageName, "selectSlots", "SQL ERROR: (" . $mySQLiConn->error . ") QUERY: (" . $selectSlots . ")");
         }
         
         $MarqueeData .= '<tr><td rowspan="2" class="RowSpan2"><span class="SessionType">' . $siSessionName . '</span> (' . $siSessionState  . ')<span class="Blank10"></span></td>' . $tr1 . '<td rowspan="2" class="RowSpan2"><span class="Copyright">&copy; VorteX-Motorsports</span></td></tr><tr>' . $tr2 . '</tr></table>';
         
         // if(file_exists("./marqueedata.html") == false){
            // $fh = fopen("./marqueedata.html", 'wt');
               // fwrite($fh, "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>\n");
               // fwrite($fh, "<html>\n");
               // fwrite($fh, "<head>\n");
               // fwrite($fh, "<meta http-equiv='content-type' content='text/html; charset=UTF-8'>\n");
               // fwrite($fh, "<title>VM LiveView Lite (Marquee)</title>\n");
               // fwrite($fh, "</head>\n");
               // fwrite($fh, "<body>\n");
               // fwrite($fh, $MarqueeData);
               // fwrite($fh, "</body>\n");
               // fwrite($fh, "</html>\n");
            // fclose($fh);
         // }
      }
   }else{
      $MarqueeData = '<table><tr><td class="NoData">No Session Data</td></tr></table>';
      writeErrorLog($pageName, "Connect to sql server", "SQL ERROR: (" . $mySQLiConn->connect_error . ")");
   }
?>
<script language="JavaScript1.2" type="text/javascript">
   /* 
      Cross browser VM LiveView Lite standings marquee for rFactor and rFactor2.
      Copyright © frank.geyer@vortex-motorsports.de.
      This script has been inspired by PTRKennyVette's rFactor Hotlaps "Marquee.htm". Visit http://www.pureteamracing.com for more details.
      Requirements: VMHotlaps v1.1.255.F - Build: 20120201 or higher within the rFactor's Plugins directory.
      You are free to modify this script to your needs, but ALL copyright information to VorteX-Motorsports has to be remain untouched!
      
      ### VorteX-Mode = OFF | Frank-Mode = ON ###
      Especially if you call yourself Lutz Enger.
      But in deepest antipathy to your person, I really hope you avoid this live timing suite the same way you threatened simracing league
      admins and endurance race streaming projects to withdraw your permission to them to use your "free" streaming platform "simrace.tv",
      as long as they are using our tools and/or remove the copyright marks to VorteX-Motorsports. But you still have the right to jump start
      one of your highly frequent rotating "bondservant" posse pals to do a cut&paste and/or rev. eng. on this.
      
      To Achim: In case you are still sailing in "her" slip stream and the bottle points to you - I will not obfuscate the loader, so it should
      be cake for you this time.
      
      Oh sorry Mrs. Enger, I almost forgot that you must be capable of doing this all by yourself, because according to a statement of yours,
      barfing at the 24h de la Sarthe 2011 live stream, to you it is an impertinence of the ISI guys not to change the given source code of rFactor1
      which most likely has not been designed for a drivers' lost connection during a race session to reconnect, but to your understanding the source
      code change should take them only 15 minutes to accomplish this and then providing this functionality to the simrace community.
      ### Frank-Mode = OFF | VorteX-Mode = ON ###
      
      Credit MUST stay intact!
   */
   
   /*
      Cross browser Marquee script- © Dynamic Drive (www.dynamicdrive.com)
      For full source code, 100's more DHTML scripts, and Terms Of Use, visit http://www.dynamicdrive.com
      Credit MUST stay intact!
   */
   
   // -- Specify the marquee's width (in pixels)
   var marqueewidth = '1280px';
   // -- Specify the marquee's height
   var marqueeheight = '48px';
   // -- Specify the marquee's marquee speed (larger is faster 1-10)
   var marqueespeed = 3;
   // -- configure background color:
   var marqueebgcolor = 'Black';
   // -- Pause marquee onMousever (0=no. 1=yes)?
   var pauseit = 0;
   
   // -- Specify the marquee's content (don't delete <nobr> tag)
   // -- Keep all content on ONE line, and backslash any single quotations (ie: that\'s great):
   
   // -- var marqueecontent = 'Overall Standings are currently not availbale.';
   
   var marqueecontent = <?php echo "'" . $MarqueeData . "'"; ?>;
   
   var classToShow = <?php echo "'" . $classToShow . "'"; ?>;
   
   //////// NO NEED TO EDIT BELOW THIS LINE ////////
   if(window.ActiveXObject){
      marqueespeed -= 1;
   }
   marqueespeed = (document.all) ? marqueespeed : Math.max(1, marqueespeed - 1); // -- slow speed down by 1 for NS
   var copyspeed = marqueespeed;
   var pausespeed = (pauseit == 0) ? copyspeed : 0;
   var iedom = document.all || document.getElementById;
   if(iedom){
      document.write('<span id="temp" style="visibility:hidden;position:absolute;top:-100px;left:-9000px">' + marqueecontent + '</span>');
   }
   var actualwidth = '';
   var cross_marquee, ns_marquee, interval;
   
   function populate(){
      if(iedom){
         cross_marquee = document.getElementById ? document.getElementById("iemarquee") : document.all.iemarquee;
         cross_marquee.style.left=parseInt(marqueewidth) + 8 + "px";
         cross_marquee.innerHTML = marqueecontent;
         actualwidth = document.all ? temp.offsetWidth : document.getElementById("temp").offsetWidth;
      }else if(document.layers){
         ns_marquee = document.ns_marquee.document.ns_marquee2;
         ns_marquee.left = parseInt(marqueewidth) + 8;
         ns_marquee.document.write(marqueecontent);
         ns_marquee.document.close();
         actualwidth = ns_marquee.document.width;
      }
      lefttime = setInterval("scrollmarquee()",20);
   }
   
   window.onload = populate;
   
   function scrollmarquee(){
      if(iedom){
         if(parseInt(cross_marquee.style.left) > (actualwidth * (-1) + 8)){
            cross_marquee.style.left = parseInt(cross_marquee.style.left) - copyspeed + "px";
         }else{
            cross_marquee.style.left = parseInt(marqueewidth) + 8 + "px";
            // -- window.location.reload();
            window.location.href = "marquee.php?showClass=" + classToShow;
            // -- alert("Finished Scrolling IEDOM");
         }
      }else if(document.layers){
         if(ns_marquee.left > (actualwidth * (-1) + 8)){
            ns_marquee.left -= copyspeed;
         }else{
            ns_marquee.left = parseInt(marqueewidth) + 8;
            // -- alert("Finished Scrolling OTHER");
         }
      }
   }
   
   if(iedom||document.layers){
      with(document){
         document.write('<table border="0" cellspacing="0" cellpadding="0"><td>');
         if(iedom){
            write('<div style="position:relative;width:' + marqueewidth + ';height:' + marqueeheight + ';overflow:hidden">');
            write('<div style="position:absolute;width:' + marqueewidth + ';height:' + marqueeheight + ';background-color:' + marqueebgcolor + '; background-image:url(img/bg-marquee.jpg);" onMouseover="copyspeed=pausespeed" onMouseout="copyspeed=marqueespeed">');
            write('<div id="iemarquee" style="position:absolute;left:0px;top:0px"></div>');
            write('</div></div>');
         }else if(document.layers){
            write('<ilayer width=' + marqueewidth + ' height=' + marqueeheight + ' name="ns_marquee" bgColor='+ marqueebgcolor + '>');
            write('<layer name="ns_marquee2" left=0 top=0 onMouseover="copyspeed=pausespeed" onMouseout="copyspeed=marqueespeed"></layer>');
            write('</ilayer>');
         }
         document.write('</td></table>');
      }
   }
</script>
</body>
</html>
