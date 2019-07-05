<?php

namespace Tests\Api;


use App\MockEntities\ProfileCredential;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;


abstract class BaseApiTest extends TestCase
{

    use DatabaseMigrations;

}
