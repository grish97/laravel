@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h3>Part Number: {{$data->part}}</h3>
            </div>
            <div class="card-body">
                <p class="card-text"><span class="font-weight-bold">EN:</span> {{$data->en}}</p>
                <p class="card-text"><span class="font-weight-bold">ES:</span> {{$data->es}}</p>
            </div>
        </div>
    </div>
@endsection