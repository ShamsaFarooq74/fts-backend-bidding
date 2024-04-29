<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\api\ResponseController;
use App\Models\FuelType;
use App\Models\VehicleType;
use App\Models\VehicleClass;
use App\Helpers\FileHelper;

class SettingsController extends ResponseController
{
    public function addfuel(Request $request, $fuelId = ''){
        if ($fuelId) {
            $validator = Validator::make($request->all(), [
                'fuel' => 'required|string',
                'status' => 'required|string',
            ]);
            
            if ($validator->fails()) {
                $error = $validator->errors()->all();
                return $this->sendError(0,$error, null, 400);
            }
            $fuel = FuelType::find($fuelId);
            if (!$fuel) {
                $error =  'Fuel not found';
                return $this->sendError(0,$error, null, 404);
            }
            $fuel->fuel_type = $request->fuel;
            $fuel->is_active = $request->status;
            $fuel->save();
            $status = 1;
            $message = 'Fuel updated successfully';
           return $this->sendResponse($status, $message, 200);
        } else {
            $validator = Validator::make($request->all(), [
                'fuel' => 'required|string',
                'status' => 'required|string',
            ]);
            if ($validator->fails()) {
                 $error =  $validator->errors()->first();
                return $this->sendError(0,$error, null, 400);
            }

            $checkIfDeleted = FuelType::where('fuel_type', $request->fuel)->first();
            if ($checkIfDeleted && $checkIfDeleted->is_deleted == 'Y') {
                $checkIfDeleted->is_deleted = 'N';
                $checkIfDeleted->save();
                $status = 1;
                $message = 'Fuel added successfully';
                return $this->sendResponse($status, $message, 200);
            }

            if ($checkIfDeleted && $checkIfDeleted->is_deleted == 'N') {
                 $error  =  'Fuel is already taken';
                 return $this->sendError(0,$error, null, 400);
            }

            $fuel = new FuelType();
            $fuel->fuel_type = $request->fuel;
            $fuel->is_active = $request->status;
            $fuel->save();
            $status = $fuel->is_active ;
            $message  =  'Fuel added successfully';
            return $this->sendResponse($status, $message, 200);

        }
    }
    public function fuelList($fuelId = null){
        $fuelType = FuelType::where('is_deleted', 'N')->orderBy('created_at', 'DESC');
        if ($fuelId !== null) {
          $fuelType->where('id', $fuelId);
        }
        $fuel = $fuelType->get();
        if($fuel->isEmpty()){
           $message = "Record is not found";
        }else{
            $message = 'Fuel Type Fetched Successfully.';
        }
        $status = 1;
        return $this->sendResponse($status, $message, $fuel);
    }
    public function deleteFuel(Request $request){
       $fuel = FuelType::where('id', $request->id)->first();
        $fuel->is_deleted = 'Y';
        $fuel->update();
        if ($fuel) {
             $status = 1;
             $message = 'Fuel Deleted successfully';
             return $this->sendResponse($status, $message, 200);
        } else {
          $error = 'Something Wrong';
         return $this->sendError(0,$error, null, 400);
        }
    }
    public function addVehicleType(Request $request, $vehicleTypeId = ''){
        if ($vehicleTypeId) {
            $validator = Validator::make($request->all(), [
                'vehicle_type' => 'required|string',
                'status' => 'required|string',
            ]);
             
            if ($validator->fails()) {
                $error = $validator->errors()->all();
                return $this->sendError(0,$error, null, 400);
            }
            $vehicleType = VehicleType::where('id', $vehicleTypeId)->first();
            $vehicleType->vehicle_type = $request->vehicle_type;
            $vehicleType->is_active = $request->status;
            $vehicleType->save();
            $status = 1;
            $message = 'Vehicle Type updated successfully';
            return $this->sendResponse($status, $message, 200);
        } else {
            $validator = Validator::make($request->all(), [
                'vehicle_type' => 'required',
                'status' => 'required',
            ]);
            if ($validator->fails()) {
                 $error =  $validator->errors()->first();
                return $this->sendError(0,$error, null, 400);
            }
            $checkIfDeleted = VehicleType::where('vehicle_type', $request->vehicle_type)->first();
            if ($checkIfDeleted && $checkIfDeleted->is_deleted == 'Y') {
                $checkIfDeleted->is_deleted = 'N';
                $checkIfDeleted->save();
                $status = 1;
                $message = 'Vehicle Type added successfully';
                return $this->sendResponse($status, $message, 200);
            }

            if ($checkIfDeleted && $checkIfDeleted->is_deleted == 'N') {
                 $error = 'Vehicle Type is already taken';
                 return $this->sendError(0,$error, null, 400);
            }

            $vehicleType = new VehicleType();
            $vehicleType->vehicle_type = $request->vehicle_type;
            $vehicleType->is_active = $request->status;
            $vehicleType->save();
            $status = $vehicleType->is_active ;
            $message = 'Vehicle Type added successfully';
            return $this->sendResponse($status, $message, 200);

        }
    }
    public function deleteVehicleType(Request $request){
        $vehicleType = VehicleType::where('id', $request->id)->first();
        $vehicleType->is_deleted = 'Y';
        $vehicleType->update();
        if ($vehicleType) {
                $status = 1;
                $message = 'Vehicle Type Deleted successfully';
                return $this->sendResponse($status, $message, 200);
        } else {
            $error = 'Something Wrong';
            return $this->sendError(0,$error, null, 400);
        }
    }
    public function vehicleTypeList($vehicleTypeId = null){
        $vehicleTypes = VehicleType::where('is_deleted', 'N')->orderBy('created_at', 'DESC');
        if($vehicleTypeId != null){
          $vehicleTypes = VehicleType::where('id', $vehicleTypeId);
        }
        $vehicleType= $vehicleTypes->get();
        if($vehicleType->isEmpty()){
         $message = "Record is not found";
        }else{
         $message = "All vehicle types fetched successfully.";
        }
        $status = 1;
        return $this->sendResponse($status, $message, $vehicleType);
    }
    public function addVehicleClass(Request $request, $vehicleClassId = '')
    {
        if ($vehicleClassId) {
            $validator = Validator::make($request->all(), [
                'vehicle_type_id' => 'required',
                'vehicle_class' => 'required',
                'status' => 'required',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                return $this->sendError(0,$error, null, 400);
            }
            $vehicleClass = VehicleClass::where('id', $vehicleClassId)->first();
            $vehicleClass->vehicle_type_id = $request->vehicle_type_id;
            $vehicleClass->vehicle_class = $request->vehicle_class;
            $vehicleClass->is_active = $request->status;
            if ($request->file('vehicle_thumbnail')) {
                $file = $request->file('vehicle_thumbnail');
                $vehicleThumbnail = FileHelper::uploadFile($file, '/images/settings/vehicle-img/');
                $vehicleClass->thumbnail = $vehicleThumbnail;
            }
            $vehicleClass->update();
            if ($vehicleClass) {
                $status = 1;
                $message = 'Vehicle Class updated successfully';
                return $this->sendResponse($status, $message, 200);
            } else {
                 $error = 'Sorry! Vehicle Class not updated';
                 return $this->sendError(0,$error, null, 400);
            }
        } else {
            $validator = Validator::make($request->all(), [
                 'vehicle_type_id' => 'required',
                 'vehicle_class' => 'required',
                 'status' => 'required',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return $this->sendError(0,$error, null, 400);
            }
            $getVehicle = VehicleClass::where('vehicle_type_id',
            $request->vehicle_type_id)->where('vehicle_class',$request->vehicle_class)->first();
            if($getVehicle !=""){
                  $error = 'Vehicle Class is Already Taken!';
                  return $this->sendError(0,$error, null, 400);
            }

            $vehicleClass = new VehicleClass();
            $vehicleClass->vehicle_type_id = $request->vehicle_type_id;
            $vehicleClass->vehicle_class = $request->vehicle_class;
            $vehicleClass->is_active = $request->status;
            if ($request->file('vehicle_thumbnail')) {
                $file = $request->file('vehicle_thumbnail');
                $vehicleThumbnail = FileHelper::uploadFile($file, '/images/settings/vehicle-img/');
                $vehicleClass->thumbnail = $vehicleThumbnail;
            }
            $vehicleClass->save();
            if ($vehicleClass) {
                $status = 1;
                $message = 'Vehicle Class added successfully';
                return $this->sendResponse($status, $message, 200);
            } else {
                $error = 'Sorry! Vehicle Class not added';
                return $this->sendError(0,$error, null, 400);
            }
        }
    }
    public function deleteVehicleClass(Request $request){
        $vehicleClass = VehicleClass::where('id', $request->id)->first();
        $vehicleClass->is_deleted = 'Y';
        $vehicleClass->update();
        if ($vehicleClass) {
                $status = 1;
                $message = 'Vehicle Class Deleted successfully';
                return $this->sendResponse($status, $message, 200);
        } else {
            $error = 'Something Wrong';
            return $this->sendError(0,$error, null, 400);
        }
    } 
    public function vehicleClassList($vehicleclassId = null)
    {
          $vehicleTypes = VehicleType::where('is_deleted', 'N')->where('is_active', '1')->get();
          $vehicleClassQuery = VehicleClass::whereHas('vehicleType', function ($query) {
          $query->where('is_deleted', 'N')->where('is_active', '1'); })->where('is_deleted', 'N')->orderBy('created_at','DESC');

          if ($vehicleclassId !== null) {
          $vehicleClassQuery->where('id', $vehicleclassId);
          }

          $vehicleClass = $vehicleClassQuery->get();
        // $vahicalType = VehicleType::where('is_deleted', 'N')->where('is_active', '1')->get();
        // $vehicleClass = VehicleClass::whereHas('vehicleType', function ($query) {
        // $query->where('is_deleted', 'N')->where('is_active', '1');})->where('is_deleted', 'N')->orderBy('created_at','DESC')->get();
        for ($i = 0; $i < count($vehicleClass); $i++) {
            $vehicleClass[$i]['vehicleType'] = VehicleType::where('id', $vehicleClass[$i]['vehicle_type_id'])->first()['vehicleType'];
          
            if (empty($vehicleClass[$i]['thumbnail'])) {
                $vehicleClass[$i]['thumbnail'] = 'xyz';
            }
            $file = public_path() . '/images/settings/vehicle-img/' . $vehicleClass[$i]['thumbnail'];
            if (!empty($vehicleClass[$i]['thumbnail']) && file_exists($file)) {
              $vehicleClass[$i]['thumbnail'] = FileHelper::getImageUrl($vehicleClass[$i]['thumbnail'], 'ModelThumbnail');
            } else {
              $vehicleClass[$i]['thumbnail'] = FileHelper::getImageUrl('vehicle-icon.png', 'dummyImg');
            }
        }
        if($vehicleClass->isEmpty()){
            $message = "Record is not found";
        }else{
             $message = "Vehicle Class fetched successfully.";
        }
         $status = 1;
         return $this->sendResponse($status, $message, $vehicleClass);
    }
    public function addDocumentType(Request $request, $documentTypeId = ''){
        if ($documentTypeId) {
            $validator = Validator::make($request->all(), [
                'document_type' => 'required|string',
                'status' => 'required',
            ]);
            
            if ($validator->fails()) {
                $error = $validator->errors()->all();
                return $this->sendError(0,$error, null, 400);
            }
            $document = DocumentType::find($documentTypeId);
            if (!$document) {
                $error =  'Fuel not found';
                return $this->sendError(0,$error, null, 404);
            }
            $document->document_type = $request->document_type;
            $document->is_active = $request->status;
            $document->save();
            $status = 1;
            $message = 'Document Type updated successfully';
           return $this->sendResponse($status, $message, 200);
        } else {
            $validator = Validator::make($request->all(), [
                'document_type' => 'required|string',
                'status' => 'required',
            ]);
            if ($validator->fails()) {
                 $error =  $validator->errors()->first();
                return $this->sendError(0,$error, null, 400);
            }

            $checkIfDeleted = DocumentType::where('document_type', $request->document_type)->first();
            if ($checkIfDeleted && $checkIfDeleted->is_deleted == 'Y') {
                $checkIfDeleted->is_deleted = 'N';
                $checkIfDeleted->save();
                $status = 1;
                $message = 'Document Type added successfully';
                return $this->sendResponse($status, $message, 200);
            }

            if ($checkIfDeleted && $checkIfDeleted->is_deleted == 'N') {
                 $error  =  'Document is already taken';
                 return $this->sendError(0,$error, null, 400);
            }

            $document = new DocumentType();
            $document->fuel_type = $request->fuel;
            $document->is_active = $request->status;
            $document->save();
            $status = $document->is_active ;
            $message  =  'Fuel added successfully';
            return $this->sendResponse($status, $message, 200);

        }
    }

    public function discountCard(Request $request, $discountCardId = ''){
        if ($discountCardId) {
            $validator = Validator::make($request->all(), [
                'dicount_percentage' => 'required',
                'usage_limit' => 'required|date',
                'code' => 'required',
                'status' => 'required',
            ]);
            
            if ($validator->fails()) {
                $error = $validator->errors()->all();
                return $this->sendError(0,$error, null, 400);
            }
            $discountCard = DiscountCard::find($documentTypeId);
            if (!$discountCard) {
                $error =  'Fuel not found';
                return $this->sendError(0,$error, null, 404);
            }
            $discountCard->dicount_percentage = $request->dicount_percentage;
            $discountCard->usage_limit = $request->usage_limit;
            $discountCard->code = $request->code;
            $discountCard->is_active = $request->status;
            $discountCard->save();
            $status = 1;
            $message = 'DiscountCard updated successfully';
           return $this->sendResponse($status, $message, 200);
        } else {
            $validator = Validator::make($request->all(), [
                'document_type' => 'required|string',
                'status' => 'required|string',
            ]);
            if ($validator->fails()) {
                 $error =  $validator->errors()->first();
                return $this->sendError(0,$error, null, 400);
            }

            $checkIfDeleted = DocumentType::where('document_type', $request->document_type)->first();
            if ($checkIfDeleted && $checkIfDeleted->is_deleted == 'Y') {
                $checkIfDeleted->is_deleted = 'N';
                $checkIfDeleted->save();
                $status = 1;
                $message = 'Document Type added successfully';
                return $this->sendResponse($status, $message, 200);
            }

            if ($checkIfDeleted && $checkIfDeleted->is_deleted == 'N') {
                 $error  =  'Document is already taken';
                 return $this->sendError(0,$error, null, 400);
            }

            $document = new DocumentType();
            $document->fuel_type = $request->fuel;
            $document->is_active = $request->status;
            $document->save();
            $status = $document->is_active ;
            $message  =  'Fuel added successfully';
            return $this->sendResponse($status, $message, 200);

        }
    }
    
}
