<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;
use Exception;

trait TestSaves
{

    protected abstract function model();
    protected abstract function routeStore();
    protected abstract function routeUpdate();

    protected function assertStore(array $sendData, array $testDatabase, array $testJsonData = null): TestResponse
    {

        $response = $this->json('POST', $this->routeStore(), $sendData);
        if ($response->status() !== 201) {
            throw new Exception("Response status must be 201, give {$response->status()}: {$response->content()}");
        }
        $this->assertInDatabase($response, $testDatabase);
        $this->assertJsonresponseContent($response, $testDatabase, $testJsonData);

        return $response;
    }

    protected function assertUpdate(array $sendData, array $testDatabase, array $testJsonData = null): TestResponse
    {

        $response = $this->json('PUT', $this->routeUpdate(), $sendData);
        if ($response->status() !== 200) {
            throw new Exception("Response status must be 200, give {$response->status()}: {$response->content()}");
        }
        $this->assertInDatabase($response, $testDatabase);
        $this->assertJsonresponseContent($response, $testDatabase, $testJsonData);
        return $response;
    }

    private function assertInDatabase($response, $testDatabase)
    {
        $model = $this->model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas($table, $testDatabase + ['id' => $response->json('id')]);
    }

    private function assertJsonresponseContent($response, $testDatabase, array $testJsonData = null)
    {
        $testResponse = $testJsonData ?? $testDatabase;
        $response->assertJsonFragment($testResponse + ['id' => $response->json('id')]);
    }
}
