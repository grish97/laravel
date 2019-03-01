@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="jumbotron">
            <div class="row align-items-end">
                {{--SEARCH OF NAME--}}
                <form class="mr-5 formMake">
                    <label for="name">Make or Part: </label>
                    <div class="form-inline">
                        <input type="text" id="name" name="name" class="form-control mr-3">
                        <button type="submit" class="btn btn-info request" data-action="/getCar">Search</button>
                    </div>
                </form>

                {{--SEARCH OF MAKE MODEL YEAR--}}
                <form class="form-inline mt-5">
                    <div class="row">
                        <div class="col">
                            <select class="custom-select" id="make">
                                <option value="">Make</option>
                                @foreach($makes as $key => $make)
                                    <option value="{{$key}}">{{$make->name}}</option>
                                @endforeach;
                            </select>
                        </div>
                        <div class="col">
                            <select class="custom-select" id="model">
                                <option value="">Model</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
                        </div>
                        <div class="col">
                            <select class="custom-select" id="year">
                                <option value="">Year</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-info ml-3">Search</button>
                </form>
            </div>
        </div>

        <div id="view" class="mt-5">
            <table class="table d-none">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection