<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Auth\V1\Service;

(defined( 'ABSPATH' ) && defined( 'WPINC' )) || exit();

use Ababilithub\{
    FlexAuthorization\Package\Plugin\Auth\V1\Manager\Auth as AuthorizationManager
};
class Auth implements AuthorizationContract 
{
    private $permissionManager;
    private $roleManager;
    private $userManager;

    public function __construct(
        PermissionManagerContract $permissionManager,
        RoleManagerContract $roleManager,
        UserManagerContract $userManager
    ) {
        $this->permissionManager = $permissionManager;
        $this->roleManager = $roleManager;
        $this->userManager = $userManager;
    }

    public function userCan(int $userId, string $permissionSlug): bool {
        return $this->userManager->hasPermission($userId, $permissionSlug);
    }

    public function permissions(): PermissionManagerContract {
        return $this->permissionManager;
    }

    public function roles(): RoleManagerContract {
        return $this->roleManager;
    }

    public function users(): UserManagerContract {
        return $this->userManager;
    }
}