<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Locations</title>
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

	<div>

	</div>
	<div class="welcome">
		<h1>Add Locations</h1><p>
			{{ Form::open(array('route' => 'locations.store')) }}
			{{ Form::label('start_lat', 'Start Latitude: ') }}
			{{ Form::text('start_lat') }}&nbsp;
			{{ Form::label('start_lng', 'Start Longitude: ') }}
			{{ Form::text('start_lng') }}<br>
			{{ Form::label('end_lat', 'End Latitude: ') }}
			{{ Form::text('end_lat') }}<br>
			{{ Form::label('end_lng', 'End Longitude: ') }}
			{{ Form::text('end_lng') }}<br>
			{{ Form::label('lat_increment', 'Lat Increment: ') }}
			{{ Form::text('lat_increment') }}<br>
			{{ Form::label('lng_increment', 'Long Increment: ') }}
			{{ Form::text('lng_increment') }}<br>
		{{Form::submit('Submit') }}<p>
		{{ Form::close() }}
		@if(!empty($locations))
			<table>
				<caption>Locations</caption>
				<tr>
					<th>Latitude</th>
					<th>Longitude</th>
					<th>Delete</th>
				</tr>
				@foreach($locations as $location)
					<tr>
						<td>{{$location->lat}}</td>
						<td>{{$location->lng}}</td>
						<td>{{ link_to_route('locations.destroy', 'Delete', $location->id) }}</td>
					</tr>
				@endforeach
			</table>
		@endif
	</div>
</body>
</html>
