@extends('app')

@section('content')
    <div id="app">
        <dashboard
                :data="{{ json_encode($dashboard) }}"
                dusk="dashboard-component"
        >
        </dashboard>
    </div>
@endsection