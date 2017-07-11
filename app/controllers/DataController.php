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
/*
		$query = Data::whereBetween('id', [43301,56593])->get();

		foreach ($query as $arr) {

			$time = date('H:i', strtotime($arr->created_at));

				$update = Data::whereId($arr->id)->first();
				$update->day = 'Thursday';
				$update->time = $time;
				$update->save();

		}*/
	}
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
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

			$data_point = new Data;
			$data_point->lat = $location->lat;
			$data_point->lng = $location->lng;
			$data_point->day_of_week = $date->format('l');
			$data_point->time = $time;
			$data_point->lyft_surge = $this->lyft($location);
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
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CAINFO, app_path("/cacert.pem"),
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"authorization: Bearer $token",
				"cache-control: no-cache"
			),
		));

		$response = curl_exec($curl);

		$data = json_decode($response);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return 0;
		}

		$arr = (array) $data;

		return  $arr["cost_estimates"][1]->primetime_percentage;

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
		$day = Carbon::now();
		$data = Data::take(156)->whereDayOfWeek($day->format('l'))->orderBy('time', 'desc')->get();
		if(empty($data[0])){
			$array = array();
		}
		else{
		foreach ($data as $test){

			if($test->lyft_surge > $max){
				$max = $test->lyft_surge;
			}
			$array[] =[
				$test->lat,
				$test->lng,
				$test->lyft_surge,
				$test->time,
				$test->day_of_week
			];
		}
		}
		return Response::json(array('data'=>$array,'max_data'=>$max));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function average()
	{

		Return View::make('data.average');
	}
	public function average_get()
	{
		$day = Input::get('day');
		$time = Input::get('time');

		$locations = Locations::all();

		$data = Data::take(156)->where(function ($query) use ($time, $day) {

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
			foreach ($data as $test) {

				if(($location->lat == $test->lat)AND($location->lng == $test->lng)) {
					$avg_data["total"] += $test->lyft_surge;
					$avg_data["count"]++;
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
				$day,
				$avg_data["total"],
				$avg_data["count"]
			];
		}
			Return Response::json(array('data'=>$array,'max_data'=>$max));

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
