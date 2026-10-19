<?php

namespace Tests\Unit;

use App\Http\Middleware\RoleAccess;
use App\Http\Middleware\WebsiteAdminAccess;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    private function userWithPermissions(array $keys = [], string $roleKey = 'donor'): User
    {
        $role = new Role(['key' => $roleKey]);
        $role->setRelation('permissions', new Collection(array_map(fn ($key) => new Permission(['key' => $key]), $keys)));
        $user = new User(['name' => 'Test', 'active' => true]);
        $user->setRelation('roles', new Collection([$role]));
        return $user;
    }

    private function requestFor(User $user, ?string $name): Request
    {
        $request = Request::create('/api/v1/users/1/roles/1', 'POST');
        $route = new Route('POST', 'api/v1/users/{user}/roles/{role}', fn () => null);
        if ($name) $route->name($name);
        $request->setRouteResolver(fn () => $route);
        $request->setUserResolver(fn () => $user);
        return $request;
    }

    public function test_donor_cannot_assign_admin_role(): void
    {
        $this->expectException(HttpException::class);
        (new RoleAccess)->handle($this->requestFor($this->userWithPermissions(), 'users.attachRole'), fn () => $this->fail('Unauthorized role assignment passed'));
    }

    public function test_editing_users_does_not_authorize_editing_roles(): void
    {
        $this->expectException(HttpException::class);
        (new RoleAccess)->handle($this->requestFor($this->userWithPermissions(['users.edit']), 'users.detachRole'), fn () => $this->fail('Unauthorized role removal passed'));
    }

    public function test_role_editor_can_assign_roles(): void
    {
        $response = (new RoleAccess)->handle($this->requestFor($this->userWithPermissions(['roles.edit']), 'users.attachRole'), fn () => response('allowed'));
        $this->assertSame('allowed', $response->getContent());
    }

    public function test_unnamed_route_fails_closed_for_non_admin(): void
    {
        $this->expectException(HttpException::class);
        (new RoleAccess)->handle($this->requestFor($this->userWithPermissions(), null), fn () => $this->fail('Unnamed route passed'));
    }

    public function test_website_admin_lists_require_authentication(): void
    {
        $this->getJson('/api/v1/admin/website/complaints')->assertUnauthorized();
        $this->getJson('/api/v1/admin/website/donations')->assertUnauthorized();
        $this->getJson('/api/v1/admin/website/donation-accounts')->assertUnauthorized();
    }

    public function test_mobile_staff_actions_have_explicit_permissions(): void
    {
        $routes = app('router')->getRoutes();
        foreach ([
            ['GET', '/api/v1/mobile/employee/volunteer-requests', 'mobile.volunteer_requests.view'],
            ['POST', '/api/v1/mobile/employee/news', 'mobile.news.create'],
        ] as [$method, $uri, $permission]) {
            $route = $routes->match(Request::create($uri, $method));
            $this->assertContains('api_permission:'.$permission, $route->gatherMiddleware());
        }
    }

    public function test_mysql_is_the_only_configured_sql_connection(): void
    {
        $this->assertSame('mysql', config('database.default'));
        $this->assertSame(['mysql'], array_keys(config('database.connections')));
    }
}
