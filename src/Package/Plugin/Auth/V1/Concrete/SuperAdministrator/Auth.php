<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Auth\V1\Concrete\SuperAdministrator;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexAuthorization\Package\Plugin\Auth\V1\Base\Auth as BaseAuth,
    FlexAuthorization\Package\Plugin\Permission\V1\Contract\PermissionManagerContract,
    FlexAuthorization\Package\Plugin\Role\V1\Contract\RoleManagerContract,
    FlexAuthorization\Package\Plugin\User\V1\Contract\UserManagerContract
};

class Auth extends BaseAuth
{
    public function init(): self
    {
        if ($this->isInitialized) {
            return $this;
        }
        
        // Initialize super admin specific capabilities
        $this->setupSuperAdminCapabilities();
        
        $this->setInitialized();
        return $this;
    }
    
    public function register(): void
    {
        if ($this->isRegistered) {
            return;
        }
        
        // Register hooks and filters
        add_filter('user_has_cap', [$this, 'grantSuperAdminCapabilities'], 10, 4);
        add_action('admin_menu', [$this, 'configureAdminMenu'], 999);
        
        $this->setRegistered();
    }
    
    protected function setupSuperAdminCapabilities(): void
    {
        // Get all existing capabilities
        $allCapabilities = $this->getAllPossibleCapabilities();
        
        // Ensure super admin role exists
        $role = $this->roleManager->findOrCreate('super-admin', [
            'name' => 'Super Administrator',
            'capabilities' => array_fill_keys($allCapabilities, true)
        ]);
    }
    
    public function grantSuperAdminCapabilities(array $allcaps, array $caps, array $args, \WP_User $user): array
    {
        if (in_array('super-admin', $user->roles)) {
            // Grant all capabilities
            $allcaps = array_fill_keys(array_keys($allcaps), true);
        }
        
        return $allcaps;
    }
    
    public function configureAdminMenu(): void
    {
        global $menu, $submenu;
        
        if (current_user_can('super-admin')) {
            // Super admin can see all menus
            return;
        }
        
        // Restrict menus for other roles if needed
    }
    
    protected function getAllPossibleCapabilities(): array
    {
        // Implementation to get all WP capabilities
        // This should include core, plugin, and theme capabilities
        return [
            'manage_options',
            'edit_users',
            // ... all other capabilities
        ];
    }
}