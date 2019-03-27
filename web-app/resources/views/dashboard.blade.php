@extends('app')

@section('content')
    <div id="app">
        <dashboard
        :user="{{ $user }}"
        :credentials="{{ $credentials }}"
        :sessions="{{ $sessions }}"
        :assignments="{{ $assignments }}"
        :subjects="{{ $subjects }}"
        :schools="{{ $schools }}"
        >
    </dashboard>
    </div>
@endsection