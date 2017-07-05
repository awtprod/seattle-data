<?php

class LocationsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$locations = Locations::all();

		Return View::make('locations.index', ['locations'=>$locations]);
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
		$input = Input::all();

		settype($input["start_lat"], "float");
		settype($input["start_lng"], "float");
		settype($input["end_lat"], "float");
		settype($input["end_lng"], "float");

		$input["end_lat"] -= 0.005;
		$input["end_lng"] += 0.005;

		while ( $input["start_lat"] >= $input["end_lat"]) {

			$start_lng = $input["start_lng"];

			while ($start_lng <= $input["end_lng"]) {

				$location = new Locations;
				$location->lat = $input["start_lat"];
				$location->lng = $start_lng;
				$location->save();


				$start_lng += $input["lng_increment"];

			}

			$input["start_lat"] += $input["lat_increment"];
		}
		Return Redirect::route('locations.index');
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Locations::destroy($id);

		Redirect::route('locations.index');
	}


}
