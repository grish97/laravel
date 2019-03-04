<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Makes;
use App\Models\Models;
use App\Models\Parts;
use App\Models\Descriptions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,Descriptions $description,Parts $part)
    {
        $this->validate($request,[
            'name' => 'required'
        ]);

        $name = $request->get('name');

        if(is_numeric($name)) {
            $parts = $part::query()->where('part','like',"%$name")->with('description')->get();
            if(count($parts) != 0) return response()->json(['parts' => $parts]);
            else return response()->json([]);
        }else {
            $desc = $description::query()->with('part')->select('part')->get();
            if(count($desc) != 0) return response()->json(['desc' => $desc]);
            else return response()->json([]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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
