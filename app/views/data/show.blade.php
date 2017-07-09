<!DOCTYPE html>
<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

	<meta charset="utf-8">
	<title>Heatmaps</title>
	<style>
		/* Always set the map height explicitly to define the size of the div
         * element that contains the map. */
		#map {
			height: 100%;
		}
		/* Optional: Makes the sample page fill the window. */
		html, body {
			height: 100%;
			margin: 0;
			padding: 0;
		}
		#floating-panel {
			position: absolute;
			top: 10px;
			left: 25%;
			z-index: 5;
			background-color: #fff;
			padding: 5px;
			border: 1px solid #999;
			text-align: center;
			font-family: 'Roboto','sans-serif';
			line-height: 30px;
			padding-left: 10px;
		}
		#floating-panel {
			background-color: #fff;
			border: 1px solid #999;
			left: 25%;
			padding: 5px;
			position: absolute;
			top: 10px;
			z-index: 5;
		}
	</style>
</head>

<body>
<div id="floating-panel">
	Max: <span id="max"></span>
</div>
<div id="map"></div>
<script>

	// This example requires the Visualization library. Include the libraries=visualization
	// parameter when you first load the API. For example:
	// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=visualization">

	var map, heatmap;


	function initMap() {

		map = new google.maps.Map(document.getElementById('map'), {
			zoom: 11,
			center: {lat: 47.608625, lng: -122.334036},
			mapTypeId: google.maps.MapTypeId.HYBRID
		});

		$.ajax(
				{
					method: 'GET',
					url: '/data/live',
					success: function (data) {
						if(data.length === 0){

						}
						else {
							$('#max').text(data.max_data);
							var heatMapData = [];
							//prepare the data
							$.each(data.data, function (i, r) {
								console.log(r[3]);
								heatMapData.push({
									location: new google.maps.LatLng(r[0], r[1]),
									weight: Number(r[2])
								});
							});
							heatmap = new google.maps.visualization.HeatmapLayer({
								data: heatMapData,
								map: map,
								radius: 25
							});
						}
					}
				});

	}




</script>
<script async defer
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC42EWFCy17sNCsQSB0lXPag7KZDeafKXs&libraries=visualization&callback=initMap">
</script>
</body>
</html>