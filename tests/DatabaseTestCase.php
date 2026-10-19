<?php

namespace Tests;

use Illuminate\Support\Facades\DB;

/** Integration tests must never reuse the development or production database. */
abstract class DatabaseTestCase extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUpTraits()
    {
        if (config('database.default') !== 'mysql' || config('database.connections.mysql.database') !== 'ensan_test') {
            throw new \RuntimeException('Integration tests require the isolated MySQL database ensan_test.');
        }
        try {
            DB::connection()->getPdo();
        } catch (\PDOException $e) {
            $this->markTestSkipped('MySQL test database ensan_test is unavailable. Configure .env.testing.');
        }
        return parent::setUpTraits();
    }
}
