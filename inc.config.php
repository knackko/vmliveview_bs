<?php
   // -- default timezone
   /*
      see http://www.php.net/manual/en/timezones.php
      for a list of supported timezones
   */
   date_default_timezone_set("Europe/Berlin");
   
   // -- site configuration
   $siteName = "VM LiveView Lite";
   $siteURL = "http://your-domain.com/vmliveview";
   $siteCharSet = "UTF-8";
   
   // -- error reporting
   $reportLevel = 0; // -- report all errors(-1), report no errors (0)
   $logfileFolder = "./logs";
   
   // -- sql connectivity
   $dbHost = "localhost";
   $dbPort = "3306";
   $dbCharSet = "utf8";
   $dbName = "vmliveview01";
   $dbReader = "vmliveview01dbw";
   $dbReaderPass = "your-mysql-password";
   
   // -- refresh rates in seconds
   $refreshMonitor = 1.2;
   $refreshTrack = 1.2;
   
   // -- display limits
   $displayMaxLaps = 50;
   
   // -- event admittance limits
   $admitLaps = 50;
   $admitTime = 1.07;
   $admitTimePerClass = 1;
   
   // -- vehicle class mappings
   /* ### pre defined colors ###
      LMP1 = #D81E05
      LMP2 = #335687
      GT1 = #009E49
      GT2 = #EF6B00
      GTC = #8064A2
   */
   $class1RealName = "ES_P1";
   $class1DisplayName = "P1";
   $class1Color = "#D81E05";
   $class1ProgressImg = "./img/CarP1.png";
   $class1MarqueeImg = "./img/ClassP1.png";
   $class2RealName = "ES_P2";
   $class2DisplayName = "P2";
   $class2Color = "#335687";
   $class2ProgressImg = "./img/CarP2.png";
   $class2MarqueeImg = "./img/ClassP2.png";
   $class3RealName = "ES_GT1";
   $class3DisplayName = "GT1";
   $class3Color = "#009E49";
   $class3ProgressImg = "./img/CarGT1.png";
   $class3MarqueeImg = "./img/ClassGT1.png";
   $class4RealName = "ES_GT2";
   $class4DisplayName = "GT2";
   $class4Color = "#EF6B00";
   $class4ProgressImg = "./img/CarGT2.png";
   $class4MarqueeImg = "./img/ClassGT2.png";
   
   // -- marquee class sequences (case sensitive!)
   // -- possible values: ALL, Class1, Class2, Class3, Class4
   $marqueeClassSequenceDefault = "ALL";
   $marqueeClassSequence1 = "Class1";
   $marqueeClassSequence2 = "Class2";
   $marqueeClassSequence3 = "Class3";
   $marqueeClassSequence4 = "Class4";
?>
