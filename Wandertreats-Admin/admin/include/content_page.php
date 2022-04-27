 <div class="row">
	<div class="col-md-12">
		
		
		
		<label>Draw Location Point Here In Map :<span class="red">*</span></label>
			<p><span>Please select the area by putting the points around it. Please <a href="../assets/videos/geofence/geofencenew_player.html" target="_blank" alt="Link"><b>click here</b></a> to view how to select the area and add it.</span></p>
			<div class="panel-heading location-map" style="background:none;">
				<div class="google-map-wrap">
					<input id="pac-input" type="text" placeholder="Enter Location For More Focus" style="padding:4px;width: 200px;margin-top: 5px;">
					<div id="map-canvas" class="google-map" style="width:100%; height:500px;"></div>
				</div>
				<div style="text-align: center;margin-top: 5px;">
					<button id="delete-button">Delete Selected Shape</button>
				</div>
			</div>
		
<!--
		<div class="card">
			<div class="card-body">
				<div class="row align-items-center">
					<div class="col-lg-8 col-md-12">
						<h3 class="card-title">Visitors By Countries</h3>
						<div id="visitfromworld" style="width:100%; height:350px"></div>
					</div>
					<div class="col-lg-4 col-md-12">
						<div class="row mb-15">
							<div class="col-9">India</div>
							<div class="col-3 text-right">28%</div>
							<div class="col-12">
								<div class="progress progress-sm mt-5">
									<div class="progress-bar bg-green" role="progressbar" style="width: 48%" aria-valuenow="48" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>
						</div>
						<div class="row mb-15">
							<div class="col-9"> UK</div>
							<div class="col-3 text-right">21%</div>
							<div class="col-12">
								<div class="progress progress-sm mt-5">
									<div class="progress-bar bg-aqua" role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>
						</div>
						<div class="row mb-15">
							<div class="col-9"> USA</div>
							<div class="col-3 text-right">18%</div>
							<div class="col-12">
								<div class="progress progress-sm mt-5">
									<div class="progress-bar bg-purple" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-9">China</div>
							<div class="col-3 text-right">12%</div>
							<div class="col-12">
								<div class="progress progress-sm mt-5">
									<div class="progress-bar bg-danger" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
-->
	</div>
<!--
	<div class="col-md-4">
		<div class="card" style="min-height: 422px;">
			<div class="card-header"><h3>Donut chart</h3></div>
			<div class="card-body">
				<div id="c3-donut-chart"></div>
			</div>
		</div>
	</div>
-->
</div>
 <script src="https://maps.google.com/maps/api/js?sensor=fasle&key=AIzaSyD2Ku8qGzmjx2k97qDVkWSPION8R0MJTbQ&libraries=places,drawing" type="text/javascript"></script>
        <script>
				$(document).ready(function () {
					var referrer;
					if ($("#previousLink").val() == "") { //alert('pre1');
						referrer = document.referrer;
					} else {
						referrer = $("#previousLink").val();
					}
					if (referrer == "") {
						referrer = "location.php";
					} else {
						$("#backlink").val(referrer);
					}
					$(".back_link").attr('href', referrer);
				});
				function IsEmpty() {
					if ((document.forms['location_form'].tLatitude.value === "") || (document.forms['location_form'].tLongitude.value === ""))
					{
						alert("Please select/draw the area on map shown in right hand side.");
						return false;
					}
					return true;
				}
				var drawingManager;
				var selectedShape;
				function clearSelection() {
					if (selectedShape) {
						if (typeof selectedShape.setEditable == 'function') {
							selectedShape.setEditable(false);
						}
						selectedShape = null;
					}
				}
				function deleteSelectedShape() {
					if (selectedShape) {
						selectedShape.setMap(null);
						$('#tLatitude').val("");
						$('#tLongitude').val("");
					}
				}
				function updateCurSelText(shape) {
					var latt = "";
					var longi = "";
					if (typeof selectedShape.getPath == 'function') {
						for (var i = 0; i < selectedShape.getPath().getLength(); i++) {
							var latlong = selectedShape.getPath().getAt(i).toUrlValue().split(",");
							latt += (latlong[0]) + ",";
							longi += (latlong[1]) + ",";
						}
					}
					$('#tLatitude').val(latt);
					$('#tLongitude').val(longi);
				}
				function setSelection(shape, isNotMarker) {
					clearSelection();
					selectedShape = shape;
					if (isNotMarker)
						shape.setEditable(true);
					updateCurSelText(shape);
				}
				function getGeoCounty(Countryname) {
					var geocoder = new google.maps.Geocoder();
					var address = Countryname;
					var lat, long;
					geocoder.geocode({'address': address}, function (results, status) {
						if (status == google.maps.GeocoderStatus.OK)
						{
							lat = results[0].geometry.location.lat();
							$('#cLatitude').val(lat);
							long = results[0].geometry.location.lng();
							$('#cLongitude').val(long);
							var tlat = $("#tLatitude").val();
							var tlong = $("#tLatitude").val();
							if (tlat == '' && tlong == '') {
								play();
							}
						}
					});
				}
				/////////////////////////////////////
				var map;
				var searchBox;
				var placeMarkers = [];
				var input;
				/////////////////////////////////////
				function initialize() {
					var myLatLng = new google.maps.LatLng("<?= $latitude ?>", "<?= $longitude ?>");
					map = new google.maps.Map(document.getElementById('map-canvas'), {
						zoom: 5,
						center: myLatLng,
						mapTypeId: google.maps.MapTypeId.ROADMAP,
						disableDefaultUI: false,
						zoomControl: true
					});
					var polyOptions = {
						strokeWeight: 0,
						fillOpacity: 0.45,
						editable: true
					};

					drawingManager = new google.maps.drawing.DrawingManager({
						drawingMode: drawingModevalue,
						drawingControl: true,
						drawingControlOptions: {
							position: google.maps.ControlPosition.TOP_RIGHT,
							drawingModes: ['polygon', 'polyline']
						},
						polygonOptions: polyOptions,
						map: map
					});
					google.maps.event.addListener(drawingManager, 'overlaycomplete', function (e) {
						var isNotMarker = (e.type != google.maps.drawing.OverlayType.MARKER);
						drawingManager.setDrawingMode(null);
						var newShape = e.overlay;
						newShape.type = e.type;
						google.maps.event.addListener(newShape, 'click', function () {
							setSelection(newShape, isNotMarker);
						});
						google.maps.event.addListener(newShape, 'drag', function () {
							updateCurSelText(newShape);
						});
						google.maps.event.addListener(newShape, 'dragend', function () {
							updateCurSelText(newShape);
						});
						setSelection(newShape, isNotMarker);
					});
					google.maps.event.addListener(drawingManager, 'drawingmode_changed', clearSelection);
					google.maps.event.addListener(map, 'click', clearSelection);
					google.maps.event.addDomListener(document.getElementById('delete-button'), 'click', deleteSelectedShape);
					google.maps.event.addListener(map, 'bounds_changed', function () {
						var bounds = map.getBounds();
					});
					//~ initSearch(); ============================================
					// Create the search box and link it to the UI element.
					input = /** @type {HTMLInputElement} */(//var
							document.getElementById('pac-input'));
					map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input);
					//searchBox = new google.maps.places.SearchBox((input));
					var autocomplete = new google.maps.places.Autocomplete(input);
					autocomplete.bindTo('bounds', map);
					// Listen for the event fired when the user selects an item from the
					// pick list. Retrieve the matching places for that item.
					var marker = new google.maps.Marker({
						map: map
					});
					autocomplete.addListener('place_changed', function () {
						marker.setVisible(false);
						var place = autocomplete.getPlace();
						if (!place.geometry) {
							window.alert("Autocomplete's returned place contains no geometry");
							return;
						}
						// If the place has a geometry, then present it on a map.
						placeMarkers = [];
						if (place.geometry.viewport) {
							map.fitBounds(place.geometry.viewport);
						} else {
							map.setCenter(place.geometry.location);
							map.setZoom(14);
						}
						// Create a marker for each place.
						marker = new google.maps.Marker({
							map: map,
							title: place.name,
							position: place.geometry.location
						});
						marker.setIcon(({
							url: place.icon,
							size: new google.maps.Size(71, 71),
							origin: new google.maps.Point(0, 0),
							anchor: new google.maps.Point(17, 34),
							scaledSize: new google.maps.Size(25, 25)
						}));
						marker.setVisible(true);
					});
					/*        google.maps.event.addListener(searchBox, 'places_changed', function() {
					 var places = searchBox.getPlaces();

					 if (places.length == 0) {
					 return;
					 }
					 for (var i = 0, marker; marker = placeMarkers[i]; i++) {
					 marker.setMap(null);
					 }

					 // For each place, get the icon, place name, and location.
					 placeMarkers = [];
					 var bounds = new google.maps.LatLngBounds();
					 for (var i = 0, place; place = places[i]; i++) {
					 var image = {
					 url: place.icon,
					 size: new google.maps.Size(71, 71),
					 origin: new google.maps.Point(0, 0),
					 anchor: new google.maps.Point(17, 34),
					 scaledSize: new google.maps.Size(25, 25)
					 };

					 // Create a marker for each place.
					 var marker = new google.maps.Marker({
					 map: map,
					 icon: image,
					 title: place.name,
					 position: place.geometry.location
					 });

					 placeMarkers.push(marker);
					 bounds.extend(place.geometry.location);
					 }

					 map.fitBounds(bounds);
					 map.setZoom(14);
					 });*/
					//~ EndSearch(); ============================================    
					// Polygon Coordinates
					var tLongitude = $('#tLongitude').val();
					var tLatitude = $('#tLatitude').val();
					var Country = $("#iCountry").val();
					if (Country != "" && (tLongitude == "" || tLatitude == "")) {
						getGeoCounty(Country);
						myLatLng = new google.maps.LatLng($("#cLatitude").val(), $("#cLongitude").val());
						map.fitBounds(myLatLng);
					} else {
						if (tLongitude != "" || tLatitude != "") {
							var tlat = tLatitude.split(",");
							var tlong = tLongitude.split(",");
							var triangleCoords = [];
							var bounds = new google.maps.LatLngBounds();
							for (var i = 0, len = tlat.length; i < len; i++) {
								if (tlat[i] != "" || tlong[i] != "") {
									triangleCoords.push(new google.maps.LatLng(tlat[i], tlong[i]));
									var point = new google.maps.LatLng(tlat[i], tlong[i]);
									bounds.extend(point);
								}
							}
							// Styling & Controls
							myPolygon = new google.maps.Polygon({
								paths: triangleCoords,
								draggable: false, // turn off if it gets annoying
								editable: true,
								strokeColor: '#FF0000',
								strokeOpacity: 0.8,
								strokeWeight: 2,
								fillColor: '#FF0000',
								fillOpacity: 0.35
							});
							map.fitBounds(bounds);
							myPolygon.setMap(map);
							//google.maps.event.addListener(myPolygon, "dragend", getPolygonCoords);
							google.maps.event.addListener(myPolygon.getPath(), "insert_at", getPolygonCoords);
							//google.maps.event.addListener(myPolygon.getPath(), "remove_at", getPolygonCoords);
							google.maps.event.addListener(myPolygon.getPath(), "set_at", getPolygonCoords);
							google.maps.event.addDomListener(document.getElementById('delete-button'), 'click', deleteEditShape);
						}
					}
				}
				google.maps.event.addDomListener(window, 'load', initialize);
				function deleteEditShape() {
					if (myPolygon) {
						myPolygon.setMap(null);
					}
					$('#tLatitude').val("");
					$('#tLongitude').val("");
				}
				function play() {
					var pt = new google.maps.LatLng($("#cLatitude").val(), $("#cLongitude").val());
					map.setCenter(pt);
					map.setZoom(5);
				}
				//Display Coordinates below map
				function getPolygonCoords() {
					var len = myPolygon.getPath().getLength();
					var latt = "";
					var longi = "";
					for (var i = 0; i < len; i++) {
						var latlong = myPolygon.getPath().getAt(i).toUrlValue().split(",");
						latt += (latlong[0]) + ",";
						longi += (latlong[1]) + ",";
					}
					$('#tLatitude').val(latt);
					$('#tLongitude').val(longi);
				}

</script>