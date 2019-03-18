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
        list($firstSelect,$lastSelect,$lastElem) = $selected;

        $dataName = ['make','model','year'];

        $data  = array_filter(['make' => $makeId, 'model' => $modelId,'year' =>  $yearId],function($elem) {
            return $elem != null;
        });

        $a = array_diff($selected,$data);

        $count = count($data);

        $table = Vehicle::query()
            ->leftJoin('make','vehicle.make_id','=','make.id')
            ->leftJoin('model','vehicle.model_id','=','model.id');

        if($count === 1 || $firstSelect === $lastSelect) {
            $dataName = array_diff($dataName,[$firstSelect]);
            $column1 = reset($dataName);
            $column2 = end($dataName);

            $selectColumn1 = [$column1.'_id',$column1.'.name'];
            $selectColumn2 = ($column2 == 'year') ? ['vehicle.id','vehicle.'.$column2] : [$column2.'_id',$column2.'.name'];

            $where = ($firstSelect == 'year')  ?  [[$firstSelect,$data[$firstSelect]]]
                                               :  [[$firstSelect.'_id',$data[$firstSelect]]];


            $$column1 = $table
                ->select($selectColumn1)
                ->where($where)
                ->get();

            $$column2 = $table
                ->select($selectColumn2)
                ->get();

            if($firstSelect != 'year') {
                $$column1 = $$column1->unique($column1.'_id');
                $$column2 = $$column2->unique($column2);
            }else {
                $$column1  = $$column1->unique($column1.'_id');
                $$column2  = $$column2->unique($column2.'_id');
            }

            return compact("$column1","$column2");

        }elseif($count === 2 || ($firstSelect != $lastSelect &&  $lastElem != $lastSelect)) {
            $dataName = array_diff($dataName,[$firstSelect,$lastSelect]);
            $column = reset($dataName);

            $select = [$column.'_id',$column.'.name'];

            $where = [
                [$firstSelect.'_id',$data[$firstSelect]],
                [$lastSelect.'_id',$data[$lastSelect]]
                ];

            $orderBy = $column.'.name';
            $groupBy = $column.'_id';


            if(in_array('year',[$firstSelect,$lastSelect])) {
               if($firstSelect == 'year') $where = [
                                            [$firstSelect,$data[$firstSelect]],
                                            [$lastSelect.'_id',$data[$lastSelect]]
                                       ];
               else $where = [
                       [$firstSelect.'_id',$data[$firstSelect]],
                       [$lastSelect,$data[$lastSelect]]
                    ];

            }else {
                $select = ['vehicle.id','vehicle.year'];
                $orderBy = $column;
                $groupBy = $column;
            }

            $$column = $table
                ->select($select)
                ->where($where)
                ->orderBy($orderBy,'desc')
                ->groupBy($groupBy)
                ->get();

            return compact("$column");
        }
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
