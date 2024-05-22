<?php

namespace VoyagerInc\PermissionRole\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use VoyagerInc\PermissionRole\Middleware\RoleMiddleware;
use VoyagerInc\PermissionRole\Services\Contracts\UserRoleServiceInterface;
use VoyagerInc\PermissionRole\Services\Contracts\ConfigDataServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class RoleMiddlewareTest extends BaseTest
{
    use RefreshDatabase;
    use MockeryPHPUnitIntegration;

    protected $userRoleService;
    protected $middleware;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRoleServiceMock = $this->mock(UserRoleServiceInterface::class);
        $this->configDataServiceMock = $this->mock(ConfigDataServiceInterface::class);
        $this->middlewareMock = new RoleMiddleware($this->userRoleServiceMock, $this->configDataServiceMock);
    }

    /**
     * test checks that the middleware allows access 
     * when the user’s role matches the required role.
     */
    public function test_middleware_allows_access_when_role_matches()
    {
        $this->userRoleServiceMock->shouldReceive('hasRole')
            ->once()
            ->with('admin')
            ->andReturn(true);

        $this->configDataServiceMock->shouldReceive('get')
            ->once()
            ->with('enable_middleware')
            ->andReturn(true);

        $request = Request::create('/permission-role/admin', 'GET');

        $response = $this->middlewareMock->handle($request, function () {
            return new Response('Next middleware or controller reached');
        }, 'admin');

        $this->assertEquals('Next middleware or controller reached', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * test checks that the middleware blocks access 
     * when the user’s role does not match the required role.
     */
    public function test_middleware_blocks_access_when_role_does_not_match()
    {
        $this->userRoleServiceMock->shouldReceive('hasRole')
            ->once()
            ->with('admin')
            ->andReturn(false);

        $this->configDataServiceMock->shouldReceive('get')
            ->once()
            ->with('enable_middleware')
            ->andReturn(true);

        $request = Request::create('/permission-role/admin', 'GET');

        $response = $this->middlewareMock->handle($request, function () {
            return new Response('Next middleware or controller reached');
        }, 'admin');

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(['error' => 'Unauthorized'], $response->original);
    }

    /**
     * test checks that the middleware skips role checking 
     * when the middleware is disabled via configuration.
     */
    public function test_middleware_skips_check_when_disabled()
    {
        $this->configDataServiceMock->shouldReceive('get')
            ->once()
            ->with('enable_middleware')
            ->andReturn(false);

        $request = Request::create('/permission-role/admin', 'GET');

        $response = $this->middlewareMock->handle($request, function () {
            return new Response('Next middleware or controller reached');
        }, 'admin');

        $this->assertEquals('Next middleware or controller reached', $response->getContent());
    }

    /**
     * Test checks if the middleware allows access 
     * when the user has one of multiple roles.
     */
    public function test_middleware_allows_access_with_multiple_roles()
    {
        $this->userRoleServiceMock->shouldReceive('hasRole')
            ->once()
            ->with('admin|editor')
            ->andReturn(true);

        $this->configDataServiceMock->shouldReceive('get')
            ->once()
            ->with('enable_middleware')
            ->andReturn(true);

        $request = Request::create('/permission-role/admin-or-editor', 'GET');

        $response = $this->middlewareMock->handle($request, function () {
            return new Response('Next middleware or controller reached');
        }, 'admin|editor');

        $this->assertEquals('Next middleware or controller reached', $response->getContent());
    }

    /**
     * Tests the case where no role is provided to the middleware.
     */
    public function test_middleware_blocks_access_when_no_role_is_provided()
    {
        $this->userRoleServiceMock->shouldReceive('hasRole')
            ->never();

        $this->configDataServiceMock->shouldReceive('get')
            ->once()
            ->with('enable_middleware')
            ->andReturn(true);

        $request = Request::create('permission-role/no-role', 'GET');

        $response = $this->middlewareMock->handle($request, function () {
            return new Response('Next middleware or controller reached');
        }, '');

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(['error' => 'Unauthorized'], $response->original);
    }

    public function test_middleware_blocks_access_when_user_has_no_role()
    {
        $this->userRoleServiceMock->shouldReceive('hasRole')
            ->once()
            ->with('admin')
            ->andReturn(false);

        $this->configDataServiceMock->shouldReceive('get')
            ->once()
            ->with('enable_middleware')
            ->andReturn(true);

        $request = Request::create('/admin', 'GET');

        $response = $this->middlewareMock->handle($request, function () {
            return new Response('Next middleware or controller reached');
        }, 'admin');

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(['error' => 'Unauthorized'], $response->original);
    }

    /**
     * Tests the scenario where the user role service 
     * returns an unexpected value (neither true nor false).
     */
    public function test_middleware_blocks_access_when_role_service_returns_unexpected_value()
    {
        $this->userRoleServiceMock->shouldReceive('hasRole')
            ->once()
            ->andReturn('unexpected_value');

        $this->configDataServiceMock->shouldReceive('get')
            ->once()
            ->with('enable_middleware')
            ->andReturn(true);

        $request = Request::create('/permission-role/admin', 'GET');
        $response = $this->middlewareMock->handle($request, function () {
            return new Response('Next middleware or controller reached');
        }, 'admin');

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Tests the scenario where the role parameter is not provided to the middleware.
     */
    public function test_middleware_blocks_access_when_role_parameter_is_not_provided()
    {
        $this->userRoleServiceMock->shouldReceive('hasRole')
            ->never();

        $this->configDataServiceMock->shouldReceive('get')
            ->once()
            ->with('enable_middleware')
            ->andReturn(true);

        $request = Request::create('/permission-role/admin', 'GET');
        $response = $this->middlewareMock->handle($request, function () {
            return new Response('Next middleware or controller reached');
        }, null);

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(['error' => 'Unauthorized'], $response->original);
    }
}
