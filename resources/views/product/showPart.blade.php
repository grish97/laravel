@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>Part Number: {{$data->part}}</h3>
            </div>

            <div class="card-body">
                <p class="card-text"><span class="font-weight-bold">EN:</span> {{$data->description['en']}}</p>
                <p class="card-text"><span class="font-weight-bold">ES:</span> {{$data->description['es']}}</p>
                <button type="button" data-func="generateVehicle" data-action="/showPartVehicle/{{$data->id}}" class="btn btn-warning showParts"><i class="fab fa-openid"></i> Show Vehicle</button>
            </div>

        </div>

        <div class="showVehicle d-none mt-5">
            <table class="table">
                <thead>
                <tr>
                    <th>N</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Type</th>
                    <th>Year of production</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection