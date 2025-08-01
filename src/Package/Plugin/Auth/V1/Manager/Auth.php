<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Auth\V1\Manager;

(defined( 'ABSPATH' ) && defined( 'WPINC' )) || exit();

use Ababilithub\{
    FlexAuthorization\Package\Plugin\Auth\V1\Contract\Manager\Auth as AuthorizationContract
};

class Auth 
{
    private $auth;
    private $menuMap = [];
    private $submenuMap = [];

    public function __construct(AuthorizationContract $auth) 
    {
        $this->auth = $auth;
        $this->buildMenuMap();
    }

    private function buildMenuMap(): void
    {
        // Core WordPress menu items
        $this->menuMap = [
            'index.php' => 'view_dashboard',
            'edit.php' => 'manage_posts',
            'upload.php' => 'upload_files',
            'edit-comments.php' => 'moderate_comments',
            'themes.php' => 'switch_themes',
            'plugins.php' => 'activate_plugins',
            'users.php' => 'list_users',
            'tools.php' => 'import',
            'options-general.php' => 'manage_options',
        ];

        // Custom post types
        $postTypes = get_post_types(['show_in_menu' => true], 'objects');
        foreach ($postTypes as $postType) 
        {
            if ($postType->show_in_menu) 
            {
                $this->menuMap[$postType->show_in_menu] = "manage_{$postType->name}";
            }
        }

        // Common submenu items
        $this->submenuMap = [
            'edit.php?post_type=page' => 'manage_pages',
            'edit-tags.php?taxonomy=category' => 'manage_categories',
            'edit-tags.php?taxonomy=post_tag' => 'manage_post_tags',
        ];
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

    public function is_screen_allowed(int $userId, string $screenId): bool 
    {
        // Handle special cases
        if (strpos($screenId, 'edit-') === 0) 
        {
            // Taxonomy screens
            if (strpos($screenId, 'edit-tags') !== false || strpos($screenId, 'term') !== false) 
            {
                $taxonomy = $_GET['taxonomy'] ?? '';
                return $taxonomy ? $this->auth->userCan($userId, "manage_{$taxonomy}") : false;
            }
            // Post type screens
            return $this->auth->userCan($userId, "manage_" . str_replace('edit-', '', $screenId));
        }

        // Default to menu check
        return $this->is_menu_allowed($userId, $screenId);
    }

    private function is_menu_allowed(int $userId, string $menuSlug): bool 
    {
        $requiredCap = $this->get_menu_capability($menuSlug);
        return $requiredCap ? $this->auth->userCan($userId, $requiredCap) : true;
    }

    private function get_menu_capability(string $menuSlug): ?string 
    {
        // Exact match in menu map
        if (isset($this->menuMap[$menuSlug])) 
        {
            return $this->menuMap[$menuSlug];
        }

        // Check submenu map
        if (isset($this->submenuMap[$menuSlug])) 
        {
            return $this->submenuMap[$menuSlug];
        }

        // Handle query strings (e.g., edit.php?post_type=page)
        if (strpos($menuSlug, '?') !== false) 
        {
            parse_str(parse_url($menuSlug, PHP_URL_QUERY), $params);
            
            if (isset($params['post_type'])) {
                return "manage_{$params['post_type']}";
            }
            
            if (isset($params['taxonomy'])) {
                return "manage_{$params['taxonomy']}";
            }
        }

        // Handle special WordPress screens
        if ($menuSlug === 'profile.php' || $menuSlug === 'profile') 
        {
            return 'edit_profile';
        }

        // Default capability for unknown items
        return 'manage_options';
    }

    public function prevent_unauthorized_access(): void 
    {
        if (defined('DOING_AJAX') && DOING_AJAX) 
        {
            return;
        }

        $screen = get_current_screen();
        $userId = get_current_user_id();

        if (!$screen || !$this->is_screen_allowed($userId, $screen->id)) 
        {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
    }
}