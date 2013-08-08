var y1 = 355;   // change the # on the left to adjuct the Y co-ordinate
(document.getElementById) ? dom = true : dom = false;

function hideIt() {
  if (dom) {document.getElementById("seleccion").style.visibility='hidden';}
}

function showIt() {
  if (dom) {document.getElementById("seleccion").style.visibility='visible';}
}

function placeIt() {
  if (dom && !document.all) {document.getElementById("seleccion").style.top = window.pageYOffset + (window.innerHeight - (window.innerHeight-y1)) + "px";}
  if (document.all) {document.all["seleccion"].style.top = document.documentElement.scrollTop + (document.documentElement.clientHeight - (document.documentElement.clientHeight-y1)) + "px";}
  window.setTimeout("placeIt()", 10); 
 }