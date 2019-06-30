@extends('app')

@section('content')
    <div id="app">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col">

                        <profile-component :profile="{{ json_encode($profile) }}" :aggregate="{{ json_encode($aggregated) }}"></profile-component>

                    </div>
                    <div class="col">

                        <profile-credentials :credentials="{{ json_encode($credentials) }}" :aggregate="{{ json_encode($aggregated) }}"></profile-credentials>

                    </div>
                </div>
                <div class="row">
                    <div class="col">

                        <marking-sessions :aggregated="{{ json_encode($aggregated) }}"></marking-sessions>

                    </div>
                </div>
            </div>
        </div>

    {{--<div id="app">
        <dashboard
        :user="{{ $user }}"
        :credentials="{{ $credentials }}"
        :sessions="{{ $sessions }}"
        :subjects="{{ $subjects }}"
        :schools="{{ $schools }}"
        :regions="{{ $regions }}"
        :districts="{{ $districts }}"
        :user_credentials="{{ $user_credentials }}"
        dusk="dashboard-component"
        >
    </dashboard>--}}
    </div>
@endsection