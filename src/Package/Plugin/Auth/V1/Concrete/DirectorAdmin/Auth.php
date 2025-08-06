<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Auth\V1\Concrete\DirectorAdmin;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexAuthorization\Package\Plugin\Auth\V1\Base\Auth as BaseAuth,
    FlexAuthorization\Package\Plugin\Permission\V1\Contract\PermissionManagerContract,
    FlexAuthorization\Package\Plugin\Role\V1\Contract\RoleManagerContract,
    FlexAuthorization\Package\Plugin\User\V1\Contract\UserManagerContract
};

class Auth extends BaseAuth
{
    protected array $directorAdminCapabilities = [
        'manage_operations' => true,
        'approve_content' => true,
        'manage_team' => true,
    ];
    
    public function init(): self
    {
        if ($this->isInitialized) {
            return $this;
        }
        
        $this->setupDirectorAdminRole();
        $this->setInitialized();
        return $this;
    }
    
    public function register(): void
    {
        if ($this->isRegistered) {
            return;
        }
        
        add_filter('user_has_cap', [$this, 'filterDirectorAdminCapabilities'], 10, 4);
        add_action('admin_menu', [$this, 'restrictAdminMenu'], 999);
        
        $this->setRegistered();
    }
    
    protected function setupDirectorAdminRole(): void
    {
        $baseCapabilities = [
            'read' => true,
            'upload_files' => true,
            'edit_posts' => true,
        ];
        
        $capabilities = array_merge($baseCapabilities, $this->directorAdminCapabilities);
        
        $this->roleManager->findOrCreate('director-admin', [
            'name' => 'Director Admin',
            'capabilities' => $capabilities
        ]);
    }
    
    public function filterDirectorAdminCapabilities(array $allcaps, array $caps, array $args, \WP_User $user): array
    {
        if (in_array('director-admin', $user->roles)) {
            foreach ($this->directorAdminCapabilities as $cap => $grant) {
                $allcaps[$cap] = $grant;
            }
        }
        
        return $allcaps;
    }
    
    public function restrictAdminMenu(): void
    {
        if (!current_user_can('director-admin')) {
            return;
        }
        
        global $menu, $submenu;
        
        $allowedMenus = [
            'index.php', // Dashboard
            'edit.php', // Posts
            'upload.php', // Media
            // Add other allowed menus
        ];
        
        foreach ($menu as $index => $item) {
            if (!in_array($item[2], $allowedMenus)) {
                unset($menu[$index]);
            }
        }
    }
}