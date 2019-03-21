<?php

namespace App\Http\Controllers;

use App\Models\Makes;
use App\Models\Models;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
   public function index(Request $request) {
       $makes = Makes::query()->groupBy('name')->get();
       $models = Models::query()->groupBy('name')->get();
       $years =  Vehicle::query()->groupBy('year')->orderBy('year','desc')->get();

       if($request->ajax()) return compact('makes','models','years');

       return view('home.home',compact('makes','models','years'));
   }
}
