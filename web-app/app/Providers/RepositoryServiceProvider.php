<?php

namespace App\Providers;

use App\Dynamics\Assignment;
use App\Dynamics\AssignmentStatus;
use App\Dynamics\Credential;
use App\Dynamics\Decorators\CacheDecorator;
use App\Dynamics\District;
use App\Dynamics\Interfaces\iAssignment;
use App\Dynamics\Interfaces\iAssignmentStatus;
use App\Dynamics\Interfaces\iCredential;
use App\Dynamics\Interfaces\iDistrict;
use App\Dynamics\Interfaces\iProfile;
use App\Dynamics\Interfaces\iProfileCredential;
use App\Dynamics\Interfaces\iRegion;
use App\Dynamics\Interfaces\iSchool;
use App\Dynamics\Interfaces\iSession;
use App\Dynamics\Interfaces\iSubject;
use App\Dynamics\Profile;
use App\Dynamics\ProfileCredential;
use App\Dynamics\Region;
use App\Dynamics\School;
use App\Dynamics\Session;
use App\Dynamics\Subject;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(iProfile::class, function ($app) {
            return new CacheDecorator($app->make(Profile::class));
        });

        $this->app->bind(iProfileCredential::class, function ($app) {
            return new CacheDecorator($app->make(ProfileCredential::class));
        });

        $this->app->bind(iAssignment::class, function ($app) {
            return new CacheDecorator($app->make(Assignment::class));
        });

        $this->app->bind(iSession::class, function ($app) {
            return new CacheDecorator($app->make(Session::class));
        });

        $this->app->bind(iCredential::class, function ($app) {
            return new CacheDecorator($app->make(Credential::class));
        });

        $this->app->bind(iRegion::class, function ($app) {
            return new CacheDecorator($app->make(Region::class));
        });

        $this->app->bind(iSubject::class, function ($app) {
            return new CacheDecorator($app->make(Subject::class));
        });

        $this->app->bind(iSchool::class, function ($app) {
            return new CacheDecorator($app->make(School::class));
        });

        $this->app->bind(iDistrict::class, function ($app) {
            return new CacheDecorator($app->make(District::class));
        });

        $this->app->bind(iAssignmentStatus::class, function ($app) {
            return new CacheDecorator($app->make(AssignmentStatus::class));
        });

        $this->app->bind(ClientInterface::class, function () {
            return new Client( config('dynamics.connection'));
        });

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }


}
