<?php

namespace App\Http\Controllers;

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

        if(!empty($id)) {
            if($elemId === 'make') {
                $data = $vehicle::query()
                    ->where('make_id','=',"$id")
                    ->with('model')
                    ->groupBy('model_id')
                    ->get();
                return response()->json($data);
            }elseif($elemId === 'model') {
                $data = $vehicle::query()
                    ->where('model_id','=',$id)
                    ->with('make')
                    ->groupBy('make_id')
                    ->get()
                    ->first();
                return response()->json($data);
            }elseif($elemId === 'year') {
                $year = $vehicle::query()
                    ->select('year')
                    ->where('id','=',$id)
                    ->get()
                    ->first();
                $data = $vehicle::query()
                    ->where('year','=',$year->year);
                $make = $data
                    ->select('make_id')
                    ->with('make')
                    ->groupBy('make_id')
                    ->get();
                $models = $data
                    ->select('model_id')
                    ->with('model')
                    ->groupBy('model_id')
                    ->get();
                return response()->json(['make' => $make,'model' => $models]);
            }
        }
    }

    public function reset() {
        $models = Models::query()->get();
        $years =  Vehicle::query()->select('id', 'year')->groupBy('year')->orderBy('year','desc')->get();
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
