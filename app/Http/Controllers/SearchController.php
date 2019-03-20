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
        $name = $request->name;

        $data = $parts::query()
            ->select('part.id as partId','part.part','description.en','description.es')
            ->leftJoin('description','part.description_id','=','description.id')
            ->where('es','like',"%$name%")
            ->orWhere('en','like',"%$name%")
            ->orWhere('part','like',"%$name%")
            ->get();

        return response()->json($data);
    }

    public function fillSelect(Request $request) {
        $makeId = $request->make;
        $modelId = $request->model;
        $yearId =  $request->year;
        $selected = $request->selected;

        $data = $this->getSelectValue($makeId, $modelId, $yearId, $selected);

        return response()->json($data);
    }

    public function getSelectValue($makeId = null, $modelId = null, $yearId = null, $selected)    {
        list($firstSelect, $currentSelect, $lastSelect) = $selected;

        $dataName = ['make', 'model', 'year'];

        $data  = array_filter(['make' => $makeId, 'model' => $modelId,'year' =>  $yearId],function($elem) {
            return $elem != null;
        });

        $count = count($data);

        $table = Vehicle::query()
            ->leftJoin('make','vehicle.make_id','=','make.id')
            ->leftJoin('model','vehicle.model_id','=','model.id');

        if($count === 1 || $firstSelect === $currentSelect) {
            $dataName = array_diff($dataName, [$firstSelect]);
            $columnName1 = reset($dataName);
            $columnName2 = end($dataName);

            $selectColumn1 = [$columnName1.'_id', $columnName1.'.name'];
            $selectColumn2 = ($columnName2 == 'year') ? ['vehicle.id','vehicle.'.$columnName2] : [$columnName2.'_id',$columnName2.'.name'];

            $where = ($firstSelect == 'year')  ?  [[$firstSelect,$data[$firstSelect]]]  :  [[$firstSelect.'_id',$data[$firstSelect]]];

            $selectData1 = $table
                ->select($selectColumn1)
                ->where($where)
                ->get();

            $selectData2 = $table
                ->select($selectColumn2)
                ->get();

            if($firstSelect != 'year') {
                $selectData1 = $selectData1->unique($columnName1.'_id');
                $selectData2 = $selectData2->unique($columnName2);
            }else {
                $selectData1  = $selectData1->unique($columnName1.'_id');
                $selectData2  = $selectData2->unique($columnName2.'_id');
            }

            return ["$columnName1" => $selectData1,"$columnName2" => $selectData2];

        }elseif($count === 2 || ($firstSelect != $currentSelect &&  $lastSelect != $currentSelect)) {
            $dataName = array_diff($dataName,[$firstSelect,$currentSelect]);
            $columnName = reset($dataName);

            $select = [$columnName.'_id',$columnName.'.name'];

            $where = [
                [$firstSelect.'_id',$data[$firstSelect]],
                [$currentSelect.'_id',$data[$currentSelect]]
                ];

            $orderBy = $columnName.'.name';
            $groupBy = $columnName.'_id';


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
                $orderBy = $columnName;
                $groupBy = $columnName;
            }

            $selectData = $table
                ->select($select)
                ->where($where)
                ->orderBy($orderBy,'desc')
                ->groupBy($groupBy)
                ->get();

            return ["$columnName" => $selectData];
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


    public function showPart(Parts $parts,$id) {
        $data = $parts::query()
            ->where('id',$id)
            ->with('description')
            ->firstOrFail();

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


    public function showVehicle($id)
    {
        $data = Vehicle::query()
            ->where('id','=',$id)
            ->with('make','model')
            ->firstOrFail();
        return view('product.show',compact('data'));
    }

    public function showPartVehicle($id,VehicleParts $vehicleParts) {
        $vehicle = $vehicleParts::query()
            ->select('vehicle.id','vehicle.make_id','make.name as make','vehicle.model_id','model.name as model','vehicle.year', 'type.name as type_name', 'type.id as type_id')
            ->leftJoin('vehicle','vehicle_part.vehicle_id','vehicle.id')
            ->leftJoin('make','vehicle.make_id','make.id')
            ->leftJoin('model','vehicle.model_id','model.id')
            ->leftJoin('type','vehicle.type_id','type.id')
            ->where('vehicle_part.part_id', $id)
            ->orderBy('make.name', 'asc')
            ->orderBy('model.name', 'asc')
            ->orderBy('vehicle.year', 'desc')
            ->get();

        return response()->json($vehicle);
    }
}
