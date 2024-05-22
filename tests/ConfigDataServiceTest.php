<?php

namespace VoyagerInc\PermissionRole\Tests;

use VoyagerInc\PermissionRole\Services\Contracts\ConfigDataServiceInterface;

class ConfigDataServiceTest extends BaseTest
{   
    protected $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ConfigDataServiceInterface::class);
    }

    public function test_get_returns_true_value()
    {
        config(['permission-role.enable_middleware' => true]);

        $result = $this->service->get('enable_middleware');

        $this->assertEquals(true, $result);
    }

    public function test_get_returns_false_value()
    {
        $result = $this->service->get('enable_middleware', false);

        $this->assertEquals(false, $result);
    }

    public function test_get_returns_default_true_if_key_not_exists_and_default_not_provided()
    {
        $result = $this->service->get('non_existent_key');

        $this->assertTrue($result);
    }

    public function test_set_config_is_null_and_will_return_false()
    {
        config(['permission-role.enable_middleware' => null]);

        $result = $this->service->get('enable_middleware');

        $this->assertEquals(false, $result);
    }
}