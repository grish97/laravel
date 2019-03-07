<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Makes;
use App\Models\Models;
use App\Models\Parts;
use App\Models\Vehicle;
use App\Models\Descriptions;
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

        $data = $parts::query()->join('description','part.id','=','description.id')
            ->where('es','like',"%$name%")
            ->orWhere('en','like',"%$name%")
            ->orWhere('part','like',"%$name%")
            ->get();
        if(count($data) != 0) return response()->json($data);
        else return response()->json([]);
    }

    public function fillSelect(Request $request,Makes $makes,Models $model,Vehicle $vehicle) {
        $elemId = $request->elemId;
        $id = $request->id;
        $joinData = $model::query()
            ->leftJoin('make_models','model.id','=','make_models.model_id')
            ->with('vehicle');

        if(!empty($id)) {
            if($elemId === 'make') {
                $data = $joinData->where('make_id','=',"$id") ->get();
                return response()->json($data);
            }elseif($elemId === 'model') {
                $makeYear = $joinData->where('model_id',"$id")->get()->first();
                $make = '';
                if($makeYear !== null) $make = $makes::query()->where('id',$makeYear->make_id)->get()->first();
                return response()->json(['make' => $make,'makeYear' => $makeYear['vehicle']]);
            }elseif($elemId === 'year') {
                $year = $vehicle::query()
                    ->select('year','make_id')
                    ->where('id','=',$id)
                    ->get()->first();
                $data = $vehicle::query()
                    ->where('year','=',$year->year)
                    ->join('make','vehicle.make_id','make.id')
                    ->with('model')->get();
                return response()->json($data);
            }
        }
    }

    public function reset() {
        $models = Models::query()->get();
        $years = Vehicle::query()->get();
        return response()->json(['models' => $models,'years' => $years]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
