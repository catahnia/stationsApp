  //κάποιες global παράμετροι που χρειάζονται σε όλη την εφαρμογή
  
  myMap = null;   //the map object
  myInfowindow = null;   //the infowindows object
  myPosition = null;    //browser's position
  markers = [];     //array to store marker references
  
  //dynamicaly associate the initialize function with the onload event of the page
  google.maps.event.addDomListener(window, 'load', initialize);

function initialize() {
  //δομή ρυθμίσεων του χάρτη που θα φτιάξουμε - δεν είναι υποχρεωτικό!
  //https://developers.google.com/maps/documentation/javascript/3.exp/reference#MapOptions
  var mapOptions = {
    zoom: 8,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  //δημιουργία του map object μέσα στο div με id='map-canvas'
  myMap = new google.maps.Map(document.getElementById("site-wrap"), mapOptions);

  //έλεγχος υποστήριξης geolocation για θέση χρήστη  
  if(navigator.geolocation) {
    //ορισμός callback συναρτήσεων για τον χειρισμό 
    //επιτυχούς ή ανεπιτυχούς προσδιορισμού θέσης
    navigator.geolocation.getCurrentPosition(cbGetCurPosOK, cbGetCurPosFail);
  } else {
    //o browser δεν υποστηρίζει geolocation
    alert('Your browser does not support geolocation.');
  }
  
  //create an infowindow object - άδειο, σε default θέση (πάνω αριστερά)
  //θα το χρησιμοποιήσουν τα markers όταν τα κλικάρουμε.
  myInfowindow = new google.maps.InfoWindow({
     map: myMap,
     content: 'feed me'
  });
  //hide the infowindow
  myInfowindow.close();

}

//callback σε υποστήριξη geolocation
//position είναι το στίγμα που επεστράφει από τον browser
function cbGetCurPosOK(position) {
  //έστω διαβάζουμε την τρέχουσα θέση και φτιάχνουμε ένα σημείο χάρτη
  var curPosition = new google.maps.LatLng( position.coords.latitude,
                                            position.coords.longitude );
  // κεντράρουμε το χάρτη σε αυτό το σημείο
  myMap.setCenter(curPosition);

  // φτιάχνουμε μια πινέζα (marker) σε αυτό το σημείο
  var curMarker = new google.maps.Marker({ position: curPosition,
                                              title: 'You are here!',
                                              icon: 'home.png' });
  //βάζουμε την πινέζα στο χάρτη (γίνεται και στην αρχικοποίηση!)
  curMarker.setMap(myMap);

  //zoom στη θέση μας - επιλέξτε επίπεδο zoom που επιτρέπει στο χρήστη
  //να δει και κάποια σημεία αναφοράς της περιοχής για να προσανατολιστεί.
  //Συνήθως τιμές 10-12 είναι οι ποιο ταιριαστές
  myMap.setZoom(12);
}

//callback σε MH υποστήριξη geolocation
function cbGetCurPosFail(error) { 
  //διαβάζουμε το error code και ενημερώνουμε τον χρήστη
  switch(error.code) { 
    case error.PERMISSION_DENIED: 
      alert("User denied the request for Geolocation."); 
      break; 
    case error.POSITION_UNAVAILABLE: 
      alert("Location information is unavailable."); 
      break; 
    case error.TIMEOUT: 
      alert("The request to get user location timed out."); 
      break; 
    case error.UNKNOWN_ERROR: 
      alert("An unknown error occurred."); 
      break; 
  } 
}


//συνάρτηση αρχικοποίησης AJAX υποδομής - επιστρέφει reference στο AJAX
//object ή false αν δεν υποστηρίζονται AJAX κλήσεις από τον browser.
function initAJAX() { 
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    return new XMLHttpRequest();
  } 
  else if (window.ActiveXObject) {
    // code for IE6, IE5 - εδώ χρησιμοποιούνται ActiveX object (MS τεχνολογίες)
    return new ActiveXObject("Microsoft.XMLHTTP");
  } else {
    alert("Your browser does not support XMLHTTP!");
    return false;
  }
}  


function getData() {

  //εκκίνηση AJAX υποδομής
  var xmlhttp = initAJAX();

  //Εφόσον υποστηρίζονται AJAX κλίεις:
  if (xmlhttp) {
    // κλήση σε API για λήψη δεδομένων (ΕΔΩ κλήση σε στατικό JSON αρχείο)
    // Για λόγους security ο ΙΕ δεν ανήγει τοπικό αρχείο οπότε για τις ανάγκες
    //του παραδείγματος το έβαλα στον server που βλέπετε παρακάτω.
    xmlhttp.open("GET","http://localhost:8000/gasStations",true);
    xmlhttp.send(null);

    //ορισμός callback για τον χειρισμό της απάντησης
    xmlhttp.onreadystatechange=function() {
      if(xmlhttp.readyState==4 && xmlhttp.status==200) {
            
        var obj = JSON.parse(xmlhttp.responseText);
        var stations = obj.gasStations;
        for (i=0; i<stations.length; i++){
          putMarker( stations[i].id, 
                     stations[i].gasStationLat,
                     stations[i].gasStationLong,
                     stations[i].gasStationAddress );
        }   //for
      }   //if
    }   //callback
  }   //if xmlhttp
}   //function



// δημιουργία, τοποθέτηση και αποθήκευση marker
function putMarker(id,myLat,myLong,gasStationAddress) {

  //create a position object
  var myPosition = new google.maps.LatLng(myLat,myLong);

  //initialize and create a marker object
  myMarker = new google.maps.Marker({
  	 position: myPosition,  //θέση
     map: myMap,            //χαρτης στον οποίο θα εμφανιστούν
     title: gasStationAddress,      //tooltip
     draggable: false       //χωρίς δυνατότητα μετακίνησης νε drag'n'drop
  });
  
  //αποθήκευση στο markers array
  markers.push(myMarker);

  //associate the infowindow with the click on the marker
  google.maps.event.addListener( myMarker, 'click', function(){
		myInfowindow.setContent(id.toString()+" : "+gasStationAddress);
    //ζηταμε να εμφανιστεί στη θέση του συσχετισμένου marker
    myInfowindow.position = myMarker.getPosition();
  	myInfowindow.open(myMap,this); }
  );

}

//αφαίρεση των markers απο το χάρτη, κάνοντας το property map
//βλ. και αρχικοποίηση marker, παραπάνω!
function cleanMarkers() {
  for (i=0; i<markers.length; i++)
    markers[i].setMap(null);
  //άδειασμα και του πίνακα με τα references
  markers=[];
}

function getNumberOfGasStations() {
  var xmlhttp = initAJAX();

  if (xmlhttp) {
    // κλήση σε API για λήψη δεδομένων (ΕΔΩ κλήση σε στατικό JSON αρχείο)
    // Για λόγους security ο ΙΕ δεν ανήγει τοπικό αρχείο οπότε για τις ανάγκες
    //του παραδείγματος το έβαλα στον server που βλέπετε παρακάτω.
    xmlhttp.open("GET","http://localhost:8000/gasStationsCount",true);
    xmlhttp.send(null);

    //ορισμός callback για τον χειρισμό της απάντησης
    xmlhttp.onreadystatechange=function() {
      if(xmlhttp.readyState==4 && xmlhttp.status==200) {
            
        var obj = JSON.parse(xmlhttp.responseText);
        var number = obj.count;
        var element = document.getElementById("number");
        element.innerHTML=number;
      }   //if
    }   //callback
  }   //if xmlhttp

}

function showStats() {
  var xmlhttp = initAJAX();
  var fuelId = document.getElementsByName('fuelTypeID')[0].value;
  var fuelSubId = document.getElementsByName('fuelSubTypeID')[0].value;


  if (xmlhttp) {
    // κλήση σε API για λήψη δεδομένων (ΕΔΩ κλήση σε στατικό JSON αρχείο)
    // Για λόγους security ο ΙΕ δεν ανήγει τοπικό αρχείο οπότε για τις ανάγκες
    //του παραδείγματος το έβαλα στον server που βλέπετε παρακάτω.
    xmlhttp.open("GET","http://localhost:8000/stats?fuelTypeID="+fuelId+"&fuelSubTypeID="+fuelSubId,true);
    xmlhttp.send(null);

    //ορισμός callback για τον χειρισμό της απάντησης
    xmlhttp.onreadystatechange=function() {
      if(xmlhttp.readyState==4 && xmlhttp.status==200) {
            
        var obj = JSON.parse(xmlhttp.responseText);
        var stats = obj.stats;
        var max = stats[1];
        var min = stats[0];
        var avg = stats[2];

        document.getElementById("min").innerHTML=min;
        document.getElementById("max").innerHTML=max;
        document.getElementById("avg").innerHTML=avg;

        //var element = document.getElementById("number");
        //element.innerHTML=number;
      }   //if
    }   //callback
  }   //if xmlhttp

}

function fillIds () {
  var select = document.getElementById("stationId");

  var xmlhttp = initAJAX();

  //Εφόσον υποστηρίζονται AJAX κλίεις:
  if (xmlhttp) {
    // κλήση σε API για λήψη δεδομένων (ΕΔΩ κλήση σε στατικό JSON αρχείο)
    // Για λόγους security ο ΙΕ δεν ανήγει τοπικό αρχείο οπότε για τις ανάγκες
    //του παραδείγματος το έβαλα στον server που βλέπετε παρακάτω.
    xmlhttp.open("GET","http://localhost:8000/gasStations",true);
    xmlhttp.send(null);

    //ορισμός callback για τον χειρισμό της απάντησης
    xmlhttp.onreadystatechange=function() {
      if(xmlhttp.readyState==4 && xmlhttp.status==200) {
            
        var obj = JSON.parse(xmlhttp.responseText);
        var stations = obj.gasStations;
        for (i=0; i<stations.length; i++){
            var option = document.createElement('option');
            option.innerHTML = stations[i].id;
            option.value = stations[i].id;
            select.appendChild(option);
        }   //for
      }   //if
    }   //callback
  }   //if xmlhttp

}

function getPrices () {
  var xmlhttp = initAJAX();
  var select = document.getElementById("stationId");
  var id = select.options[select.selectedIndex].value
  var parent = document.getElementById("priceCol");

  while (parent.firstChild) {
    parent.removeChild(parent.firstChild);
  }
 
  //Εφόσον υποστηρίζονται AJAX κλίεις:
  if (xmlhttp) {
    // κλήση σε API για λήψη δεδομένων (ΕΔΩ κλήση σε στατικό JSON αρχείο)
    // Για λόγους security ο ΙΕ δεν ανήγει τοπικό αρχείο οπότε για τις ανάγκες
    //του παραδείγματος το έβαλα στον server που βλέπετε παρακάτω.
    xmlhttp.open("GET","http://localhost:8000/prices/"+id,true);
    xmlhttp.send(null);

    //ορισμός callback για τον χειρισμό της απάντησης
    xmlhttp.onreadystatechange=function() {
      if(xmlhttp.readyState==4 && xmlhttp.status==200) {
            
        var obj = JSON.parse(xmlhttp.responseText);
        var prices = obj.prices;
        for (i=0; i<prices.length; i++){
            
            var p = document.createElement('p');
            p.innerHTML = prices[i].priceDataId+" : "+ prices[i].fuelName + " : " + prices[i].fuelPrice;
            parent.appendChild(p);
        }   //for
      }   //if
    }   //callback
  }   //if xmlhttp

}

function getOrders(id) {
  var xmlhttp = initAJAX();
 
  var parent = document.getElementById("ordersBody");

  while (parent.firstChild) {
    parent.removeChild(parent.firstChild);
  }
  //Εφόσον υποστηρίζονται AJAX κλίεις:
  if (xmlhttp) {
    // κλήση σε API για λήψη δεδομένων (ΕΔΩ κλήση σε στατικό JSON αρχείο)
    // Για λόγους security ο ΙΕ δεν ανήγει τοπικό αρχείο οπότε για τις ανάγκες
    //του παραδείγματος το έβαλα στον server που βλέπετε παρακάτω.
    xmlhttp.open("GET","http://localhost:8000/orders/"+id,true);
    xmlhttp.send(null);

    //ορισμός callback για τον χειρισμό της απάντησης
    xmlhttp.onreadystatechange=function() {
      if(xmlhttp.readyState==4 && xmlhttp.status==200) {
            
        var obj = JSON.parse(xmlhttp.responseText);
        var orders = obj.orders;
        for (i=0; i<orders.length; i++){
            
            var p = document.createElement('p');
            p.innerHTML = orders[i].users_id+" ) "+ orders[i].quantity + "l of "+ orders[i].fuelName + " at " + orders[i].fuelPrice;
            parent.appendChild(p);
        }   //for
      }   //if
    }   //callback
  }   //if xmlhttp


}

function fillPriceDataIds(id) {
  var select = document.getElementById("selectPriceId");

  var xmlhttp = initAJAX();

  //Εφόσον υποστηρίζονται AJAX κλίεις:
  if (xmlhttp) {
    // κλήση σε API για λήψη δεδομένων (ΕΔΩ κλήση σε στατικό JSON αρχείο)
    // Για λόγους security ο ΙΕ δεν ανήγει τοπικό αρχείο οπότε για τις ανάγκες
    //του παραδείγματος το έβαλα στον server που βλέπετε παρακάτω.
    xmlhttp.open("GET","http://localhost:8000/prices/"+id,true);
    xmlhttp.send(null);

    //ορισμός callback για τον χειρισμό της απάντησης
    xmlhttp.onreadystatechange=function() {
      if(xmlhttp.readyState==4 && xmlhttp.status==200) {
            
        var obj = JSON.parse(xmlhttp.responseText);
        var prices = obj.prices;
        for (i=0; i<prices.length; i++){
            var option = document.createElement('option');
            option.innerHTML = prices[i].fuelName;
            option.value = prices[i].priceDataId;
            select.appendChild(option);
        }   //for


      }   //if
    }   //callback
  }   //if xmlhttp

}

function change(){
    var select=document.getElementById("selectPriceId");
    var id = select.options[select.selectedIndex].value
    console.log(id);
    document.getElementById("priceID").value=id;

}
function getSelection() {
  var select = document.getElementById("ChartType");
  var selection = select.options[select.selectedIndex].value;
  console.log(selection);
  if(selection=="pie"){
    prepareData();
  } else if(selection=="column"){
    graphTwo();
  }
}

function prepareData() {
  var name = ['AVIN', 'SHELL', 'AEGEAN', 'EKO', 'BP', 'REVOIL',
  'ΕΛΙΝΟΙΛ','Α.Π.','ΕΤΕΚΑ','KAOIL','ΑΡΓΩ','JETOIL','SILKOIL','ΤΡΙΑΙΝΑ', 'CYCLON'];
  var count = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
  var data=[];
  data.name = name;
  data.count = count;
  var xmlhttp = initAJAX();

  //Εφόσον υποστηρίζονται AJAX κλίεις:
  if (xmlhttp) {
    // κλήση σε API για λήψη δεδομένων (ΕΔΩ κλήση σε στατικό JSON αρχείο)
    // Για λόγους security ο ΙΕ δεν ανήγει τοπικό αρχείο οπότε για τις ανάγκες
    //του παραδείγματος το έβαλα στον server που βλέπετε παρακάτω.
    xmlhttp.open("GET","http://localhost:8000/gasStations",true);
    xmlhttp.send(null);

    //ορισμός callback για τον χειρισμό της απάντησης
    xmlhttp.onreadystatechange=function() {
      if(xmlhttp.readyState==4 && xmlhttp.status==200) {
        var obj = JSON.parse(xmlhttp.responseText);
        var stations = obj.gasStations;
        console.log(stations.length,data.length);

        for(var i=0; i<stations.length; i++){
          for(var j=0; j<15; j++){

            if(stations[i].fuelCompNormalName==data.name[j]) {
              data.count[j]++;
            }
          }
        }
        google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {

        var datas = new google.visualization.DataTable();

        datas.addColumn('string', 'Company Name');
        datas.addColumn('number', 'Count');
        datas.addRows(15);

        for(i=0;i<15;i++){
          datas.setCell(i,0, data.name[i]);
          datas.setCell(i,1, data.count[i]);
        }
        var options = {
          title: 'No Of Gas Stations Per Company'
        };

        var chart = new google.visualization.PieChart(document.getElementById('Chart'));

        chart.draw(datas, options);
      }


      }   //if
    }   //callback
  }   //if xmlhttp

}


function graphTwo() {
  var name = ['AVIN', 'SHELL', 'AEGEAN', 'EKO', 'BP', 'REVOIL',
  'ΕΛΙΝΟΙΛ','Α.Π.','ΕΤΕΚΑ','KAOIL','ΑΡΓΩ','JETOIL','SILKOIL','ΤΡΙΑΙΝΑ', 'CYCLON'];
  var count = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
  var data=[];
  data.name = name;
  data.count = count;
  var xmlhttp = initAJAX();

  //Εφόσον υποστηρίζονται AJAX κλίεις:
  if (xmlhttp) {
    // κλήση σε API για λήψη δεδομένων (ΕΔΩ κλήση σε στατικό JSON αρχείο)
    // Για λόγους security ο ΙΕ δεν ανήγει τοπικό αρχείο οπότε για τις ανάγκες
    //του παραδείγματος το έβαλα στον server που βλέπετε παρακάτω.
    xmlhttp.open("GET","http://localhost:8000/gasStations",true);
    xmlhttp.send(null);

    //ορισμός callback για τον χειρισμό της απάντησης
    xmlhttp.onreadystatechange=function() {
      if(xmlhttp.readyState==4 && xmlhttp.status==200) {
        var obj = JSON.parse(xmlhttp.responseText);
        var stations = obj.gasStations;
        console.log(stations.length,data.length);

        for(var i=0; i<stations.length; i++){
          for(var j=0; j<15; j++){

            if(stations[i].fuelCompNormalName==data.name[j]) {
              data.count[j]++;
            }
          }
        }
        google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {

        var datas = new google.visualization.DataTable();

        datas.addColumn('string', 'Company Name');
        datas.addColumn('number', 'Count');
        datas.addRows(15);

        for(i=0;i<15;i++){
          datas.setCell(i,0, data.name[i]);
          datas.setCell(i,1, data.count[i]);
        }
        /*
        var view = new google.visualization.DataView(datas);
        view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);
*/
        var options = {
          title: 'No Of Gas Stations Per Company',
          bar: {groupWidth: "70%"},
          legend: { position: "none" },
          colors: ['#1c91c0']

        };

        var chart = new google.visualization.ColumnChart(document.getElementById('Chart'));

        chart.draw(datas, options);
      }


      }   //if
    }   //callback
  }   //if xmlhttp

}

function fillFuelIds () {
  var select = document.getElementById("orderFuelID");

  var xmlhttp = initAJAX();

  //Εφόσον υποστηρίζονται AJAX κλίεις:
  if (xmlhttp) {
    // κλήση σε API για λήψη δεδομένων (ΕΔΩ κλήση σε στατικό JSON αρχείο)
    // Για λόγους security ο ΙΕ δεν ανήγει τοπικό αρχείο οπότε για τις ανάγκες
    //του παραδείγματος το έβαλα στον server που βλέπετε παρακάτω.
    xmlhttp.open("GET","http://localhost:8000/prices",true);
    xmlhttp.send(null);

    //ορισμός callback για τον χειρισμό της απάντησης
    xmlhttp.onreadystatechange=function() {
      if(xmlhttp.readyState==4 && xmlhttp.status==200) {
            
        var obj = JSON.parse(xmlhttp.responseText);
        var prices = obj.prices;
        for (i=0; i<prices.length; i++){
            var option = document.createElement('option');
            option.innerHTML = prices[i].priceDataId + ") "+prices[i].fuelNormalName+" at :"+prices[i].gasstation_id;
            option.value = prices[i].priceDataId;
            select.appendChild(option);
        }   //for

        select.style="width: 15em;";
      
      }   //if
    }   //callback
  }   //if xmlhttp

}

function select() {
  var select=document.getElementById("orderFuelID");
    var id = select.options[select.selectedIndex].value
    console.log(id);
    document.getElementById("orderPriceDataId").value=id;
}
