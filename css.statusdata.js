
var intervalID = 0;
var intervalValue = 5000;
var requestCharset = "utf-8";
var requestStatus = 1;

if(typeof(siteCharset) != "undefined" && siteCharset != null){
   requestCharset = siteCharset;
}

intervalID = setInterval(function(){loadData()}, intervalValue);

function httpRequest(){
   var httpRequest = null;
   try{
      httpRequest = new XMLHttpRequest();
   }catch(e){
      try{
         httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
      }catch(e){
         httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
      }
   }
   return httpRequest;
}

function loadData(){
   var dataRequest = httpRequest();
   
   // -- Note: When using async=false, do NOT write an onreadystatechange function - just put the code after the send() statement!
   if(dataRequest != null){
      try{
         dataRequest.open("POST", "./statusdata.php", false);
         dataRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=" + requestCharset);
         dataRequest.send("Status=1");
         document.getElementById("statusData").innerHTML = dataRequest.responseText;
      }catch(e){
         cssError.handle(1, e);
      }
   }else{
      requestStatus = 0;
   }
}

document.onLoad = loadData();
