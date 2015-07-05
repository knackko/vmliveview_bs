<?php   
   include "inc.config.php";
   error_reporting($reportLevel);
   
   $QueryLang = "en";
   if(isset($_GET["Lang"]) && strlen($_GET["Lang"]) == 2){
      $QueryLang = $_GET["Lang"];
   }
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html>
<head>
<meta http-equiv='pragma' content='no-cache'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='content-type' content='text/html; charset=<?php echo $siteCharSet; ?>'>
<title>VM LiveView Lite (Track View)</title>
<link rel='stylesheet' type='text/css' href='<?php echo $siteURL; ?>/styles.css'>
<script type='text/javascript'>
   var siteCharset = '<?php echo $siteCharSet; ?>';
   var siteURL = '<?php echo $siteURL; ?>';
   var siteLang = '<?php echo $QueryLang; ?>';
   var pageRefresh = '<?php echo $refreshTrack; ?>';
</script>
<script src='<?php echo $siteURL; ?>/css.errors.js' type='text/javascript'></script>
</head>
<body>
<div class='break4'></div>
<div class='center'>
<div class='sitename'><?php echo $siteName; ?></div>
</div>
<div id='trackData'></div>
<script src='<?php echo $siteURL; ?>/css.trackdata.js' type='text/javascript'></script>
<script type='text/javascript'>
   if(requestStatus != 1){
      document.write('<div class=\'break4\'><\/div>' +
                     '<div class=\'center\'>' +
                     'Could not load track data ... possible reasons:' +
                     '<br><br>' +
                     'Incompatible browser.' +
                     '<br><br>' +
                     'For best results try an up-to-data version of Firefox, Chrome, IE etc. !' +
                     '<\/div>');
   }
</script>
</body>
</html>
