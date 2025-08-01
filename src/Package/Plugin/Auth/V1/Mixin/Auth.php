<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Auth\V1\Mixin;
    
trait Auth
{
    // show permitted menu items
    public function show_permitted_menu_items(): void 
    {
        global $menu, $submenu;

        foreach ($menu as $menu_item) 
        {
            $menu_title = $menu_item[0];
            $menu_capability = $menu_item[1];
            $menu_slug = $menu_item[2];

            if ((!current_user_can($menu_capability) || (current_user_can($menu_capability) && !current_user_can(base64_encode($menu_slug)))) && !current_user_can('administrator')) 
            {
                remove_menu_page($menu_slug);
                continue;
            }
            
            if (isset($submenu[$menu_slug])) 
            {
                foreach ($submenu[$menu_slug] as $submenu_item) 
                {
                    $submenu_title = $submenu_item[0];
                    $submenu_capability = $submenu_item[1];
                    $submenu_slug = $submenu_item[2];
                    if ((!current_user_can($submenu_capability) || (current_user_can($submenu_capability) && !current_user_can(base64_encode($submenu_slug)))) && !current_user_can('administrator')) 
                    {
                        remove_submenu_page($menu_slug,$submenu_slug);
                    }
                }
            }					
        }
    }

    //Roles functionalities
    public function get_all_roles(): array|null 
    {	
        global $wp_roles;
                        
        $roles = null;
        
        if ($wp_roles && property_exists($wp_roles, 'roles')) 
        {
            $roles = $wp_roles->roles;					
        }

        return $roles;				
    }

    public function user_has_role($role): bool 
    { 
        if( is_user_logged_in() ) 
        {			   
            $user = wp_get_current_user();
        
            $roles = ( array ) $user->roles;

            if(in_array($role,$roles))
            {
                return true;
            }
            else
            {
                return false;
            }	   
        } 
        else
        {			   
            return false;			   
        }
        
    }

    public function add_roles($roles): void 
    {
        // $roles = array(
        // 	'tour_manager' => __( 'Booking Manager' ),
        // );

        foreach ($roles as $role => $display_name) 
        {
            //remove_role($role);
            add_role($role, $display_name);
        }
    }

    public function remove_roles($roles): void 
    {
        foreach ($roles as $role) 
        {
            remove_role($role);
        }
    }

    public function user_has_capability($capability): bool 
    { 
        if(current_user_can($capability))
        {
            return true;
        }
        else
        {
            return false;
        }
        
    }

    public function add_capabilities_to_roles(array $capabilities, array $roles): void 
    {				
        foreach ($roles as $role) 
        {
            $role_object = get_role($role);
            if ($role_object) 
            {
                foreach ($capabilities as $capability) 
                {
                    $role_object->add_cap($capability);
                }
            }

        }
        
    }

    public function remove_capabilities_from_roles(array $capabilities, array $roles): void 
    {

        foreach ($roles as $role) 
        {
            $role_object = get_role($role);
            if ($role_object) 
            {
                foreach ($capabilities as $capability) 
                {
                    $role_object->remove_cap($capability);
                }
            }
        }
        
    }

    public function clone_to_roles($from_role,$to_roles): void
    {
        $from_role = get_role($from_role);
        $from_role_caps = array_keys( $from_role->capabilities );
        
        foreach ( $from_role_caps as $cap ) 
        {
            foreach($to_roles as $new_role)
            {
                unset($role);
                $role = get_role($new_role);
                $role->add_cap( $cap );
            }					
        }
    }

    // Capability functionalities
    public function get_all_capabilities_grouped(): array
    {
        global $wp_roles;
        
        $grouped = [
            'roles' => [],
            'post_types' => [],
            'taxonomies' => [],
            'core' => $this->get_core_capabilities()
        ];
        
        // 1. Get capabilities from all roles
        foreach ($wp_roles->roles as $role_name => $role_data) 
        {
            if (!empty($role_data['capabilities'])) 
            {
                $grouped['roles'][$role_name] = array_keys($role_data['capabilities']);
            }
        }
        
        // 2. Get capabilities from post types
        $post_types = get_post_types([], 'objects');
        foreach ($post_types as $post_type) 
        {
            if (!empty($post_type->cap)) 
            {
                $caps = array_values((array) $post_type->cap);
                $grouped['post_types'][$post_type->name] = array_unique($caps);
            }
        }
        
        // 3. Get capabilities from taxonomies
        $taxonomies = get_taxonomies([], 'objects');
        foreach ($taxonomies as $taxonomy) 
        {
            if (!empty($taxonomy->cap)) 
            {
                $caps = array_values((array) $taxonomy->cap);
                $grouped['taxonomies'][$taxonomy->name] = array_unique($caps);
            }
        }
        
        return $grouped;
    }
    
    /**
     * Get all capabilities as a flat unique sorted array
     */
    public function get_all_possible_capabilities(): array 
    {
        $grouped = $this->get_all_capabilities_grouped();
        $capabilities = [];
        
        foreach ($grouped['roles'] as $role_caps) {
            $capabilities = array_merge($capabilities, $role_caps);
        }
        
        foreach ($grouped['post_types'] as $post_type_caps) {
            $capabilities = array_merge($capabilities, $post_type_caps);
        }
        
        foreach ($grouped['taxonomies'] as $taxonomy_caps) {
            $capabilities = array_merge($capabilities, $taxonomy_caps);
        }
        
        $capabilities = array_merge($capabilities, $grouped['core']);
        
        $capabilities = array_unique(array_filter($capabilities));
        sort($capabilities);
        
        return $capabilities;
    }
    
    /**
     * Get WordPress core capabilities
     */
    protected function get_core_capabilities(): array
    {
        return [
            'activate_plugins', 'create_users', 'delete_plugins', 'delete_themes',
            'delete_users', 'edit_dashboard', 'edit_files', 'edit_plugins',
            'edit_theme_options', 'edit_themes', 'edit_users', 'export',
            'import', 'install_plugins', 'install_themes', 'list_users',
            'manage_options', 'promote_users', 'remove_users', 'switch_themes',
            'update_core', 'update_plugins', 'update_themes',
            'manage_categories', 'moderate_comments', 'unfiltered_html',
            'upload_files', 'read', 'read_private_pages', 'read_private_posts',
            'edit_posts', 'edit_others_posts', 'edit_published_posts',
            'publish_posts', 'delete_posts', 'delete_others_posts',
            'delete_published_posts', 'delete_private_posts', 'edit_private_posts',
            'publish_pages', 'edit_pages', 'edit_others_pages', 'edit_published_pages',
            'delete_pages', 'delete_others_pages', 'delete_published_pages',
            'delete_private_pages', 'edit_private_pages'
        ];
    }
}