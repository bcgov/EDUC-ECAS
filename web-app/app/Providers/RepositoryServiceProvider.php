<?php

namespace App\Providers;

use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\AssignmentStatusController;
use App\Http\Controllers\Api\ContractStageController;
use App\Http\Controllers\Api\CredentialController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\PaymentTypeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProfileCredentialController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\SessionActivityController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\SessionTypeController;
use App\Http\Controllers\Api\SubjectController;
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
