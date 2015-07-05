var cssError = {
   // -- true: errors via alert
   // -- false: errors are being logged
   debugMode:false,
   
   // -- process errors
   handle:function(sev, msg){
      if(cssError.debugMode){
         // -- alert("Error - Severity: (" + sev + ") Message: (" + msg + ")");
      }else{
         var img = new Image();
         img.src = "css.errors.php?sev=" + encodeURIComponent(sev) + "&msg=" + encodeURIComponent(msg) + "&url=" + encodeURIComponent(document.URL);
      }
   }
}

// -- error has not been caught and forwarded to "errorhandler" manually -> windowerror
window.onerror = function(msg, url, line){
   cssError.handle(1, "UncaughtError: " + msg + " in " + url + ", line " + line);
   return true; // -- continue
}
