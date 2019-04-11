@extends('app')

@section('content')
    <div id="app">
        <dashboard
        :user="{{ $user }}"
        :credentials="{{ $credentials }}"
        :sessions="{{ $sessions }}"
        :subjects="{{ $subjects }}"
        :schools="{{ $schools }}"
        :regions="{{ $regions }}"
        :districts="{{ $districts }}"
        :payments="{{ $payments }}"
        >
    </dashboard>
    </div>
@endsection