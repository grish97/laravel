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

    public function fillSelect(Request $request) {
        $elemId = $request->elemId;
        $id = $request->id;
        if($elemId === 'make') {
            $models = Models::query()
                ->leftJoin('make_models','model.id','=','make_models.model_id')
                ->where('make_id','=',"$id")
                ->get();
            return response()->json($models);
        }elseif($elemId === 'model') {
            $make = Makes::query()
                ->where('model_id','=',"$id")
                ->leftJoin('make_models','make.id','make_models.make_id')
                ->get()->first();
            return response()->json($make);
        }
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
