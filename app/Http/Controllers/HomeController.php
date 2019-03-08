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
       $makes =  Makes::query()->get();
       $models = Models::query()->get();
       $years =  Vehicle::query()->select('id', 'year')->groupBy('year')->orderBy('year','desc')->get();

       return view('home.home',compact('makes','models','years'));
   }
}
