<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Apartment;
use App\Models\Reservation;
use App\Models\Feature;
use App\Models\User;

class ClientController extends Controller
{
    public function getApartments(Request $request)
	{
		$validator = Validator::make($request->all(), [
				"features"    			=> "required|array|min:1",
				"features.*"  			=> "required|integer|distinct|min:1|exists:features,id"
             ]);
		 if($validator->fails()){

                return response()->json(
                    $this::responseError($validator->errors(),"Error on Validations:")
                ,400);

            }
		$filterFeatures=$request->get('features');
		$apartments = Apartment::where('available',true)
				->whereHas('features', function($q) use($filterFeatures){

    			               $q->whereIn('feature_id', $filterFeatures); //this refers id field from features table

		             })
                 ->with('features:id,name')
				 ->with('user:id,name')->get();
				 
		return response()->json($apartments);
	}
	
	public function requestReservation(Request $request)
	{
		$minbirthdate=$this::getMinDate();
		
		$validator = Validator::make($request->all(), [
				'apartment_id' 			=> 'exists:apartments,id',
				"name"    			=> "required|string|max:200",
				"birthdate"			=> "required|date|date_format:Y-m-d|before_or_equal:".$minbirthdate
             ]);
		 if($validator->fails()){

                return response()->json(
                    $this::responseError($validator->errors(),"Error on Validations:")
                ,400);

            }
		$apartment=Apartment::find($request->get('apartment_id'));
		if($apartment->available===false){
			$error=[
						"statusApartment"=>$apartment->available
						];
					return response()->json(
                    $this::responseError($exception->getMessage(),"The apartment is not available")
					,500);
		}
			
		try{
				$reservation = new Reservation;
				$reservation->apartment_id=$request->get('apartment_id');
				$reservation->name=$request->get('name');
				$reservation->birthdate=$request->get('birthdate');
				$reservation->confirmed=false;
                $reservation->save();
				
								
				
                return  response()->json([
                    'status' => 'ok',
                    'message' => 'Request send'
                ]);
				
            } catch (Exception  $exception) {

                return response()->json(
                    $this::responseError($exception->getMessage(),"Error al registrar")
                ,500);

            }
		
	}
	private function getMinDate(){
		$today = date('Y-m-d'); 
		$eithteenYearsAgo = strtotime ('-18 year' , strtotime($today)); //Se añade un año mas
		$minDate = date ('Y-m-d',$eithteenYearsAgo);
		return $minDate;
	}
	private function responseError($value,$message,$bln=false)
        {
            if ($bln) $value = array("descripcion" => [$value]);
            
             return  ['errors' => $value,'message' => $message];
        }
}
