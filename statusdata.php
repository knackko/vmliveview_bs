<?php
   $pageName = "statusdata.php";
   
   include "inc.config.php";
   error_reporting($reportLevel);
   include "inc.common.php";
   include "inc.mysqli.conn.php";
   include "inc.requests.php";
?>
<?php
   echo "<table class='status'>\n";
   echo "<tr><td class='center' colspan='2'>" . $siteName . "</td></tr>\n";
   $ServerStats = sqlGetServerStats($mySQLiConn);
   if($ServerStats["ServerState"] == 0){
      echo "<tr><td class='center' colspan='2'><img src='./img/Offline.png' alt='OFFLINE'></td></tr>\n";
   }else{
      echo "<tr><td class='center' colspan='2'><img src='./img/Online.png' alt='ONLINE'></td></tr>\n";
   }
   $SessionStatus = "";
   if($ServerStats["MaxLaps"] < 2147483647){
      $SessionStatus = " (" . $ServerStats["CurLaps"] . "/" . $ServerStats["MaxLaps"] . " Laps)";
   }
   echo "<tr><td class='right'>Venue</td><td>" . $ServerStats["TrackName"] . "</td></tr>\n";
   echo "<tr><td class='right'>Start</td><td>" . formatStartTime($ServerStats["SessionStart"]) . "</td></tr>\n";
   echo "<tr><td class='right'>Session</td><td>" . setSessionName($ServerStats["SessionID"], $ServerStats["GameID"]) . " (" . setSessionState($ServerStats["SessionState"]) . ")</td></tr>\n";
   echo "<tr><td class='right'>Status</td><td>" . formatSessionTime($ServerStats["SessionEnd"] - $ServerStats["SessionTime"]) . $SessionStatus . "</td></tr>\n";
   echo "<tr><td class='right'>Vehicles</td><td>" . $ServerStats["NumVehicles"] . "</td></tr>\n";
   echo "<tr><td class='right'>Ambient</td><td>" . sprintf("%.1f", $ServerStats["AmbientTemp"]) . " &deg;C</td></tr>\n";
   echo "<tr><td class='right'>Track</td><td>" . sprintf("%.1f", $ServerStats["TrackTemp"]) . " &deg;C</td></tr>\n";
   echo "<tr><td class='right'>Date</td><td>" . date("d.m.Y (H:i:s) T") . "</td></tr>\n";
   echo "<tr><td class='right'>Live View</td><td><a href='" . $siteURL . "/monitor.php' target='_blank'>Monitor</a> &copy; <a href='http://vortex-motorsports.de/' target='_blank'>VorteX-Motorsports</a></td></tr>\n";
   echo "</table>\n";
?>
