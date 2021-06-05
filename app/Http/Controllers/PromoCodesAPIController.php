<?php

namespace App\Http\Controllers;

use App\Models\promo_code;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PromoCodesAPIController extends Controller
{
    //
    public function index(){
        return promo_code::all();
    }
     

    public function create(){

        request()->validate([
            'code_name','amount','address','latitude','longitude','radius'
        ]);

        $period = request('valid_for');
        $date = Carbon::now();
        $validTo = $date->addDays($period);

        $success = promo_code::create([
            'code_name' => request('code_name'),
            'valid_to' => $validTo,
            'amount' => request('amount'),
            'address' => request('address'),
            'longitude' => request('longitude'),
            'latitude' => request('latitude'),
            'radius'=>request('radius')
        ]);
            
        return [
            "msg" => "SUCCESS",
            "data" => $success
        ];
    }

    public function update(promo_code $promoCode){
        try {
            request()->validate([
                'code_name','amount','address','latitude','longitude','radius'
            ]);
        
            $success = $promoCode->update([
                'code_name' => request('code_name'),
                'amount' => request('amount'),
                'address' => request('address'),
                'longitude' => request('longitude'),
                'latitude' => request('latitude'),
                'radius'=>request('radius')
            ]);
        
            return [
                "status" => "SUCCESS",
                "msg" => "Records updated successfully"
            ];
        }
        catch(Exception $e)
        {
            return [
                "status" => "FALSE",
                "msg" => "Error updating records"
            ];
        }
    }

    public function action(promo_code $promoCode){
        request()->validate([
            'status'
        ]);
    
        $stat = request('status');
    
        switch($stat){
            case "1":
                $success = $promoCode->update([
                    'status' => request('status')
                ]);
            
                if($success == "true"){
                    return [
                        "status" => $success,
                        "msg" => "Promo Code succfully activated"
                    ];
                }else{
                    return [
                        "status" => $success,
                        "msg" => "Error activating Promo code"
                    ];
                }
                break;
            case "2":
                $success = $promoCode->update([
                    'status' => request('status')
                ]);
    
                if($success == "true"){
                    return [
                        "status" => $success,
                        "msg" => "Promo Code succfully de-activated"
                    ];
                }else{
                    return [
                        "status" => $success,
                        "msg" => "Error de-activating Promo code"
                    ];
                }
                break;
            case "3":
                $success = $promoCode->update([
                    'status' => request('status')
                ]);
    
                if($success == "true"){
                    return [
                        "status" => $success,
                        "msg" => "Promo Code deleted succfully"
                    ];
                }else{
                    return [
                        "status" => $success,
                        "msg" => "Error deleting Promo code"
                    ];
                }
                break;
            default :
                return [
                        "status" => "false",
                        "msg" => "Invalid values submitted"
                    ];
                break;
        }
    }

    public function single($id){
        try {
            $success = promo_code::findOrFail($id);
            return [
                "status" => "SUCCESS",
                "data" => $success
            ];
        }
        catch(Exception $e)
        {
            return [
                "status" => "FALSE",
                "msg" => "No recordds found"
            ];
        }
    }

    public function active(){
        try {
            $success = promo_code::where('status','1')->get();
            return [
                "status" => "SUCCESS",
                "data" => $success
            ];
        }
        catch(Exception $e)
        {
            return [
                "status" => "FALSE",
                "msg" => "No records found"
            ];
        }
    }

    public function inactive(){
        try {
            $success = promo_code::where('status','2')->get();
            return [
                "status" => "SUCCESS",
                "data" => $success
            ];
        }
        catch(Exception $e)
        {
            return [
                "status" => "FALSE",
                "msg" => "No recordds found"
            ];
        }
    }

    public function trash(){
        try {
            $success = promo_code::where('status','3')->get();
            return [
                "status" => "SUCCESS",
                "data" => $success
            ];
        }
        catch(Exception $e)
        {
            return [
                "status" => "FALSE",
                "msg" => "No recordds found"
            ];
        }
    }

    public function getPromoRide(){

        request()->validate([
            'codeName','origin_latitude','origin_longitude','destination_latitude','destination_longitude'
        ]);

        $codeName = request('codeName');
        $originLat = request('origin_latitude');
        $originLong = request('origin_longitude');
        $destLat = request('destination_latitude');
        $destLong = request('destination_longitude');

        //distance, latitude, longitude" FROM TABLES HAVING distance <= 30;
        $data = promo_code::where('code_name',$codeName)->get();
        try{
            
            $promoLat = $data[0]->latitude;
            $promoLong = $data[0]->longitude;
            $promoStat = $data[0]->status;
            $promoRadius = $data[0]->radius;
            $promoName = $data[0]->code_name;
            $promoAmount = $data[0]->amount;
            $validUntill = $data[0]->valid_to;


            switch($promoStat){
                case "1":
                    if(Carbon::now()->lessThan($validUntill)){
                        $earth_radius = 6371000;
                        $dLat = deg2rad($originLat - $promoLat);
                        $dLon = deg2rad($originLong - $promoLong);

                        $template = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($promoLat)) * cos(deg2rad($originLat)) * sin($dLon/2) * sin($dLon/2);
                        $res = 2 * asin(sqrt($template));
                        $proximity = $earth_radius * $res;

                        if($proximity <= $promoRadius){
                            return [
                                "status" => "SUCCESS",
                                "data" =>[
                                    "codeName" => $promoName,
                                    "amount" => $promoAmount,
                                    "Expiry date" => $validUntill,
                                    "ployLine" => [
                                        ['latitude' => $originLat, 'longitude' => $originLong], 
                                        ['latitude' => $destLat, 'longitude' => $destLong]
                                    ]
                                ]
                            ];

                        }else{
                            $dLat = deg2rad($destLat - $promoLat);
                            $dLon = deg2rad($destLong - $promoLong);
                            
                            $template = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($promoLat)) * cos(deg2rad($originLat)) * sin($dLon/2) * sin($dLon/2);
                            $res = 2 * asin(sqrt($template));
                            $destiny = $earth_radius * $res;

                            if($destiny <= $promoRadius){
                                return [
                                    "status" => "SUCCESS",
                                    "data" =>[
                                        "codeName" => $promoName,
                                        "amount" => $promoAmount,
                                        "Expiry date" => $validUntill,
                                        "ployLine" => [
                                            ['latitude' => $destLat, 'longitude' => $destLong], 
                                            ['latitude' => $originLat, 'longitude' => $originLong]
                                        ]
                                    ]
                                ];
                            }else{
                                return [
                                    "status" => "Alert",
                                    "msg" => "Promo Code is out of range"
                                ];
                            }
                        }
                    }
                    else
                    {
                        return [
                            "status" => "Alert",
                            "msg" => "Promo Code is expired"
                        ];
                    }
                    break;

                case "2":
                    return [
                        "status" => "SUCESS",
                        "msg" => "Promo Code is not available at the moment"
                    ];
                    break;
                case "3":
                    return [
                        "status" => "Alert",
                        "msg" => "Promo Code not found"
                    ];
                    break;
                default:
                    return [
                        "status" => "Alert",
                        "msg" => "Invalid details submitted"
                        ];
                    break;
            }
        }
        catch(Exception $e){
            return [
                "status" => "Error",
                "msg" => "Promo Code not found"
            ];
        }
        
    }
}
