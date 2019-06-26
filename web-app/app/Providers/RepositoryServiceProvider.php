<?php

namespace App\Providers;

use App\Dynamics\Interfaces\iCredentialRepository;
use App\Dynamics\Interfaces\iSchoolRepository;
use App\Dynamics\Credential;
use App\Dynamics\School;
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
//        $this->app->bind(
//            iCredentialRepository::class,
//            Credential::class
//        );
//
//        $this->app->bind(
//            iSchoolRepository::class,
//            School::class
//        );
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
