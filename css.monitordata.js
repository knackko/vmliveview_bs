
var intervalID = 0;
var intervalValue = 1000;
var requestCharset = "utf-8";
var requestLang = "en";
var requestStatus = 1;
var requestHiddenData = "";
var requestSelectedData = "";
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

function getHiddenData(){
   requestHiddenData = "Hidden=0&displayLang=" + requestLang;
   
   var inputControls = document.getElementsByTagName("input");
   
   for(var i = 0; i < inputControls.length; i++){
      if(inputControls[i].type == "hidden" && inputControls[i].value){
         requestHiddenData += "&" + inputControls[i].name + "=" + inputControls[i].value;
      }
   }
}

function display(){
   requestSelectedData = "Selected=0";
   
   var inputControls = document.getElementsByTagName("input");
   
   for(var i = 0; i < inputControls.length; i++){
      if(inputControls[i].type == "checkbox" && inputControls[i].checked){
         requestSelectedData += "&" + inputControls[i].name + "=" + inputControls[i].value;
      }
      
      if(inputControls[i].type == "radio" && inputControls[i].checked){
         requestSelectedData += "&" + inputControls[i].name + "=" + inputControls[i].value;
      }
   }
}

function loadData(){
   var dataRequest = httpRequest();
   
   getHiddenData();
   
   // -- Note: When using async=false, do NOT write an onreadystatechange function - just put the code after the send() statement!
   if(dataRequest != null){
      try{
         dataRequest.open("POST", "./monitordata.php", false);
         dataRequest.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=" + requestCharset);
         dataRequest.send(requestHiddenData + "&" + requestSelectedData);
         document.getElementById("monitorData").innerHTML = dataRequest.responseText;
      }catch(e){
         cssError.handle(1, e);
      }
   }else{
      requestStatus = 0;
   }
}

document.onLoad = loadData();
