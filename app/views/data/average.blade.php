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
	Day of Week: <select id="day_of_week">
		<option value="">All</option>
		<option value="Weekday">Weekday</option>
		<option value="Weekend">Weekend</option>
		<option value="Monday" selected>Monday</option>
		<option value="Tuesday">Tuesday</option>
		<option value="Wednesday">Wednesday</option>
		<option value="Thursday">Thursday</option>
		<option value="Friday">Friday</option>
		<option value="Saturday">Saturday</option>
		<option value="Sunday">Sunday</option>
	</select>
	Month: <select id="month">
		<option value="">All</option>
		<option value="January">January</option>
		<option value="February">February</option>
		<option value="March">March</option>
		<option value="April">April</option>
		<option value="May">May</option>
		<option value="June">June</option>
		<option value="July">July</option>
		<option value="August">August</option>
		<option value="September" selected>September</option>
		<option value="October">October</option>
		<option value="November">November</option>
		<option value="December">December</option>
	</select>
	<button onclick="time_dec()"><</button>
	Time: <input type="time" id="time" value="08:30">
	<button onclick="time_inc()">></button>
	Max Median: <span id="median"></span>
	Max Average: <span id="average"></span>
</div>
<div id="map"></div>
<script>

		// This example requires the Visualization library. Include the libraries=visualization
		// parameter when you first load the API. For example:
		// <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=visualization">

		var map, heatmap;


		function time_inc() {
			document.getElementById("time").stepUp(5);
			console.log($("#time").val());
			toggleHeatmap();
		}
		function time_dec() {
			document.getElementById("time").stepDown(5);
			console.log($("#time").val());
			toggleHeatmap();
		}
		$("#day_of_week").change(function () {
			toggleHeatmap();
		});

		function initMap() {
			var day = $("select#day_of_week option:checked").val();
			var time = $("#time").val();
			map = new google.maps.Map(document.getElementById('map'), {
				zoom: 11,
				center: {lat: 47.608625, lng: -122.334036},
				mapTypeId: google.maps.MapTypeId.HYBRID
			});

			$.ajax(
					{
						method: 'POST',
						url: '/data/average_get',
						data: {day: day, time: time},
						success: function (data) {
							console.log(data);
							if(data.length === 0){

							}
							else {
								$('#median').text(data.max_median);
								$('#average').text(data.max_average);
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

		function toggleHeatmap() {
			heatmap.setMap(heatmap.getMap() ? null : map);

			var day = $("select#day_of_week option:checked").val();
			var month = $("select#month option:checked").val();
			var time = $("#time").val();

			$.ajax(
					{
						method: 'POST',
						url: '/data/average_get',
						data: {day: day, month: month, time: time},
						success: function (data) {
							console.log(data);
							if(data.length === 0){

							}
							else {
								$('#median').text(data.max_median);
								$('#average').text(data.max_average);
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