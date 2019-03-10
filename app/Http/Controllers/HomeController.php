<?php

namespace App\Http\Controllers;

use App\Models\Makes;
use App\Models\Models;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
   public function index() {
       $makes = Makes::query()->groupBy('name')->get();
       $models = Models::query()->groupBy('name')->get();
       $years =  Vehicle::query()->groupBy('year')->orderBy('year','desc')->get();
       return view('home.home',compact('makes','models','years'));
   }
}
