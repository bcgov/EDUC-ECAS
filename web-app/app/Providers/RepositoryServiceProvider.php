<?php

namespace App\Providers;

use App\Dynamics\Assignment;
use App\Dynamics\District;
use App\Dynamics\Profile;
use App\Dynamics\ProfileCredential;
use App\Dynamics\School;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\DistrictSearchController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProfileCredentialController;
use App\Http\Controllers\Api\SchoolSearchController;
use App\Http\Controllers\DashboardController;
use App\Interfaces\iModelRepository;
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

        $this->app->when(DashboardController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->app->make(Profile::class);
            });


        $this->app->when(AssignmentController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->app->make(Assignment::class);
            });

        $this->app->when(ProfileController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->app->make(Profile::class);
            });

        $this->app->when(ProfileCredentialController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->app->make(ProfileCredential::class);
            });

        $this->app->when(DistrictSearchController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->app->make(District::class);
            });

        $this->app->when(SchoolSearchController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->app->make(School::class);
            });


        $this->app->bind(
            ClientInterface::class,
            function () {
                return new Client( config('dynamics.connection'));
            }
        );

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
