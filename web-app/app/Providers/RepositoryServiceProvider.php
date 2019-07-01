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
        //TODO - The logic below should be refactored into a loop to avoid duplication

        $this->app->when(AssignmentController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('Assignment');
            });

        $this->app->when(AssignmentStatusController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('AssignmentStatus');
            });

        $this->app->when(ContractStageController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('ContractStage');
            });

        $this->app->when(CredentialController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('Credential');
            });

        $this->app->when(DistrictController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('District');
            });

        $this->app->when(PaymentTypeController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('Payment');
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



        $this->app->when(CredentialController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('Credential');
            });

        $this->app->when(RegionController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('Region');
            });

        $this->app->when(RoleController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('Role');
            });

        $this->app->when(SchoolController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('School');
            });

        $this->app->when(SessionController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('Session');
            });

        $this->app->when(SessionActivityController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('SessionActivity');
            });

        $this->app->when(SessionTypeController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('SessionType');
            });

        $this->app->when(SubjectController::class)
            ->needs(iModelRepository::class)
            ->give(function () {
                return $this->getRepository('Subject');
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
