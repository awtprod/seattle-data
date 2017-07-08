<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Locations</title>
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
<div id="map_canvas" style="height:600px;width: 600px"></div>
<script>
	function initialize() {
		var mapOptions = {
			zoom: 10,
			opacity: 0.1,
			center: new google.maps.LatLng(47.608625,-122.334036),
			mapTypeId: google.maps.MapTypeId.HYBRID
		};
		map = new google.maps.Map(document.getElementById('map_canvas'),
				mapOptions);

		//query the fusiontable via ajax
		$.ajax(
				{
					method: 'GET',
					url     : '/data/live',
					success:  function(data){

						var heatMapData=[];
						//prepare the data
						$.each(data,function(i,r){
							console.log(r[0],r[1],r[2],r[3]);
							heatMapData.push({
								location:new google.maps.LatLng(r[0],r[1]),
								weight:Number(r[2])
							});
						});
						//create the weighted heatmap
						new google.maps.visualization.HeatmapLayer({
							data: heatMapData,map:map,radius: 20
						});
					}
				});
	}

	google.maps.event.addDomListener(window, 'load', initialize);
</script>
</body>
</html>
