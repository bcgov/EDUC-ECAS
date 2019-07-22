@extends('app')

@section('content')
    <div id="app">
        <div>
            <ecas-logout></ecas-logout>
        </div>

        <div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <ecas-profile :data="{{ json_encode($dashboard) }}"></ecas-profile>
                        </div>
                        <profile-credentials :data="{{ json_encode($dashboard) }}"></profile-credentials>
                    </div>
                    <div class="col">
                        <div class="row pt-4">
                            <marking-sessions :data="{{ json_encode($dashboard) }}"></marking-sessions>
                        </div>
                    </div>
                </div>
            </div>

        </div>


    </div>
@endsection
