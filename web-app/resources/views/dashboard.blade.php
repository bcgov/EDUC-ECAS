@extends('app')

@section('content')
    <div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <input type="submit" value="Logout" />
        </form>
    </div>
    <div id="app">
        <dashboard
                :data="{{ json_encode($dashboard) }}"
                dusk="dashboard-component"
        >
        </dashboard>
    </div>
@endsection