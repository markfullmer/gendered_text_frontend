function female() {
  var selects = document.getElementsByTagName('select');
  for(var i=0; i<selects.length; i++){
    var items = selects[i].getElementsByTagName('option');
    var index = 0;
    selects[i].selectedIndex = index;
  }
}

