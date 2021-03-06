<!DOCTYPE html>
<html>
  <head>
    <title>Place Autocomplete Address Form</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 
        0%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">
    <style>
      #locationField, #controls {
        position: relative;
        width: 480px;
      }
      #autocomplete {
        position: absolute;
        top: 0px;
        left: 0px;
        width: 99%;
      }
      .label {
        text-align: right;
        font-weight: bold;
        width: 100px;
        color: #303030;
      }
      #address {
        border: 1px solid #000090;
        background-color: #f0f0ff;
        width: 480px;
        padding-right: 2px;
      }
      #address td {
        font-size: 10pt;
      }
      .field {
        width: 99%;
      }
      .slimField {
        width: 80px;
      }
      .wideField {
        width: 200px;
      }
      #locationField {
        height: 20px;
        margin-top: 13px;
        margin-bottom: 8px;
      }
	  #map-layer {
	margin: 20px 0px;
	max-width: 600px;
	min-height: 400;
}
    </style>
	<script>
$(document).ready(function(){
    $(".btnDistance").click(function(){
        $('#modelWindow').modal('show');
    });
});
</script>
  </head>

  <body onload="geolocate()">
  
  
  
  
  
  <div class="modal fade" id="modelWindow" role="dialog">
            <div class="modal-dialog modal-sm vertical-align-center">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title">Heading</h4>
                </div>
                <div class="modal-body">
                    <?php get_template_part( 'partials/navigation', 'overlaymap' ); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
                </div>
              </div>
            </div>
        </div>
  
  
  
    <div class="alert alert-success" style="display:none;">
  <strong>Success!</strong> Google Address Copied...
</div>
    <div class="checkbox">
  <label><input type="checkbox" value="" class="get_google_address">Copy Address from Google Map</label>
  <button type="button" class="btn btn-primary btnDistance">Distance</button>
</div>

    <div id="locationField">
      <input id="autocomplete" placeholder="Enter your address"
              type="text"></input>
    </div>

    <table id="address">
      <tr>
        <td class="label">Street address</td>
        <td class="slimField"><input class="field" id="street_number"
              disabled="true"></input></td>
        <td class="wideField" colspan="2"><input class="field" id="route"
              disabled="true"></input></td>
      </tr>
      <tr>
        <td class="label">City</td>
        <!-- Note: Selection of address components in this example is typical.
             You may need to adjust it for the locations relevant to your app. See
             https://developers.google.com/maps/documentation/javascript/examples/places-autocomplete-addressform
        -->
        <td class="wideField" colspan="3"><input class="field" id="locality"
              disabled="true"></input></td>
      </tr>
      <tr>
        <td class="label">State</td>
        <td class="slimField"><input class="field"
              id="administrative_area_level_1" disabled="true"></input></td>
        <td class="label">Zip code</td>
        <td class="wideField"><input class="field" id="postal_code"
              disabled="true"></input></td>
      </tr>
      <tr>
        <td class="label">Country</td>
        <td class="wideField" colspan="3"><input class="field"
              id="country" disabled="true"></input></td>
      </tr>
    </table>


    <div id="map"></div>
	<div id="map-layer"></div>

    <script>
      // This example displays an address form, using the autocomplete feature
      // of the Google Places API to help users fill in the information.

      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

      var placeSearch, autocomplete;
      var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
      };
	  var map;
	  var infoWindowHTML;
	  var infoWindow;
	  var currentLocation;

      function initAutocomplete() {


        // show google map start
		var geocoder = new google.maps.Geocoder();
		console.log('aa ' +geocoder);

    // show google map end



        // Create the autocomplete object, restricting the search to geographical
        // location types.
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
            {types: ['geocode']});

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', fillInAddress);
      }

      function fillInAddress() {
        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();
		//console.log(place);
		// get lat
		var lat = place.geometry.location.lat();
		// get lng
		var lng = place.geometry.location.lng();
        //console.log(lat + ' ' + lng);
		
				infoWindowHTML = "Latitude: " + lat + "<br>Longitude: " + lng;
				infoWindow = new google.maps.InfoWindow({map: map, content: infoWindowHTML});
				currentLocation = { lat: lat, lng: lng };
				infoWindow.setPosition(currentLocation);
		
		
		

        for (var component in componentForm) {
          document.getElementById(component).value = '';
          document.getElementById(component).disabled = false;
        }

        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        for (var i = 0; i < place.address_components.length; i++) {
          var addressType = place.address_components[i].types[0];
          if (componentForm[addressType]) {
            var val = place.address_components[i][componentForm[addressType]];
            document.getElementById(addressType).value = val;
          }
        }
      }

      // Bias the autocomplete object to the user's geographical location,
      // as supplied by the browser's 'navigator.geolocation' object.
      function geolocate() {
		  var mapLayer = document.getElementById("map-layer");
		var centerCoordinates = new google.maps.LatLng(37.6, -95.665);
		var defaultOptions = { center: centerCoordinates, zoom: 13 }

		map = new google.maps.Map(mapLayer, defaultOptions);
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };

				var currentLatitude = position.coords.latitude;
				var currentLongitude = position.coords.longitude;
				infoWindowHTML = "Latitude: " + currentLatitude + "<br>Longitude: " + currentLongitude;
				infoWindow = new google.maps.InfoWindow({map: map, content: infoWindowHTML});
				currentLocation = { lat: geolocation.lat, lng: geolocation.lng };
				infoWindow.setPosition(currentLocation);
				console.log('on load: ' +geolocation.lat,geolocation.lng);
            
            var circle = new google.maps.Circle({
              center: geolocation,
              radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
          });

        }
      }


    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAiTp11QRkeWqbqcBmuTfBhk1h0fdF3pnM&libraries=places&callback=initAutocomplete"
        async defer></script>
  </body>
</html>