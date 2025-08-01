<?php 
namespace Ababilithub\FlexAuthorization\Package\Plugin\Role\V1\Concrete\DirectorAdmin;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\FlexAuthorization\Package\Plugin\Role\V1\Base\Role as BaseRole;

class Role extends BaseRole
{
    public function init(array $data = []): static
    {
        $this->role_slug = 'director-admin';
        $this->display_name = 'Director Admin';
        
        // Set specific capabilities for this role
        $this->capabilities = [
            'manage_flex_authorization' => true,
            'edit_flroles' => true,
            'edit_fpermisns' => true,
            'manage_options' => true
        ];
        
        $this->init_hooks();
        return $this;
    }

    protected function init_hooks(): void
    {
        add_action('admin_menu', [$this, 'filter_admin_menu'], 999);
    }

    public function filter_admin_menu(): void
    {
        if (current_user_can('administrator')) 
        {
            return;
        }

        global $menu, $submenu;
        
        $allowed_menus = [
            'admin.php?page=flex-authorization',
            'edit.php?post_type=flrole',
            'edit.php?post_type=fpermisn'
        ];

        foreach ($menu as $index => $menu_item) 
        {
            $menu_slug = $menu_item[2] ?? '';
            
            if (!in_array($menu_slug, $allowed_menus)) 
            {
                remove_menu_page($menu_slug);
            }
        }
    }
}