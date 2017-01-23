<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \DateTime;
use \SimpleXMLElement;


use App\Gasstation;
use App\Pricedata;
use App\Order;

class StationController extends Controller
{
    public function allStations ($format=null) {
    	$stations = Gasstation::all(array('id', 'gasStationLat', 'gasStationLong', 'fuelCompID', 'fuelCompNormalName', 'gasStationOwner', 'ddID', 'ddNormalName', 'municipalityID', 'municipalityNormalName', 'countyID', 'countyName', 'gasStationAddress', 'phone1', 'user_id'));
	
        if($format=="json"){
    		return \Response::json(array(
    			'error' => false,
    			'gasStations' => $stations,
    			'status_code' => 200));
        } else if ($format=="xml") {
            return response()->xml($stations);
        } else {
            return \Response::json(array(
                'error' => false,
                'gasStations' => $stations,
                'status_code' => 200));
        }

    }

    public function count () {

    	$stations = Gasstation::count();
	
		return \Response::json(array(
			'error' => false,
			'count' => $stations,
			'status_code' => 200));
    }

    public function stationData ($gasstation_id=null) {
        if($gasstation_id!=null){
        	$prices = Pricedata::where('gasstation_id','=', $gasstation_id)->get();
        	
        	return \Response::json(array(
        		'error' => false,
        		'prices' => $prices,
        		'status_code' => 200));
        }
        else {
            $prices = Pricedata::all();

            return \Response::json(array(
                'error' => false,
                'prices' => $prices,
                'status_code' => 200));

        }

    }

    public function update ($gasstation_id) {
        try{
            $result = Pricedata::where([['gasstation_id','=', $gasstation_id],
                ['priceDataId','=', $_POST['priceDataId']]])->update(
                ['fuelPrice' => $_POST['value']]);
        } catch (\Exception $e) {
            return  \Response::json(array(
            'error' => true,
            'result' => 0,
            'status_code' => 400));

        }

        if ($result==1) {

            return redirect('/')->with('status', 'Price Changed!');

            /*return  \Response::json(array(
            'error' => false,
            'result' => $result,
            'status_code' => 200));*/
        }

    }

    public function stats () {

        
        $max = Pricedata::where([['fuelTypeID','=', $_GET['fuelTypeID']],
                ['fuelSubTypeID','=', $_GET['fuelSubTypeID']]])->max('fuelPrice');

        $min = Pricedata::where([['fuelTypeID','=', $_GET['fuelTypeID']],
                ['fuelSubTypeID','=', $_GET['fuelSubTypeID']]])->min('fuelPrice');

        $avg = Pricedata::where([['fuelTypeID','=', $_GET['fuelTypeID']],
                ['fuelSubTypeID','=', $_GET['fuelSubTypeID']]])->avg('fuelPrice');

        $stats = [$min, $max, $avg];

    

        return \Response::json(array(
            'error' => false,
            'stats' => $stats,
            'status_code' => 200));

    }

    public function orders ($gasstation_id) {
        $orders = Order::where('id','=', $gasstation_id)->get();

        return \Response::json(array(
            'error' => false,
            'orders' => $orders,
            'status_code' => 200));
        
    }

    public function allStationsXml () {
        $stations = Gasstation::all(array('id', 'gasStationLat', 'gasStationLong', 'fuelCompID', 'fuelCompNormalName', 'gasStationOwner', 'ddID', 'ddNormalName', 'municipalityID', 'municipalityNormalName', 'countyID', 'countyName', 'gasStationAddress', 'phone1', 'user_id'));

      
        return \Response::xml_parse(parser, $stations);

        /*
        return \Response::json(array(
            'error' => false,
            'gasStations' => $stations,
            'status_code' => 200));
        */
    }


}
