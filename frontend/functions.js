//macht ein Feld sichtbar aufgrund der Auswahl in einem anderen feld/aktivieren einer Checkbox
function toggleField(input, id) {
  var x = document.getElementById(id);
  var y = document.getElementById(input);
  
  if(y.type == "checkbox")
  {
    if (y.checked)
    {
      x.style.display = "";
    }
    else
    {
      x.style.display = "none";
    }
  }
  
  if(y.type == "select-one")
  {
    if (y.options[y.selectedIndex].value == 0)
    {
      x.style.display = "none";
    }
    else
    {
      x.style.display = "";
    }
  }

  if(y.type == "text" || y.type == "number")
  {
    if (y.value == "")
    {
      x.style.display = "none";
    }
    else
    {
      x.style.display = "";
    }
  }
  
}

function toggleFieldInversed(input, id) {
  var x = document.getElementById(id);
  var y = document.getElementById(input);

  if(y.type == "checkbox")
  {
    if (y.checked)
    {
      x.style.display = "none";
    }
    else
    {
      x.style.display = "";
    }
  }

  if(y.type == "select-one")
  {
    if (y.options[y.selectedIndex].value == 0)
    {
      x.style.display = "";
    }
    else
    {
      x.style.display = "none";
    }
  }

  if(y.type == "text" || y.type == "number")
  {
    if (y.value == "")
    {
      x.style.display = "";
    }
    else
    {
      x.style.display = "none";
    }
  }

}

//gibt den Titel der Seite aus, ohne " - CareCRM":
function writeTitle()
{
  var seite = document.title;
  var titel = document.title.replace(" - CareCRM", "");
  document.writeln(titel);
}

//schreibt Wert ins span hinter slider
$(document).ready(function() {

  const $valueSpan = $('.valueSpan');
  const $value = $('#kd_tel2');
  $valueSpan.html("0"+$value.val());
  $value.on('input change', () => {

    $valueSpan.html("0"+$value.val());
  });
});


//ladet eine andere Seite nach x Sekunden
function redirect(site, time)
{
  window.setTimeout(function(){

    window.location.href = site;

  }, time)
}


//zeigt die Werte des aktuell ausgewaehlten vermerks im Textfenster an
function display(x)
{
  var vermerktable = document.getElementById("vermerktable");
  var y = "1";
  while (y < vermerktable.getElementsByTagName("tr").length )
  {
    document.getElementById("tr"+y).className = "";
    y++;
  }

  document.getElementById("tr"+x).className = "ausgewaehlt table-active";

  var text = document.getElementById("div"+x).value;
  document.getElementById("vermerk").value = "Vermerk:\n"+text;
}

//kopiert text in die zwischenablage
function copytext(id)
{
  var copyText = document.getElementById(id);

  copyText.select();
  //für mobile:
  copyText.setSelectionRange(0, 99999);

  document.execCommand("copy");
}

function clearfield(id)
{
  var x = document.getElementById(id);
  x.value = "";
}

function toggleRequired(input, id) {
  var x = document.getElementById(id);
  var y = document.getElementById(input);

  if(y.type == "checkbox")
  {
    if (y.checked)
    {
      x.required = true;
    }
    else
    {
      x.required = false;
    }
  }

  if(y.type == "select-one")
  {
    if (y.options[y.selectedIndex].value == 0)
    {
      x.required = false;
    }
    else
    {
      x.required = true;
    }
  }

  if(y.type == "text" || y.type == "number")
  {
    if (y.value == "")
    {
      x.required = false;
    }
    else
    {
      x.required = true;
    }
  }

}

