<?php

namespace VoyagerInc\PermissionRole\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;
use VoyagerInc\PermissionRole\Middleware\RoleMiddleware;
use VoyagerInc\PermissionRole\Services\Contract\UserRoleServiceInterface;

class RoleMiddlewareTest extends BaseTest 
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_middleware_allows_access_when_role_matches()
    {
        $userRoleServiceMock = Mockery::mock(UserRoleServiceInterface::class);
        $userRoleServiceMock->shouldReceive('hasRole')
                            ->once()
                            ->with('admin')
                            ->andReturn(true);

        $middleware = new RoleMiddleware($userRoleServiceMock);

        $request = Request::create('/admin', 'GET');

        $response = $middleware->handle($request, function () {
            return new Response('Next middleware or controller reached');
        }, 'admin');

        $this->assertEquals('Next middleware or controller reached', $response->getContent());
    }
}