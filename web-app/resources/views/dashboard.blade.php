@extends('app')

@section('content')
    <div id="app">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col">

                        <profile-component :user="{{ json_encode($user) }}"
                                           :credentials="{{ $credentials }}"
                                           :sessions="{{ $sessions }}"
                                           :subjects="{{ $subjects }}"
                                           :schools="{{ $schools }}"
                                           :regions="{{ $regions }}"
                                           :districts="{{ $districts }}"
                                           :user_credentials="{{ $user_credentials }}">

                        </profile-component>

                    </div>
                    <div class="col">

                        <profile-credentials :user_credentials="{{ json_encode($user_credentials) }}"
                                             :credentials="{{ $credentials }}"
                                             :sessions="{{ $sessions }}"
                                             :subjects="{{ $subjects }}"
                                             :schools="{{ $schools }}"
                                             :regions="{{ $regions }}"
                                             :districts="{{ $districts }}"
                                             :user_credentials="{{ $user_credentials }}">

                        </profile-credentials>

                    </div>
                </div>
                <div class="row">
                    <div class="col">

                     {{--   <marking-sessions
                                          :credentials="{{ $credentials }}"
                                          :sessions="{{ $sessions }}"
                                          :subjects="{{ $subjects }}"
                                          :schools="{{ $schools }}"
                                          :regions="{{ $regions }}"
                                          :districts="{{ $districts }}"
                                          :user_credentials="{{ $user_credentials }}">
                        </marking-sessions>
--}}
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