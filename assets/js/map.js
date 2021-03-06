/**
 * Initialize Google Maps API
 */
function initialize(){
	var el = document.getElementById("map");

	if ( el === null || el === undefined ) return;

	// Initialize map instance and div selection
	var map = new google.maps.Map(el, {zoom: 16, disableDefaultUI: true, scrollwheel: false});

	// Initialize geographical location
	var geocoder = new google.maps.Geocoder();

	// Custom icon
	var iconMarker = new google.maps.MarkerImage(google_maps_data.icon, null, null, new google.maps.Point(35, 70), new google.maps.Size(70, 70));

	// Initialize custom icon
	var marker = new google.maps.Marker({
		map: map,
		draggable: false,
		icon: iconMarker,
		position: null
	});
	//Transforming address in geographic coordinates
	if(google_maps_data.latitude && google_maps_data.longitude){
		var position = new google.maps.LatLng(google_maps_data.latitude, google_maps_data.longitude);
		marker.setPosition(position);
		map.setCenter(position);
	}else{
		geocoder.geocode({ 'address': google_maps_data.address }, function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				if (results[0]) {
					var lat = results[0].geometry.location.lat();
					var lng = results[0].geometry.location.lng();

					var position = new google.maps.LatLng(lat, lng);
					marker.setPosition(position);
					map.setCenter(position);
				}
			}
		});
	}
}
google.maps.event.addDomListener(window, 'load', initialize);