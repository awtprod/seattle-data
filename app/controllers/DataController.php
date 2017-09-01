<?php
use Carbon\Carbon;
class DataController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

			$end = Data::orderBy('id', 'desc')->pluck('id');

			$start = 1;
			$first = true;
			while($start <= $end) {

				$query = Data::whereId($start)->first();


				if(count($query)>0) {

					if ($first == 'true') {

						$time = new Time;
						$time->month = 'July';
						$time->day_of_week = $query->day_of_week;
						$time->time = $query->time;
						$time->weather = $query->weather;
						$time->precip_tot = $query->precip_tot;
						$time->precip_hr = $query->precip_hr;
						$time->wndspd = $query->wndspd;
						$time->temp = $query->temp;
						$time->save();

						$current_time = $query->time;

						$first = false;
					}

					if ($query->time != $current_time) {


						$time = new Time;
						$time->month = 'July';
						$time->day_of_week = $query->day_of_week;
						$time->time = $query->time;
						$time->weather = $query->weather;
						$time->precip_tot = $query->precip_tot;
						$time->precip_hr = $query->precip_hr;
						$time->wndspd = $query->wndspd;
						$time->temp = $query->temp;
						$time->save();

						$current_time = $query->time;

					}
					$location = Locations::whereLat($query->lat)->whereLng($query->lng)->first();

					$query->loc_id = $location->id;
					$query->time_id = $time->id;
					$query->save();

				}
				$start++;

			}
			}
	public function calculate_median($arr) {

		sort($arr);
		$count = count($arr); //total numbers in array
		$middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
		if($count % 2) { // odd number, middle is the median
			$median = $arr[$middleval];
		} else { // even number, calculate avg of 2 medians
			$low = $arr[$middleval];
			$high = $arr[$middleval+1];
			$median = (($low+$high)/2);
		}
		return $median;
	}
	public function calculate_average($arr) {
		$count = count($arr); //total numbers in array
		$total = 0;
		foreach ($arr as $value) {
			$total = $total + $value; // total value of array numbers
		}
		$average = ($total/$count); // get average value
		return $average;
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$locations = Locations::all();

		$cur_obs = $this->cur_obs();

		$date = Carbon::now();
		$time = date('H:i', strtotime($date));

		foreach ($locations as $location){

			$primetime = $this->lyft($location);

			$data_point = new Data;
			$data_point->lat = $location->lat;
			$data_point->lng = $location->lng;
			$data_point->day_of_week = $date->format('l');
			$data_point->month = $date->format('F');
			$data_point->time = $time;
			$data_point->lyft_surge = $primetime["lyft"];
			$data_point->line_surge = $primetime["lyft_line"];
			$data_point->weather = $cur_obs->weather;
			$data_point->precip_tot = $cur_obs->precip_today_in;
			$data_point->precip_hr = $cur_obs->precip_1hr_in;
			$data_point->wndspd = $cur_obs->wind_mph;
			$data_point->temp = $cur_obs->temp_f;
			$data_point->save();
		}

	}

	public function lyft($location)
	{
		$token = Tokens::whereService('lyft')->pluck('token');

		// Address input
		$lat = urlencode($location->lat);
		$lng = urlencode($location->lng);

// Build the URL
		$req = "https://api.lyft.com/v1/cost?start_lat={$lat}&start_lng={$lng}";

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $req,
			CURLOPT_FRESH_CONNECT => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CAINFO, app_path("/cacert.pem"),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"authorization: Bearer $token",
				1
			),
			CURLOPT_RETURNTRANSFER => 1
		));

		$response = curl_exec($curl);

		$data = json_decode($response);

		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return 0;
		}

		$arr = (array) $data;

		$primetime = array();
		foreach ($arr["cost_estimates"] as $type){

			if($type->ride_type =="lyft_line"){
				$primetime["lyft_line"] = $type->primetime_percentage;
			}
			if($type->ride_type =="lyft"){
				$primetime["lyft"] = $type->primetime_percentage;
			}
		}

		return  $primetime;

	}

	public function uber($location)
	{

		set_time_limit(240);
		$token = Tokens::whereService('uber')->pluck('token');

		// Address input
		$lat = urlencode($location->lat);
		$lng = urlencode($location->lng);

// Build the URL
		$req = "https://api.uber.com/v1.2/estimates/price?start_latitude={$lat}&start_longitude={$lng}&end_latitude={$lat}&end_longitude={$lng}";

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $req,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"authorization: Token $token",
				'Accept-Language: en_US',
				'Content-Type: application/json',
				"cache-control: no-cache"
			),
		));

		$response = curl_exec($curl);

		$data = json_decode($response);

		$err = curl_error($curl);

		curl_close($curl);
		if(!empty($data->prices[0]->surge_multiplier)){
			return ($data->prices[0]->surge_multiplier);
		}
		else {
			return ($data->prices[0]->low_estimate)/4;
		}
		if ($err) {
			return 1;
		}

	/*	$arr = (array) $data;



		if(!empty($arr["prices"][0]->surge_multiplier)){

			return $arr["prices"][0]->surge_multiplier;

		}
		else{

			return 1;

		} */
	}

	public function cur_obs()
	{
// Build the URL
		$req = "http://api.wunderground.com/api/342d6b143d0aafe7/conditions/q/WA/Seattle.json";

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $req,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
				"cache-control: no-cache"
			),
		));

		$response = curl_exec($curl);

		$data = json_decode($response);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return false;
		}

		$arr = (array) $data;

		return $arr["current_observation"];

	}
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show()
	{
		Return View::make('data.show');
	}

	public function live()
	{
		$max = 0;
		$data = Data::take(157)->orderBy('created_at', 'desc')->get();
		if(empty($data[0])){
			$array = array();
		}
		else{
		foreach ($data as $test){

			if($test->lyft_surge > $max){
				$max = $test->lyft_surge;
			}

			if($test->lyft_surge > 0) {
				$array[] = [
					$test->lat,
					$test->lng,
					$test->lyft_surge,
					$test->time,
					$test->day_of_week
				];
			}
		}
		}
		if($max == 0){
			Return Response::json(array('data' => array(), 'max_data' => $max));

		}
		else {

			Return Response::json(array('data' => $array, 'max_data' => $max));
		}	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function predict()
	{
		$date = Carbon::now();
		$hr = date('H', strtotime($date));
		$min = date('i', strtotime($date));

		$min += 5;
		$data = Data::whereBetween('time', array('13:04', '13:30'))->get();
		print_r($data);
		//Return View::make('data.predict');
	}
	public function predict_get()
	{
		$day = Input::get('day');
		$time = Input::get('time');

		$locations = Locations::all();

		$data = Data::where(function ($query) use ($time, $day) {

			$query->where('time', '=', $time);

			if($day == 'Weekday') {
				$query->whereIn('day_of_week', array('Monday','Tuesday','Wednesday','Thursday','Friday'));
			}
			elseif($day == 'Weekend') {
				$query->whereIn('day_of_week', array('Saturday','Sunday'));
			}
			elseif(!empty($day)){
				$query->where('day_of_week','=',$day);
			}
		})->get();

		$max = 0;
		foreach ($locations as $location) {
			$avg_data = array();
			$avg_data["total"] = 0;
			$avg_data["count"] = 0;
			foreach ($data as $key => $instance) {

				if(($location->lat == $instance->lat)AND($location->lng == $instance->lng)) {
					$avg_data["total"] += $instance->lyft_surge;
					$avg_data["count"]++;
					unset($data[$key]);
				}

			}
			if($avg_data["count"]==0){
				$average = 0;
			}
			else{
				$average = ($avg_data["total"]/$avg_data["count"]);
				if($average > $max){

					$max = $average;
				}
			}

			$array[] = [
				$location->lat,
				$location->lng,
				$average,
				$time,
				$day
			];
		}
		if($max == 0){
			Return Response::json(array('data' => array(), 'max_data' => $max));

		}
		else {

			Return Response::json(array('data' => $array, 'max_data' => $max));
		}

	}
	public function airport(){
		$data = Data::where(function ($query)  {
			$query->whereIn('lat', array('47.44456000','47.44234000'));
			$query->where('lyft_surge','>','0');
		})->get();

foreach ($data as $time){
	echo $time->lat.'<br>';
	echo $time->lng.'<br>';
	echo $time->day_of_week.'<br>';
	echo $time->time.'<br>';
	echo $time->lyft_surge.'<p>';

}
		}
	public function average()
	{

		Return View::make('data.average');
	}
	public function average_get()
	{
		$day = Input::get('day');
		$time = Input::get('time');

		$locations = Locations::all();

		$data = Data::where(function ($query) use ($time, $day) {

			$query->where('time', '=', $time);

			if($day == 'Weekday') {
				$query->whereIn('day_of_week', array('Monday','Tuesday','Wednesday','Thursday','Friday'));
			}
			elseif($day == 'Weekend') {
				$query->whereIn('day_of_week', array('Saturday','Sunday'));
			}
			elseif(!empty($day)){
				$query->where('day_of_week','=',$day);
			}
		})->get();

		$max_average = 0;
		$max_median = 0;
		foreach ($locations as $location) {
			$median_data = array();
			$count = 0;
			foreach ($data as $key => $instance) {

				if(($location->lat == $instance->lat)AND($location->lng == $instance->lng)) {
					$median_data[] = $instance->lyft_surge;
					$count++;
					unset($data[$key]);

				}

			}
			if($count==0){
				$median = 0;
			}
			else{
				$median = $this->calculate_median($median_data);
				if($median > $max_median){

					$max_median = $median;
				}
				$average = $this->calculate_average($median_data);
				if($average > $max_average){
					$max_average = $average;
				}
				if($median != 0) {
					$array[] = [
						$location->lat,
						$location->lng,
						$median,
						$median_data,
						$time,
						$day
					];
				}
			}


		}
		if($max_median == 0){
			Return Response::json(array('data' => array(), 'max_median' => $max_median, 'max_average' => $max_average));

		}
		else {

			Return Response::json(array('data' => $array, 'max_median' => $max_median, 'max_average' => $max_average));
		}

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{

	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{

	}


}
