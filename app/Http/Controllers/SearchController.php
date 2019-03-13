<?php

namespace App\Http\Controllers;

use App\Models\Makes;
use App\Models\Models;
use App\Models\Parts;
use App\Models\Vehicle;
use App\Models\VehicleParts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,Parts $parts)
    {
        $this->validate($request,[
            'name' => 'required'
        ]);

        $name = $request->get('name');

        $data = $parts::query()
            ->join('description','part.id','=','description.id')
            ->where('es','like',"%$name%")
            ->orWhere('en','like',"%$name%")
            ->orWhere('part','like',"%$name%")
            ->get();
        if(count($data) != 0) return response()->json($data);
        else return response()->json([]);
    }

    public function fillSelect(Request $request,Makes $make,Models $model,Vehicle $vehicle) {
        $requestData = null;
        $makeId = $request->make;
        $modelId = $request->model;
        $yearId =  $request->year;

        $year = $vehicle::query()
            ->select('year')
            ->where('id','=',$yearId)
            ->get()->first();

        $data = $vehicle::query()
            ->leftJoin('make','vehicle.make_id','=','make.id')
            ->leftJoin('model','vehicle.model_id','=','model.id');

        if($makeId && !$modelId && !$yearId || ($makeId && $modelId && !$yearId)) {
            $models = $data
                ->select('model_id','model.name')
                ->where('make_id','=',$makeId)
                ->groupBy('model_id')
                ->get();
            $years = $data
                ->select('vehicle.id','year')
                ->get();
            $requestData = ['models' => $models, 'years' => $years];
        }elseif($makeId && $modelId && !$yearId) {
            $years = $data
                ->select('vehicle.id','year')
                ->where('make_id','=',$makeId)
                ->orWhere('model_id','=',$modelId)
                ->groupBy('year')
                ->orderBy('year','asc')
                ->get();
            $requestData = ['years' => $years];
        }

        return response()->json($requestData);

    }

    public function reset(Makes $make, Models $model,Vehicle $vehicle) {
        $makes = $make::query()->groupBy('name')->get();
        $models = $model::query()->groupBy('name')->get();
        $years =  $vehicle::query()->select('id', 'year')->groupBy('year')->orderBy('year','desc')->get();
        return response()->json(['makes' => $makes,'models' => $models,'years' => $years]);
    }

    public function showSelected(Request $request,Vehicle $vehicle) {
        $select = $request->input('formData');

        if(isset($select['make'])) {
            $data = $vehicle::query()
                ->where('make_id','=',$select['make'])
                ->with('make','model')
                ->groupBy('model_id')
                ->orderBy('year', 'DESC')
                ->get();
        }elseif(isset($select['model'])) {
            $data = $vehicle::query()
                ->where('model_id','=',$select['model'])
                ->with('make','model')
                ->orderBy('year', 'DESC')
                ->get();

        }elseif(isset($select['year'])) {
            $year = $vehicle::query()
                ->where('id','=',$select['year'])
                ->get()
                ->first();
            $data = $vehicle::query()
                ->where('year','=',$year['year'])
                ->with('make','model')
                ->groupBy('model_id')
                ->orderBy('year', 'DESC')
                ->get();
        }
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(is_numeric($id)) {
            $data = Vehicle::query()
                ->where('id','=',$id)
                ->with('make','model')
                ->get()
                ->first();
            return view('product.show',compact('data'));
        }
    }


    public function showPart($id) {
        $data = Parts::query()
            ->join('description','part.id','=','description.id')
            ->where('part.id','=',$id)
            ->get()
            ->first();
        return view('product.showPart',compact('data'));
    }

    public function showVehiclePart(VehicleParts $vehicleParts,$id) {
        $vehicle = $vehicleParts::query()
            ->where('vehicle_id','=',$id)
            ->leftJoin('part','vehicle_part.part_id','=','part.id')
            ->join('description','part.description_id','=','description.id')
            ->get();
        return response()->json($vehicle);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
