@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="jumbotron">
            <div class="row align-items-end">
                {{--SEARCH OF NAME--}}
                <form class="mr-5 formMake">
                    <label for="name">Description or Part: </label>
                    <div class="form-inline">
                        <input type="text" id="name" name="name" class="form-control mr-3">
                        <button type="submit" class="btn btn-info request" data-action="/getCar">Search</button>
                    </div>
                </form>

                {{--SEARCH OF MAKE MODEL YEAR--}}
                <form class="form-inline mt-5" id="selectForm">
                    <div class="row">
                        <div class="col">
                            <label for="make"></label>
                            <select class="custom-select" id="make">
                                <option value="">Make</option>
                                @foreach($makes as $key => $make)
                                    <option value="{{$make->id}}">{{$make->name}}</option>
                                @endforeach;
                            </select>
                        </div>
                        <div class="col">
                            <label for="model"></label>
                            <select class="custom-select" id="model">
                                <option value="">Model</option>
                                @foreach($models as $key => $model)
                                    <option value="{{$model->id}}">{{$model->name}}</option>
                                @endforeach;
                            </select>
                        </div>
                        <div class="col">
                            <label for="year"></label>
                            <select class="custom-select" id="year">
                                <option value="">Year</option>
                                @foreach($years as $key => $year)
                                    <option value="{{$year->id}}">{{$year->year}}</option>
                                @endforeach;
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-info ml-3" data-action="/select-search">Search</button>
                </form>
            </div>
        </div>

        <div id="view" class="mt-5">
            <div class="card-columns"></div>
        </div>

    </div>
@endsection