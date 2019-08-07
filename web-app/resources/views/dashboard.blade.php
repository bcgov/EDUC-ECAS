@extends('app')

@section('api_token'){{ $api_token }}@endsection

@section('content')
    <div id="app">
        <dashboard-component
            :user="{{ json_encode($user) }}"
            :user_credentials="{{ json_encode($user_credentials) }}"
            :sessions="{{ json_encode($sessions) }}"
            :subjects="{{ json_encode($subjects) }}"
            :regions="{{ json_encode($regions) }}"
            :credentials="{{ json_encode($credentials) }}"
        >
        </dashboard-component>
    </div>
@endsection