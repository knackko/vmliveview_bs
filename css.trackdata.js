
var intervalID = 0;
var intervalValue = 1000;
var requestCharset = "utf-8";
var requestLang = "en";
var requestStatus = 1;
var requestParams = "";
var requestRefresh = 1;

if(typeof(siteCharset) != "undefined" && siteCharset != null){
   requestCharset = siteCharset;
}

if(typeof(siteLang) != "undefined" && siteLang != null){
   requestLang = siteLang;
}

if(typeof(pageRefresh) != "undefined" && pageRefresh != null){
   requestRefresh = pageRefresh * 1000;
}

if(requestRefresh >= 1000){
   intervalValue = requestRefresh;
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

function requestSetParams(){
   requestParams = "displayLang=" + requestLang + "&ScreenWidth=" + screen.width + "&ScreenHeight=" + screen.height + "&InnerWidth=" + self.innerWidth + "&InnerHeight=" + self.innerHeight;
   
   var inputControls = document.getElementsByTagName("input");
   
   for(var i = 0; i < inputControls.length; i++){
      if(inputControls[i].type == "hidden" && inputControls[i].value != ""){
         requestParams += "&" + inputControls[i].name + "=" + inputControls[i].value;
      }
   }
}

function loadData(){
   var dataRequest = httpRequest();
      
   // -- Note: When using async=false, do NOT write an onreadystatechange function - just put the code after the send() statement!
   if(dataRequest != null){
      try{
         requestSetParams();
         dataRequest.open("POST", "./trackdata.php", false);
         dataRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=" + requestCharset);
         dataRequest.send(requestParams);
         document.getElementById("trackData").innerHTML = dataRequest.responseText;
      }catch(e){
         cssError.handle(1, e);
      }
   }else{
      requestStatus = 0;
   }
}

document.onLoad = loadData();
