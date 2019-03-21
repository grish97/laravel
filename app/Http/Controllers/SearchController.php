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
        $table = Vehicle::query()
            ->leftJoin('make','vehicle.make_id','=','make.id')
            ->leftJoin('model','vehicle.model_id','=','model.id');

        $data  = array_filter(['make' => $makeId, 'model' => $modelId,'year' =>  $yearId],function($elem) {
            return $elem != null;
        });

        $count = count($data);

        list($firstSelect, $currentSelect, $lastSelect) = $selected;

        $queryMethod = $this->queryMethod($data,$selected);
        $where = $queryMethod['where'];
        $select = $queryMethod['select'];
        $unique = $queryMethod['unique'];
        $orderBy = $queryMethod['orderBy'];
        $different = $queryMethod['different'];

        if($count == 1 || $firstSelect === $currentSelect) {
            $columnName1 = reset($different);
            $columnName2 = end($different);

            $data1 = $table
                ->select($select[0])
                ->where($where)
                ->orderBy($orderBy[0])
                ->get()
                ->unique($unique[0]);

            $data2 = $table
                ->select($select[1])
                ->orderBy($orderBy[1])
                ->get()
                ->unique($unique[1]);

            return ["$columnName1" => $data1,"$columnName2" => $data2];
        }elseif($count == 2 || ($firstSelect != $currentSelect && $lastSelect != $currentSelect)) {
            $columnName = reset($different);

            $data1 = $table
                ->select($select[0])
                ->where($where)
                ->orderBy($orderBy[0])
                ->get()
                ->unique($unique[0]);

            return ["$columnName" => $data1];
        }

    }

    public function queryMethod($data,$selected) {
        list($firstSelect, $currentSelect, $lastSelect) = $selected;
        $selectName = ['make', 'model', 'year'];
        $different = array_diff($selectName,$selected);
        $count = count($data);

        $where = [];
        $select = [];
        $unique = [];
        $orderBy = [];

        if($count != 1 && $firstSelect == $currentSelect) {
            $data = [$firstSelect => $data[$firstSelect]];
            $different = array_diff($selectName,[$firstSelect]);
        }elseif($count != 1 && $firstSelect != $currentSelect && $lastSelect != $currentSelect) {
            $data = [
                $firstSelect => $data[$firstSelect],
                $currentSelect => $data[$currentSelect]
            ];
            $different = array_diff($selectName,[$firstSelect,$currentSelect]);
        }

        foreach($data as $key => $val) {
            $columnId = $key.'_id';

            if($key == 'year') $columnId = 'vehicle.year';
            array_push($where,[$columnId,$val]);
        }

        foreach($different as $val) {
            $columnId = $val.'_id';
            $columnName = $val.'.name';
            $uniqueColumn = $columnId;

            if($val == 'year') {
                $columnId = 'vehicle.id';
                $columnName = $val;
                $uniqueColumn = $columnName;
            }

            array_push($select,[$columnId,$columnName]);
            array_push($unique,$uniqueColumn);
            array_push($orderBy,$columnName);
        }

        return ['where' => $where, 'select' => $select, 'unique' => $unique, 'orderBy' => $orderBy, 'different' => $different];

    }

    public function showSelected(Request $request,Vehicle $vehicle) {
        $selected = array_filter($request->selected,function($elem) {
            return $elem != null;
        });

        $where = [];

        foreach($selected as $key => $val) {
            if($key != 'year') {
                $key = $key.'_id';
            }
            array_push($where,[$key,$val]);
        }

        $data = $vehicle::query()
            ->where($where)
            ->with('make','model','type')
            ->get();

        return $data;
    }

    public function reset(Makes $make, Models $model,Vehicle $vehicle) {
        $makes = $make::query()->groupBy('name')->get();
        $models = $model::query()->groupBy('name')->get();
        $years =  $vehicle::query()->select('id', 'year')->groupBy('year')->orderBy('year','desc')->get();
        return response()->json(['makes' => $makes,'models' => $models,'years' => $years]);
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
