@extends('app')

@section('content')
    <div class="card">
        <div class="card-header"><h1>Edit Profile</h1></div>
        <div class="card-body">
            <form>
                <div class="form-group row">
                    <label for="id" class="col-3 col-form-label">id</label>
                    <div class="col">
                        <input value="{{ $user['id'] }}" type="text" class="form-control" name="id" id="id">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-3 col-form-label">email</label>
                    <div class="col">
                        <input value="{{ $user['email'] }}" type="text" class="form-control" name="email" id="email">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="preferred_first_name" class="col-3 col-form-label">preferred_first_name</label>
                    <div class="col">
                        <input value="{{ $user['preferred_first_name'] }}" type="text" class="form-control" name="preferred_first_name" id="preferred_first_name">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="first_name" class="col-3 col-form-label">first_name</label>
                    <div class="col">
                        <input value="{{ $user['first_name'] }}" type="text" class="form-control" name="first_name" id="first_name">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="last_name" class="col-3 col-form-label">last_name</label>
                    <div class="col">
                        <input value="{{ $user['last_name'] }}" type="text" class="form-control" name="last_name" id="last_name">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="social_insurance_no" class="col-3 col-form-label">social_insurance_no</label>
                    <div class="col">
                        <input value="{{ $user['social_insurance_no'] }}" type="text" class="form-control" name="social_insurance_no" id="social_insurance_no">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="professional_certificate_bc" class="col-3 col-form-label">professional_certificate_bc</label>
                    <div class="col">
                        <input type="text" class="form-control" name="professional_certificate_bc"
                               value="{{ $user['professional_certificate_bc'] }}" id="professional_certificate_bc">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="professional_certificate_yk" class="col-3 col-form-label">professional_certificate_yk</label>
                    <div class="col">
                        <input type="text" class="form-control" name="professional_certificate_yk"
                               value="{{ $user['professional_certificate_yk'] }}" id="professional_certificate_yk">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="professional_certificate_other"
                           class="col-3 col-form-label">professional_certificate_other</label>
                    <div class="col">
                        <input type="text" class="form-control" name="professional_certificate_other"
                               value="{{ $user['professional_certificate_other'] }}" id="professional_certificate_other">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="current_school" class="col-3 col-form-label">current_school</label>
                    <div class="col">
                        <select class="form-control" name="school" id="school">
                            @foreach($schools as $id => $school)
                                <option value="{{ $id }}">{{ $school }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address_1" class="col-3 col-form-label">address_1</label>
                    <div class="col">
                        <input value="{{ $user['address_1'] }}" type="text" class="form-control" name="address_1" id="address_1">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address_2" class="col-3 col-form-label">address_2</label>
                    <div class="col">
                        <input value="{{ $user['address_2'] }}" type="text" class="form-control" name="address_2" id="address_2">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="city" class="col-3 col-form-label">city</label>
                    <div class="col">
                        <input value="{{ $user['city'] }}" type="text" class="form-control" name="city" id="city">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="region" class="col-3 col-form-label">region</label>
                    <div class="col">
                        <input value="{{ $user['region'] }}" type="text" class="form-control" name="region" id="region">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="postal_code" class="col-3 col-form-label">postal_code</label>
                    <div class="col">
                        <input value="{{ $user['postal_code'] }}" type="text" class="form-control" name="postal_code" id="postal_code">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-3"></div>
                    <div class="col"><a href="/Dashboard" class="btn btn-danger btn-block">Cancel</a></div>
                    <div class="col"><a href="/Dashboard" class="btn btn-primary btn-block">Save</a></div>
                </div>
            </form>
        </div>
    </div>

@endsection