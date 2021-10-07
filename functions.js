
var showHide = function (AName) {
    var xElement;
    xElement = document.getElementById(AName);
 
    if (xElement.style.display == 'block'){
    xElement.style.display = 'none';
    console.log(AName + " - display: none;")
    } else {
    xElement.style.display = 'block';
    console.log(AName + " - display: ;")
    }
}

var show = function (AName) {
    var xElement;
    xElement = document.getElementById(AName);
    xElement.style.display = 'block';
}

var hide = function (AName) {
    var xElement;
    xElement = document.getElementById(AName);
    xElement.style.display = 'none';
}

