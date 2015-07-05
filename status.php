<?php
   $pageName = "status.php";
   
   include "inc.config.php";
   error_reporting($reportLevel);
   include "inc.common.php";
   include "inc.mysqli.conn.php";
   include "inc.requests.php";
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html>
<head>
<meta http-equiv='pragma' content='no-cache'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='content-type' content='text/html; charset=<?php echo $siteCharSet; ?>'>
<title>VM LiveView Lite (Server Status)</title>
<link rel='stylesheet' type='text/css' href='<?php echo $siteURL; ?>/styles.css'>
</head>
<body>
<div align='center'>
<div id='statusData'></div>
<script src='<?php echo $siteURL; ?>/css.statusdata.js' type='text/javascript'></script>
<script type='text/javascript'>
   if(requestStatus != 1){
      document.write('<div class=\'break4\'><\/div>' +
                     '<div>' +
                     'Could not load status data ... possible reasons:' +
                     '<br><br>' +
                     'Incompatible browser.' +
                     '<br><br>' +
                     'For best results try an up-to-data version of Firefox, Chrome, IE etc. !' +
                     '<\/div>');
   }
</script>
</div>
</body>
</html>
