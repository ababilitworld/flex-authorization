<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Auth\V1\Contract\Authorization;

interface Auth
{
    public function userCan(int $userId, string $permissionSlug): bool;
    public function permissions(): PermissionManagerContract;
    public function roles(): RoleManagerContract;
    public function users(): UserManagerContract;
}