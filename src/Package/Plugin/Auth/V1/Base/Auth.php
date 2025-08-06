<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Auth\V1\Base;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexAuthorization\Package\Plugin\Auth\V1\Contract\Auth as AuthContract,
    FlexAuthorization\Package\Plugin\Permission\V1\Contract\PermissionManagerContract,
    FlexAuthorization\Package\Plugin\Role\V1\Contract\RoleManagerContract,
    FlexAuthorization\Package\Plugin\User\V1\Contract\UserManagerContract
};

abstract class Auth implements AuthContract
{
    protected PermissionManagerContract $permissionManager;
    protected RoleManagerContract $roleManager;
    protected UserManagerContract $userManager;
    
    protected bool $isInitialized = false;
    protected bool $isRegistered = false;
    
    public function __construct(
        PermissionManagerContract $permissionManager,
        RoleManagerContract $roleManager,
        UserManagerContract $userManager
    ) {
        $this->permissionManager = $permissionManager;
        $this->roleManager = $roleManager;
        $this->userManager = $userManager;
    }
    
    public function roleCan(int $roleId, string $permissionSlug): bool
    {
        if (!$this->isInitialized) {
            throw new \RuntimeException('Auth system not initialized');
        }
        
        $role = $this->roleManager->find($roleId);
        return $role && $role->hasPermission($permissionSlug);
    }
    
    public function userCan(int $userId, string $permissionSlug): bool
    {
        if (!$this->isInitialized) {
            throw new \RuntimeException('Auth system not initialized');
        }
        
        $user = $this->userManager->find($userId);
        return $user && $user->hasPermission($permissionSlug);
    }
    
    public function permissions(): PermissionManagerContract
    {
        return $this->permissionManager;
    }
    
    public function roles(): RoleManagerContract
    {
        return $this->roleManager;
    }
    
    public function users(): UserManagerContract
    {
        return $this->userManager;
    }
    
    abstract public function init(): self;
    abstract public function register(): void;
    
    protected function setInitialized(): void
    {
        $this->isInitialized = true;
    }
    
    protected function setRegistered(): void
    {
        $this->isRegistered = true;
    }
}