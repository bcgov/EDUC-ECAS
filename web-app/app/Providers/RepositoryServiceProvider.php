<?php

namespace App\Providers;

use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProfileCredentialController;
use App\Http\Controllers\DashboardController;
use App\Interfaces\iModelRepository;
use Illuminate\Support\Facades\App;
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
                return $this->getRepository('Profile');
            });


        $this->app->when(AssignmentController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('Assignment');
            });

        $this->app->when(ProfileController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('Profile');
            });

        $this->app->when(ProfileCredentialController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('ProfileCredential');
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


    private function getRepository($className)
    {
        if(env('DATASET') == 'MockEntities') {
            return App::make('App\MockEntities\Repository\\' . $className);
        }
        return App::make('App\Dynamics\\' . $className );



    }
}
