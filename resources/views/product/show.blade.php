@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>Make: {{$data->make[0]['name']}}</h3>
            </div>
            <div class="card-body">
                <h5 class="card-title">Model: {{$data->model[0]['name']}}</h5>
                <p class="card-text">Year: {{$data->year}}</p>
                <button type="button" data-func='generatePart' data-action="/showVehiclePart/{{$data->id}}" class="btn btn-warning showParts"><i class="fab fa-openid"></i> Show Parts</button>
            </div>
        </div>

        <div class="card-columns mt-5"></div>
    </div>
@endsection