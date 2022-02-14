<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Apartment;
use App\Models\Feature;
use App\Models\Reservation;

class LandlordController extends Controller
{
	public function getApartment($id)
        {
            $apartment = Apartment::find($id);
			$features = $apartment->features;
			
			
			return response()->json($apartment);

        }
		
	public function getMyApartments($id)
	{
		$landlord = User::find($id);
		if($landlord===null){
			return response()->json([
				"Error" =>"El usuario no existe"
			]);
		} 
		$apartments=$landlord->apartments;
		return response()->json($apartments);
	}
	
    public function addApartment(Request $request)
        {
            $validator = Validator::make($request->all(), [
				'landlord_id' 			=> 'exists:users,id',				
                'title'                  => 'required|string|max:200',
				'description'            => 'required|string|max:250',
                'available'                 => 'required|boolean',
				"features"    			=> "required|array|min:1",
				"features.*"  			=> "required|integer|distinct|min:1|exists:features,id",
             ]);
			
            if($validator->fails()){

                return response()->json(
                    $this::responseError($validator->errors(),"Error on Validations:")
                ,400);

            }
			
			
			
            try{
				$apartment = new Apartment;
				$apartment->landlord_id=$request->get('landlord_id');
				$apartment->title=$request->get('title');
				$apartment->available=$request->get('available');
                $apartment->save();
				
				$listFeatures=$request->get('features');
				$feature=Feature::find($listFeatures);
				$apartment->features()->attach($feature);
				
				
                return  response()->json([
                    'status' => 'ok',
                    'message' => 'Apartment Registered!.'
                ]);
				
            } catch (Exception  $exception) {

                return response()->json(
                    $this::responseError($exception->getMessage(),"Error al registrar")
                ,500);

            }

        }
	
			
	public function updateApartment(Request $request,$id)
        {
            $validator = Validator::make($request->all(), [						
                'title'                  => 'required|string|max:200',
				'description'                => 'required|string|max:250',
                'available'                 => 'required|boolean',
				"features"    			=> "required|array|min:1",
				"features.*"  			=> "required|integer|distinct|min:1|exists:features,id",
             ]);

            if($validator->fails()){

                return response()->json(
                    $this::responseError($validator->errors(),"Error on Validations:")
                ,400);

            }
			
			
            try{
				$apartment = Apartment::find($id);
				$apartment->title=$request->get('title');
				$apartment->available=$request->get('available');
                $apartment->save();
				
				$apartment->features()->detach();
				
				$listFeatures=$request->get('features');
				$feature=Feature::find($listFeatures);
				$apartment->features()->attach($feature);
				
				
                return  response()->json([
                    'status' => 'ok',
                    'message' => 'Apartment Updated!.'
                ]);
				
            } catch (Exception  $exception) {

                return response()->json(
                    $this::responseError($exception->getMessage(),"Error on update")
                ,500);

            }

        }
	
	public function confirmReservation($id)
	{
			
			try{
				$reservation=Reservation::find($id);
				$apartment=$reservation->apartment;
				if($apartment->available==false){
					$error=[
						"statusApartment"=>$apartment->available
						];
					return response()->json(
                    $this::responseError($exception->getMessage(),"The apartment is not available")
					,500);
				}
				$reservation->confirmed=true;
				$reservation->save();
				
				$apartment->available=false;
				$apartment->save();
				
			}catch(Exception  $exception) {

                return response()->json(
                    $this::responseError($exception->getMessage(),"Error on update")
                ,500);

            }
			
	}
	
	private function responseError($value,$message,$bln=false)
        {
            if ($bln) $value = array("descripcion" => [$value]);
            
             return  ['errors' => $value,'message' => $message];
        }
}
