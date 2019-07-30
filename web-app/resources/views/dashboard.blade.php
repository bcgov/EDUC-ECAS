@extends('app')

@section('content')
    <div id="app">
        <ecas-dashboard :data="{{ json_encode($dashboard) }}"></ecas-dashboard>
    </div>
@endsection
