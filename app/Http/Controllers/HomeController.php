<?php

namespace App\Http\Controllers;

use App\Models\Makes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
   public function index() {
       $makes = Makes::query()->get();
       return view('home.home',compact('makes'));
   }
}
