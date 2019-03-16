<?php

namespace App\Http\Controllers;

use App\Models\Makes;
use App\Models\Models;
use App\Models\Parts;
use App\Models\Vehicle;
use App\Models\VehicleParts;
use Illuminate\Http\Request;
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

    public function fillSelect(Request $request) {
        $makeId = $request->make;
        $modelId = $request->model;
        $yearId =  $request->year;
        $selected = $request->selected;

        $data = $this->getSelectValue($makeId,$modelId,$yearId,$selected);

        return response()->json($data);
    }

    public function getSelectValue($makeId = null,$modelId = null,$yearId = null,$selected)    {
        $make = [];
        $model = [];
        $years = [];

        $table = Vehicle::query()
            ->leftJoin('make','vehicle.make_id','=','make.id')
            ->leftJoin('model','vehicle.model_id','=','model.id');

        $year = Vehicle::query()
            ->select('year')
            ->where('vehicle.id','=',$yearId)
            ->first();

        $year = $year->year ?? null;

        if($makeId) {
            if($modelId) {
                $year = $table
                    ->select('vehicle.id','vehicle.year')
                    ->where('make_id','=',$makeId)
                    ->where('model_id','=',$modelId)
                    ->orderBy('year')
                    ->get()
                    ->unique('year');
                return $year;
            }elseif($yearId) {
                $model = $table
                    ->select('model_id','model.name')
                    ->where('make_id','=',$makeId)
                    ->where('year','=',$year)
                    ->get();
                return $model;
            }

            $model = $table
                ->select('model_id','model.name')
                ->where('make_id','=',$makeId)
                ->orWhere('year','=',$year)
                ->groupBy('model_id')
                ->get();

            $years = $table
                ->select('vehicle.id','vehicle.year')
                ->orderBy('year','desc')
                ->get()
                ->unique('year');

        }elseif($modelId) {
            if($makeId && $selected === 'make') {
                $years = $table
                    ->select('vehicle.id','vehicle.year')
                    ->where('make_id','=',$modelId)
                    ->where('model_id','=',$makeId)
                    ->orderBy('year','desc')
                    ->get()
                    ->unique('year');
                return $years;
            }elseif($yearId && $selected === 'year') {
                $make = $table
                    ->select('make_id','make.name')
                    ->where('model_id','=',$modelId)
                    ->where('year','=',$year)
                    ->groupBy('make_id')
                    ->get();
                return $make;
            }
            $make = $table
                ->select('make_id','make.name')
                ->where('model_id','=',$modelId)
                ->orWhere('year','=',$year)
                ->groupBy('make_id')
                ->get();

            $years = $table
                ->select('vehicle.id','vehicle.year')
                ->orderBy('year','desc')
                ->get()
                ->unique('year');
        }elseif($yearId) {
            if($modelId) {
                $make = $table
                    ->select('make_id','make.name')
                    ->where('year','=',$year)
                    ->where('model_id','=',$modelId)
                    ->groupBy();
                return $make;
            }elseif($makeId) {
                $years = $table
                    ->select('vehicle.id','vehicle.year')
                    ->where('year','=',$year)
                    ->where('make_id','=',$makeId)
                    ->orderBy('year','desc')
                    ->get()
                    ->unique('year');
                return $years;
            }

            $model = $table
                ->select('model_id','model.name')
                ->where('year','=',$year)
                ->groupBy('model_id')
                ->get();

            $make = $table
                ->select('make_id','make.name')
                ->distinct()
                ->get();
        }

        return ['make' => $make, 'model' => $model,'year' => $years];
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
