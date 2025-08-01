<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Auth\V1\Manager;

(defined( 'ABSPATH' ) && defined( 'WPINC' )) || exit();

use Ababilithub\{
    FlexAuthorization\Package\Plugin\Auth\V1\Manager\Auth as AuthorizationManager
};

class auth
{
    private $auth_manager;

    public function __construct() 
    {
        $this->init();
    }

    public function init(array $data = []): static
    {
        //$this->auth_manager = new AuthorizationManager();
        $this->init_hook();

        return $this;
    }

    private function init_hook(): void 
    {
        // Filter admin menu
        // add_action('admin_menu', [$this->auth_manager, 'filter_admin_menu'], 999);
        
        // // Prevent direct access
        // add_action('current_screen', [$this, 'check_screen_access']);
        
        // // Clean up menu after filtering
        // add_action('adminmenu', [$this, 'clean_menu']);
        // Filter admin menu
        add_action('admin_menu', [$this, 'filter_admin_menu'], 999);
        
        // Prevent direct access
        add_action('current_screen', [$this, 'check_screen_access']);
        
        // Clean up menu after filtering
        add_action('adminmenu', [$this, 'clean_menu']);
    }

    public function filter_admin_menu(): void 
    {
        global $menu, $submenu;

        $userId = get_current_user_id();
        
        // Filter top-level menu
        $menu = array_filter($menu, function($item) use ($userId) {
            $menuSlug = $item[2] ?? '';
            return $this->is_menu_allowed($userId, $menuSlug);
        });

        // Filter submenu items
        foreach ($submenu as $parentSlug => $submenuItems) 
        {
            $submenu[$parentSlug] = array_filter($submenuItems, function($item) use ($userId, $parentSlug) {
                $requiredCap = $item[1] ?? $this->get_menu_capability($parentSlug);
                return $requiredCap ? $this->auth->userCan($userId, $requiredCap) : true;
            });

            // Remove parent if no subitems remain
            if (empty($submenu[$parentSlug])) 
            {
                unset($submenu[$parentSlug]);
            }
        }
    }

    public function check_screen_access(): void 
    {
        $this->auth_manager->prevent_unauthorized_access();
    }

    public function clean_menu(): void 
    {
        global $menu, $submenu;
        
        // Remove empty parent items
        foreach ($menu as $index => $item) 
        {
            if (empty($item[0]) || empty($item[2])) 
            {
                unset($menu[$index]);
            }
        }
        
        // Re-index array
        $menu = array_values($menu);
    }
}