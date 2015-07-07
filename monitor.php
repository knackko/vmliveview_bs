<?php
   include "inc.config.php";
   error_reporting($reportLevel);
   include "inc.lang.php";
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>VM LiveView Lite (Monitor)</title>
<link rel='stylesheet' type='text/css' href='http://beta.old-drivers-spirit.fr/templates/t3_bs3_blank/local/css/bootstrap.css'>
<link rel='stylesheet' type='text/css' href='<?php echo $siteURL; ?>/styles.css'>
<link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
<script type='text/javascript'>
   var siteCharset = '<?php echo $siteCharSet; ?>';
   var siteURL = '<?php echo $siteURL; ?>';
   var siteLang = '<?php echo $queryLang; ?>';
   var pageRefresh = '<?php echo $refreshMonitor; ?>';
</script>
<script src='<?php echo $siteURL; ?>/css.errors.js' type='text/javascript'></script>
</head>
<body>
<div id='monitorData'></div>
<div class='center'>
<script src='<?php echo $siteURL; ?>/css.monitordata.js' type='text/javascript'></script>
<script type='text/javascript'>
   if(requestStatus != 1){
      document.write('<div class=\'break4\'><\/div>' +
                     '<div>' +
                     'Could not load monitor data ... possible reasons:' +
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
