<?php 
namespace Ababilithub\FlexAuthorization\Package\Plugin\Role\V1\Concrete\SuperAdministrator;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexAuthorization\Package\Plugin\Role\V1\Base\Role as BaseRole
};

class Role extends BaseRole
{
    public function init(array $data = []): static
    {
        $this->role_slug = 'super-admin';
        $this->display_name = 'Super Admin';
        
        $this->remove_role();
        // Get all capability names
        $capability_names = $this->get_all_possible_capabilities();
        
        // Transform into associative array with all capabilities enabled
        $this->capabilities = array_fill_keys($capability_names, true);
        //echo "<pre>";print_r($this->capabilities);echo "</pre>";exit;
        $this->allowed_menus = array_merge([],[
            base64_encode('admin.php?page=flex-eland') => true,
            //base64_encode('edit.php?post_type=fldeed') => true // Add as main menu
        ]);

        $this->allowed_submenus = array_merge([],[
            base64_encode('admin.php?page=flex-eland') => true,    
            base64_encode('edit.php?post_type=fldeed') => true, // Main listing
            //base64_encode('post-new.php?post_type=fldeed') => true // Add new
        ]);
        
        $this->init_hooks();
        return $this;
    }

    protected function init_hooks(): void
    {
        // add_filter(
        //     'ababilithub_role_super_admin_redirect_url', 
        //     [$this, 'filter_super_admin_redirect_url'], 
        //     10, 
        //     3
        // );

        //add_action('admin_init', [$this, 'handle_admin_redirect']);
        //add_action('admin_menu', [$this, 'filter_admin_menu'], 999);
        //add_action('wp_dashboard_setup', [$this, 'dashboard_setup'], 999);
    }

    public function filter_admin_menu(): void
    {
        if (!current_user_can($this->role_slug)) 
        {
            return;
        }

        global $menu, $submenu;
        foreach ($menu as $index => $menu_item) 
        {
            $menu_capability = $menu_item[1] ?? '';
            $menu_slug = $menu_item[2] ?? '';
            $encoded_menu_slug = base64_encode($menu_slug);
            if (!isset($this->capabilities[$menu_capability]) && !isset($this->allowed_menus[$encoded_menu_slug])) 
            {              
                //remove_menu_page($menu_slug);
            }
        }
        
        foreach ($submenu as $parent_slug => $submenu_items) 
        {
            foreach ($submenu_items as $subindex => $submenu_item) 
            {
                $submenu_capability = $submenu_item[1] ?? '';
                $submenu_slug = $submenu_item[2] ?? '';
                $encoded_submenu_slug = base64_encode($submenu_slug);
                if (!isset($this->capabilities[$submenu_capability]) && !isset($this->allowed_submenus[$encoded_submenu_slug])) 
                { 
                    //remove_submenu_page($parent_slug, $submenu_slug);
                }
            }
        }
        
    }

    public function dashboard_setup(): void
    {
        if (current_user_can($this->role_slug)) 
        {
            global $wp_meta_boxes;
            // Remove all dashboard widgets except custom ones
            foreach ($wp_meta_boxes['dashboard'] as $context => $priority_array) 
            {
                foreach ($priority_array as $priority => $boxes) 
                {
                    foreach ($boxes as $id => $data) 
                    {
                        remove_meta_box($id, 'dashboard', $context);
                    }
                }
            }
        }
    }

    public function dashboard_setup_p(): void
    {
        if (current_user_can($this->role_slug)) 
        {
            global $wp_meta_boxes;
            
            // Remove only unnecessary widgets
            $keep_widgets = [
                'dashboard_activity',
                'dashboard_right_now',
                'dashboard_site_health',
                'dashboard_quick_press'
            ];
            
            foreach ($wp_meta_boxes['dashboard'] as $context => $priority_array) 
            {
                foreach ($priority_array as $priority => $boxes) 
                {
                    foreach ($boxes as $id => $data) 
                    {
                        if (!in_array($id, $keep_widgets)) 
                        {
                            remove_meta_box($id, 'dashboard', $context);
                        }
                    }
                }
            }
        }
    }

    public function filter_super_admin_redirect_url(
        string $url,
        string $role_slug,
        $role_instance
    ): string 
    {
        // Only modify if it's our role
        if ($role_slug === $this->role_slug) 
        {
            return 'admin.php?page=flex-eland';
        }
        
        return $url;
    }

    /**
     * Handle admin redirect for super admin
     */
    public function handle_admin_redirect(): void
    {
        if (wp_doing_ajax() || !current_user_can($this->role_slug)) 
        {
            return;
        }

        global $pagenow;
        $current_page = $_GET['page'] ?? '';
        $post_type = $_GET['post_type'] ?? '';
        $screen = get_current_screen();

        // Define allowed pages and conditions
        $allowed_conditions = [
            'admin.php?page=flex-eland',
            'edit.php?post_type=fldeed',
            'post-new.php?post_type=fldeed'
        ];

        // Check if we're already on an allowed page
        $is_allowed = false;
        foreach ($allowed_conditions as $condition) 
        {
            parse_str(parse_url($condition, PHP_URL_QUERY), $params);
            $matches = true;
            
            foreach ($params as $key => $value) 
            {
                if (
                    ($key === 'page' && ($current_page !== $value)) ||
                    ($key === 'post_type' && ($post_type !== $value))
                ) 
                {
                    $matches = false;
                    break;
                }
            }
            
            if ($matches && $pagenow === parse_url($condition, PHP_URL_PATH)) 
            {
                $is_allowed = true;
                break;
            }
        }

        // Also check by screen ID if available
        if ($screen && in_array($screen->id, ['toplevel_page_flex-eland', 'fldeed', 'edit-fldeed'])) 
        {
            $is_allowed = true;
        }

        if ($is_allowed) {
            return;
        }

        // Apply the redirect URL filter and ensure it's a full URL
        $redirect_path = apply_filters(
            'ababilithub_role_super_admin_redirect_url',
            'admin.php?page=flex-eland',
            $this->role_slug,
            $this
        );

        // Convert to full URL if not already
        $redirect_url = filter_var($redirect_path, FILTER_VALIDATE_URL) 
            ? $redirect_path 
            : admin_url($redirect_path);

        // Only redirect if we're not already on the target URL
        $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if ($current_url !== $redirect_url) 
        {
            wp_safe_redirect($redirect_url);
            exit;
        }
    }
}