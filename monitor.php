<?php
   include "inc.config.php";
   error_reporting($reportLevel);
   include "inc.lang.php";
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
<html>
<head>
<meta http-equiv='pragma' content='no-cache'>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='expires' content='0'>
<meta http-equiv='content-type' content='text/html; charset=<?php echo $siteCharSet; ?>'>
<title>VM LiveView Lite (Monitor)</title>
<link rel='stylesheet' type='text/css' href='<?php echo $siteURL; ?>/styles.css'>
<link rel='stylesheet' type='text/css' href='http://beta.old-drivers-spirit.fr/templates/t3_bs3_blank/local/css/bootstrap.css'>
<script type='text/javascript'>
   var siteCharset = '<?php echo $siteCharSet; ?>';
   var siteURL = '<?php echo $siteURL; ?>';
   var siteLang = '<?php echo $queryLang; ?>';
   var pageRefresh = '<?php echo $refreshMonitor; ?>';
</script>
<script src='<?php echo $siteURL; ?>/css.errors.js' type='text/javascript'></script>
</head>
<body>
<form name='SelectLang' action='<?php echo $siteURL; ?>/monitor.php' method='POST'>
<label class='lang'><span class='bold'><?php echo $langSelectLang; ?>: </span>
   <select name='displayLang' onChange='this.form.submit();'>
      <option value='00' <?php echo $selected00; ?>>Browser</option>
      <option value='de' <?php echo $selectedDE; ?>>Deutsch</option>
      <option value='en' <?php echo $selectedEN; ?>>English</option>
      <option value='fr' <?php echo $selectedFR; ?>>Français</option>
      <option value='it' <?php echo $selectedIT; ?>>Italiano</option>
   </select>
</label>
</form>
<div class='break4'></div>
<div class='center'>
<div class='sitename'><?php echo $siteName; ?></div>
<div id='monitorData'></div>
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
