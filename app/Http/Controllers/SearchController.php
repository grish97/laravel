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
        list($firstSelect,$currentSelect,$lastSelect) = $selected;

        $dataName = ['make','model','year'];

        $data  = array_filter(['make' => $makeId, 'model' => $modelId,'year' =>  $yearId],function($elem) {
            return $elem != null;
        });

        $count = count($data);

        $table = Vehicle::query()
            ->leftJoin('make','vehicle.make_id','=','make.id')
            ->leftJoin('model','vehicle.model_id','=','model.id');

        if($count === 1 || $firstSelect === $currentSelect) {
            $dataName = array_diff($dataName,[$firstSelect]);
            $column1 = reset($dataName);
            $column2 = end($dataName);

            $selectColumn1 = [$column1.'_id',$column1.'.name'];
            $selectColumn2 = ($column2 == 'year') ? ['vehicle.id','vehicle.'.$column2] : [$column2.'_id',$column2.'.name'];

            $where = ($firstSelect == 'year')  ?  [[$firstSelect,$data[$firstSelect]]]  :  [[$firstSelect.'_id',$data[$firstSelect]]];

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

        }elseif($count === 2 || ($firstSelect != $currentSelect &&  $lastSelect != $currentSelect)) {
            $dataName = array_diff($dataName,[$firstSelect,$currentSelect]);
            $column = reset($dataName);

            $select = [$column.'_id',$column.'.name'];

            $where = [
                [$firstSelect.'_id',$data[$firstSelect]],
                [$currentSelect.'_id',$data[$currentSelect]]
                ];

            $orderBy = $column.'.name';
            $groupBy = $column.'_id';


            if(in_array('year',[$firstSelect,$currentSelect])) {
               if($firstSelect == 'year') $where = [
                                            [$firstSelect,$data[$firstSelect]],
                                            [$currentSelect.'_id',$data[$currentSelect]]
                                       ];
               else $where = [
                       [$firstSelect.'_id',$data[$firstSelect]],
                       [$currentSelect,$data[$currentSelect]]
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
            $selected = array_filter($request->selected,function($elem) {
                return $elem != null;
            });

            $selectKey = array_keys($selected);
            $selectedCount = count($selected);

            $where = [];

            if($selectedCount === 1) {
                list($select) = $selectKey;
                $column = ($select == 'year') ? $select : $select.'_id';
                $value  = $selected[$select];

                $where = [
                    [$column,$value]
                ];

            }elseif($selectedCount === 2) {
                list($selectOne,$selectTwo) = $selectKey;
                $columnOne = ($selectOne === 'year') ? $selectOne : $selectOne.'_id';
                $columnTwo = ($selectTwo === 'year') ? $selectTwo : $selectTwo.'_id';

                $valueOne  = $selected[$selectOne];
                $valueTwo  = $selected[$selectTwo];

                $where = [
                    [$columnOne,$valueOne],
                    [$columnTwo,$valueTwo]
                ];

            }else {
                list($selectOne,$selectTwo,$selectThree) = $selectKey;
                $columnOne = ($selectOne === 'year') ? $selectOne : $selectOne.'_id';
                $columnTwo = ($selectTwo === 'year') ? $selectTwo : $selectTwo.'_id';
                $columnThree = ($selectThree === 'year') ? $selectThree : $selectThree.'_id';

                $valueOne  = $selected[$selectOne];
                $valueTwo  = $selected[$selectTwo];
                $valueThree  = $selected[$selectThree];

                $where = [
                    [$columnOne,$valueOne],
                    [$columnTwo,$valueTwo],
                    [$columnThree,$valueThree],
                ];
            }

        $data = $vehicle::query()
            ->where($where)
            ->with('make','model')
            ->get();

        return $data;
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

    public function showPartVehicle($id) {
        var_dump($id);
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
