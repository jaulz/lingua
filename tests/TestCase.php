<?php

namespace Jaulz\Lingua\Tests;

use Jaulz\Lingua\LinguaServiceProvider;
use Tpetry\PostgresqlEnhanced\PostgresqlEnhancedServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LinguaServiceProvider::class,
            PostgresqlEnhancedServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app) {
    }
}