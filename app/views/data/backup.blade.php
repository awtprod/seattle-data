<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Locations</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC42EWFCy17sNCsQSB0lXPag7KZDeafKXs&v=3.exp&libraries=visualization&.js"></script>
	<style>
		@import url(//fonts.googleapis.com/css?family=Lato:700);

		body {
			margin:0;
			font-family:'Lato', sans-serif;
			text-align:center;
			color: #999;
		}

		.welcome {
			width: 300px;
			height: 200px;
			position: absolute;
			left: 50%;
			top: 50%;
			margin-left: -150px;
			margin-top: -100px;
		}

		a, a:visited {
			text-decoration:none;
		}

		h1 {
			font-size: 32px;
			margin: 16px 0 0 0;
		}
		table, th, td {
			border: 1px solid black;
			border-collapse: collapse;
		}
		th, td {
			padding: 5px;
			text-align: left;
		}
	</style>
</head>
<body>
<div id="data_params">
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
	<button onclick="time_dec()"><</button>
	Time: <input type="time" id="time" value="10:49">
	<button onclick="time_inc()">></button>
</div>
<div id="map_canvas" style="height:600px;width: 600px"></div>
<script>
	function time_inc() {
		document.getElementById("time").stepUp(5);
	}
	function time_dec() {
		document.getElementById("time").stepDown(5);
	}
	var getUrlParameter = function getUrlParameter(sParam) {
		var sPageURL = decodeURIComponent(window.location.search.substring(1)),
				sURLVariables = sPageURL.split('&'),
				sParameterName,
				i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : sParameterName[1];
			}
		}
	};
	var mapOptions = {
		zoom: 10,
		opacity: 0.1,
		center: new google.maps.LatLng(47.608625,-122.334036),
		mapTypeId: google.maps.MapTypeId.HYBRID
	};
	map = new google.maps.Map(document.getElementById('map_canvas'),
			mapOptions);

	var heatmap;

	function initialize(day, time) {
		//query the fusiontable via ajax
		$.ajax(
				{
					method: 'POST',
					url: '/data/average_get',
					data: {day: day, time: time},
					success:  function(data){
						var heatMapData=[];
						//prepare the data
						$.each(data,function(i,r){
							heatMapData.push({
								location:new google.maps.LatLng(r[0],r[1]),
								weight:Number(r[2])
							});
						});
						//create the weighted heatmap
						heatmap = new google.maps.visualization.HeatmapLayer({
							data: heatMapData,map:map, radius: 25
						});
					}
				});
	}
	function clearMap() {
		heatmap.setMap(null);
	}
	function update(day, time) {
		heatmap.setMap(null);

		initialize(day, time)

	}

initialize($("select#day_of_week option:checked").val(),$("#time").val());
clearMap();

	$('#data_params').bind("DOMSubtreeModified",function(){
		update($("select#day_of_week option:checked").val(),$("#time").val());
	});
</script>
</body>
</html>
