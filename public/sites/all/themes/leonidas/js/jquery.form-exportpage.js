
var inputs = new Array();
var labels = new Array();
var radios = new Array();
var radioLabels = new Array();
var checkboxes = new Array();
var checkboxLabels = new Array();
var buttons = new Array();

function is_mac() {
  if (navigator.appVersion.indexOf("Safari") != -1)
  {
    if(!window.getComputedStyle)
    {
      return true;
    }
  }

  return false;
}

function initCastomForms() {
  if(!document.getElementById) {return false;}
  getElements();
  separateElements();
  replaceRadios();
  replaceCheckboxes();
}


// getting all the required elements
function getElements() {
  var _frms = document.getElementsByTagName("form");
  for (var nf = 0; nf < _frms.length; nf++) {
    if(_frms[nf].className.indexOf("default") == -1) {
      var a = document.forms[nf].getElementsByTagName("input");
      for(var nfi = 0; nfi < a.length; nfi++) {
        inputs.push(a[nfi]);
      }
      var b = document.forms[nf].getElementsByTagName("label");
      for(var nfl = 0; nfl < b.length; nfl++) {
        labels.push(b[nfl]);
      }
      
    }
  }
}

// separating all the elements in their respective arrays
function separateElements() {
  var r = 0; var c = 0; var t = 0; var rl = 0; var cl = 0; var tl = 0; var b = 0;
  for (var q = 0; q < inputs.length; q++) {
    if(inputs[q].type == "radio") {
      radios[r] = inputs[q]; ++r;
      for(var w = 0; w < labels.length; w++) {
        if((inputs[q].id) && labels[w].htmlFor == inputs[q].id)
        {
          radioLabels[rl] = labels[w];
          ++rl;
        }
      }
    }
    if(inputs[q].type == "checkbox") {
      checkboxes[c] = inputs[q]; ++c;
      for(var w = 0; w < labels.length; w++) {
        if((inputs[q].id) && (labels[w].htmlFor == inputs[q].id))
        {
          checkboxLabels[cl] = labels[w];
          ++cl;
        }
      }
    }
    if((inputs[q].type == "submit") || (inputs[q].type == "button")) {
      buttons[b] = inputs[q]; ++b;
    }
  }
}

//replacing radio buttons
function replaceRadios() {
  for (var q = 0; q < radios.length; q++) {
    if(!jQuery(radios[q]).hasClass('outtaHere')){
      radios[q].className += " outtaHere";
      var radioArea = document.createElement("div");
      if(radios[q].checked) {
        radioArea.className = "radioAreaChecked";
        radioLabels[q].className += " radioAreaCheckedLabel";
        jQuery(radios[q]).closest('.form-type-radio').find('label').addClass('radioAreaCheckedLabel');
      }
      else
      {
        radioArea.className = "radioArea";
      }
      radioArea.id = "myRadio" + q;
      radios[q].parentNode.insertBefore(radioArea, radios[q]);
      radios[q]._ra = radioArea;

      radioArea.onclick = new Function('rechangeRadios('+q+')');
      if (radioLabels[q])
      {
        radioLabels[q].onclick = new Function('rechangeRadios('+q+')');
      }
    }
  }
  return true;
}

//checking radios
function checkRadios(who) {
  var what = radios[who]._ra;
  for(var q = 0; q < radios.length; q++) {
    if((radios[q]._ra.className == "radioAreaChecked")&&(radios[q]._ra.nextSibling.name == radios[who].name))
    {
      radios[q]._ra.className = "radioArea";
      radioLabels[q].className = radioLabels[q].className.replace("radioAreaCheckedLabel", "");
      jQuery(radios[q]).closest('.form-type-radio').find('label').removeClass('radioAreaCheckedLabel');
    }
  }
  what.className = "radioAreaChecked";
  radioLabels[who].className += " radioAreaCheckedLabel";
  jQuery(radios[who]).closest('.form-type-radio').find('label').addClass('radioAreaCheckedLabel');
}

//changing radios
function changeRadios(who) {
  if(radios[who].checked) {
    for(var q = 0; q < radios.length; q++) {
      if(radios[q].name == radios[who].name) {
        radios[q].checked = false;
      }
      radios[who].checked = true;
      checkRadios(who);
    }
  }
}

//rechanging radios
function rechangeRadios(who) {
  if (!radios[who].checked) {
    for(var q = 0; q < radios.length; q++) {
      if(radios[q].name == radios[who].name && radios[q].checked)	{
        radios[q].checked = false;
      }
    }
    radios[who].checked = true;
    // Trigger change event to call all listeners.
    jQuery(radios[who]).trigger('change');
    checkRadios(who);
  }
}

//replacing checkboxes
function replaceCheckboxes() {
  for (var q = 0; q < checkboxes.length; q++) {
    if(!jQuery(checkboxes[q]).hasClass('outtaHere')){
      checkboxes[q].className += " outtaHere";
      var checkboxArea = document.createElement("div");
      if(checkboxes[q].checked) {
        checkboxArea.className = "checkboxAreaChecked";
        checkboxLabels[q].className += " checkboxAreaCheckedLabel"
      }
      else {
        checkboxArea.className = "checkboxArea";
      }
      checkboxArea.id = "myCheckbox" + q;
      checkboxes[q].parentNode.insertBefore(checkboxArea, checkboxes[q]);
      checkboxes[q]._ca = checkboxArea;
      checkboxArea.onclick = checkboxArea.onclick2 = new Function('rechangeCheckboxes('+q+')');
      if (checkboxLabels[q])
      {
        checkboxLabels[q].onclick = new Function('changeCheckboxes('+q+')');
      }

      checkboxes[q].onkeydown = checkEvent;
    }
  }
  return true;
}

//checking checkboxes
function checkCheckboxes(who, action) {
  var what = checkboxes[who]._ca;
  if(action == true) {
    what.className = "checkboxAreaChecked";
    what.checked = true;
    checkboxLabels[who].className += " checkboxAreaCheckedLabel";
  }
  if(action == false) {
    what.className = "checkboxArea";
    what.checked = false;
    checkboxLabels[who].className = checkboxLabels[who].className.replace("checkboxAreaCheckedLabel", "");
  }
}

//changing checkboxes
function changeCheckboxes(who) {
  if(checkboxes[who].checked) {
    checkCheckboxes(who, false);
  }
  else {
    checkCheckboxes(who, true);
  }
}

//rechanging checkboxes
function rechangeCheckboxes(who) {
  var tester = false;
  if(checkboxes[who].checked == true) {
    tester = false;
  }
  else {
    tester = true;
  }
  checkboxes[who].checked = tester;
  checkCheckboxes(who, tester);
}

//check event
function checkEvent(e) {
  if (!e) var e = window.event;
  if(e.keyCode == 32) {for (var q = 0; q < checkboxes.length; q++) {if(this == checkboxes[q]) {changeCheckboxes(q);}}} //check if space is pressed
}


 //window.onload = initCastomForms;