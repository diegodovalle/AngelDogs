   $(document).ready(function () {

		   //Google Map

			var mapCanvas = document.getElementById('map-canvas');
			var myLatlng = new google.maps.LatLng(-22.969778, -43.186859); //your latitude and longitude here
			var mapOptions = {
				zoom: 17,
				scrollwheel: false,
				center: myLatlng,
				styles:[ { "featureType": "administrative.country", "elementType": "geometry.fill", "stylers": [ { "visibility": "on" } ] }, { "featureType": "administrative.country", "elementType": "labels.text.fill", "stylers": [ { "visibility": "on" } ] }, { "featureType": "poi", "elementType": "all", "stylers": [ { "visibility": "off" } ] }, { "featureType": "road", "elementType": "all", "stylers": [ { "visibility": "on" } ] }, { "featureType": "transit", "elementType": "all", "stylers": [ { "visibility": "on" } ] }, { "featureType": "water", "elementType": "all", "stylers": [ { "visibility": "on" } ] } ]
			}
			var map = new google.maps.Map(mapCanvas, mapOptions)
			var marker = new google.maps.Marker({
				position: myLatlng,
				icon: 'img/mapmarker.png',
				map: map,
				title: ''
			});
		});