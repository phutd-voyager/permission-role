<?php

namespace VoyagerInc\PermissionRole\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;
use VoyagerInc\PermissionRole\Middleware\RoleMiddleware;
use VoyagerInc\PermissionRole\Services\Contracts\UserRoleServiceInterface;
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

        $this->userRoleService = $this->mock(UserRoleServiceInterface::class);
        $this->middleware = new RoleMiddleware($this->userRoleService);
    }
    
    /**
     * test checks that the middleware allows access 
     * when the user’s role matches the required role.
     */
    public function test_middleware_allows_access_when_role_matches()
    {
        $userRoleServiceMock = Mockery::mock(UserRoleServiceInterface::class);
        $userRoleServiceMock->shouldReceive('hasRole')
                            ->once()
                            ->with('admin')
                            ->andReturn(true);

        $middleware = new RoleMiddleware($userRoleServiceMock);

        $request = Request::create('/permission-role/admin', 'GET');

        $response = $middleware->handle($request, function () {
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
        $userRoleServiceMock = Mockery::mock(UserRoleServiceInterface::class);
        $userRoleServiceMock->shouldReceive('hasRole')
                            ->once()
                            ->with('admin')
                            ->andReturn(false);

        $middleware = new RoleMiddleware($userRoleServiceMock);

        $request = Request::create('/permission-role/admin', 'GET');

        $response = $middleware->handle($request, function () {
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
        config(['permission-role.enable_middleware' => false]);

        $userRoleServiceMock = Mockery::mock(UserRoleServiceInterface::class);

        $middleware = new RoleMiddleware($userRoleServiceMock);

        $request = Request::create('/permission-role/admin', 'GET');

        $response = $middleware->handle($request, function () {
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
        $userRoleServiceMock = Mockery::mock(UserRoleServiceInterface::class);

        $userRoleServiceMock->shouldReceive('hasRole')
                            ->once()
                            ->with('admin|editor')
                            ->andReturn(true);

        $middleware = new RoleMiddleware($userRoleServiceMock);

        $request = Request::create('/permission-role/admin-or-editor', 'GET');

        $response = $middleware->handle($request, function () {
            return new Response('Next middleware or controller reached');
        }, 'admin|editor');

        $this->assertEquals('Next middleware or controller reached', $response->getContent());
    }

    /**
     * Tests the case where no role is provided to the middleware.
     */
    public function test_middleware_blocks_access_when_no_role_is_provided()
    {
        $userRoleServiceMock = Mockery::mock(UserRoleServiceInterface::class);
        $userRoleServiceMock->shouldReceive('hasRole')
                            ->never();

        $middleware = new RoleMiddleware($userRoleServiceMock);

        $request = Request::create('permission-role/no-role', 'GET');

        $response = $middleware->handle($request, function () {
            return new Response('Next middleware or controller reached');
        }, '');

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(['error' => 'Unauthorized'], $response->original);
    }

    public function test_middleware_blocks_access_when_user_has_no_role()
    {
        $userRoleServiceMock = Mockery::mock(UserRoleServiceInterface::class);
        $userRoleServiceMock->shouldReceive('hasRole')
                            ->once()
                            ->with('admin')
                            ->andReturn(false);

        $middleware = new RoleMiddleware($userRoleServiceMock);

        $request = Request::create('/admin', 'GET');

        $response = $middleware->handle($request, function () {
            return new Response('Next middleware or controller reached');
        }, 'admin');

        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals(['error' => 'Unauthorized'], $response->original);
    }
}