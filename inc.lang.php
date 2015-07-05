<?php
   $pageNameIO = "inc.lang.php";
   
   $queryLang = "en";
   
   $selected00 = "";
   $selectedDE = "";
   $selectedEN = "";
   $selectedFR = "";
   $selectedIT = "";
   
   $langBrowser = "en";
   if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])){
      $langBrowser = strtolower(substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2));
   }
   
   // -- echo "Debug Browser Lang: " . $langBrowser . "\n";
   
   $langBrowserAvailable = false;
   $langBrowserInclude = "./lang/en.php";
   if(file_exists("./lang/" . $langBrowser . ".php")){
      $langBrowserAvailable = true;
      $langBrowserInclude = "./lang/" . $langBrowser . ".php";
   }
   
   if(isset($_POST["displayLang"]) && strlen($_POST["displayLang"]) == 2){
      switch($_POST["displayLang"]){
         case "00": $queryLang = $langBrowser; include $langBrowserInclude; $selected00 = "selected='selected'"; break;
         case "de": $queryLang = "de"; include "./lang/de.php"; $selectedDE = "selected='selected'"; break;
         case "en": $queryLang = "en"; include "./lang/en.php"; $selectedEN = "selected='selected'"; break;
         case "fr": $queryLang = "fr"; include "./lang/fr.php"; $selectedFR = "selected='selected'"; break;
         case "it": $queryLang = "it"; include "./lang/it.php"; $selectedIT = "selected='selected'"; break;
           default: $queryLang = "en"; include "./lang/en.php"; $selectedEN = "selected='selected'"; break;
      }
   }else{
      if($langBrowserAvailable == true){
         $queryLang = $langBrowser; include $langBrowserInclude; $selected00 = "selected='selected'";
      }else{
         $queryLang = "en"; include "./lang/en.php"; $selectedEN = "selected='selected'";
      }
   }
?>
