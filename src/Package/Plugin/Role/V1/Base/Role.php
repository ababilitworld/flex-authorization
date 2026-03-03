<?php 
namespace Ababilithub\FlexAuthorization\Package\Plugin\Role\V1\Base;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexAuthorization\Package\Plugin\Mixin\Auth as AuthMixin,
    FlexAuthorization\Package\Plugin\Role\V1\Contract\Role as RoleContract
};

abstract class Role implements RoleContract
{
    //use AuthMixin;
    public $role_slug;
    public $display_name;
    public $capabilities = [];
    public $default_capabilities = [];
    public $allowed_menu = [];
    public $allowed_submenu = [];    
    public $allowed_posttypes = [];
    public $wp_role;
    public $redirect_url;

    public function __construct()
    {
        $this->init();
    }

    abstract public function init(array $data = []): static;

    public function register(): void
    {
        $this->create_role();
    }
    
    public function create_role(): void
    {        
        $this->remove_all_capabilities();

        $this->remove_role();

        //echo "<pre>"; print_r($this->get_all_capabilities_grouped()); echo "</pre>";exit;

        // Add the role with capabilities
        add_role($this->role_slug, $this->display_name, $this->capabilities);
        
        // Store the WP_Role object
        $this->wp_role = get_role($this->role_slug);
    }

    public function clone_role($from_role,$to_roles): void
    {
        $from_role = get_role($from_role);
        $from_role_caps = array_keys( $from_role->capabilities );        

        foreach($to_roles as $new_role)
        {
            unset($role);
            $role = get_role($new_role);
            foreach ( $from_role_caps as $cap ) 
            {
                $role->add_cap( $cap );
            }					
        }
    }

    public function get_label(): string
    {
        $roles = wp_roles();
        return $roles->role_names[$this->role_slug] ?? $this->display_name;
    }

    public function remove_role(): void
    {
        // Remove existing role if it exists
        $role = get_role($this->role_slug);
        if (isset($role)) 
        {
            remove_role($this->role_slug);
        }
    }

    public function get_default_capabilities():array
    {
        return $this->default_capabilities = [
            'read' => true,
            'view_admin_dashboard' => true,
        ];
    }

    public function add_capabilities(array $capabilities = []): void
    {
        $role = get_role($this->role_slug);
        if(isset($role) && count($capabilities))
        {
            foreach ($capabilities as $capability => $value) 
            {
                if ($value) 
                {
                    $role->add_cap($capability);
                } 
                else
                {
                    $role->remove_cap($capability);
                }
            }
        }
    }

    public function remove_specified_capabilities(array $capabilities = []): void
    {
        $role = get_role($this->role_slug);
        if(isset($role) && count($role->capabilities) && count($capabilities))
        {
            foreach ($capabilities as $capability => $value) 
            {
                $role->remove_cap($capability);
            }
        }
    }

    public function remove_all_capabilities(): void
    {
        $role = get_role($this->role_slug);
        if(isset($role) && count($role->capabilities))
        {
            foreach ($role->capabilities as $capability => $value) 
            {
                $role->remove_cap($capability);
            }
        }
    }

    public function get_all_capabilities_grouped(): array
    {
        global $wp_roles;
        
        $grouped = [
            'roles' => [],
            'post_types' => [],
            'taxonomies' => [],
            'core' => $this->get_core_capabilities(),
            'user' => [], // User level capabilities
            'meta' => [], // Meta capabilities
            'plugins' => [], // Plugin-specific capabilities
            'themes' => [], // Theme-specific capabilities
            'multisite' => [], // Multisite capabilities
            'primitives' => [], // Primitive capabilities (manage_options, etc)
            'custom' => [] // Catch-all for any other capabilities
        ];
        
        // Track all unique capabilities to identify duplicates
        $all_capabilities = [];
        
        // 1. Get capabilities from all roles
        foreach ($wp_roles->roles as $role_name => $role_data) 
        {
            if (!empty($role_data['capabilities'])) 
            {
                $caps = array_keys($role_data['capabilities']);
                $grouped['roles'][$role_name] = $caps;
                $all_capabilities = array_merge($all_capabilities, $caps);
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
                $all_capabilities = array_merge($all_capabilities, $caps);
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
                $all_capabilities = array_merge($all_capabilities, $caps);
            }
        }
        
        // 4. User capabilities (edit_user, delete_user, etc)
        $grouped['user'] = [
            'list_users',
            'create_users',
            'edit_users',
            'delete_users',
            'remove_users',
            'promote_users',
            'add_users',
            'edit_user',
            'delete_user'
        ];
        $all_capabilities = array_merge($all_capabilities, $grouped['user']);
        
        // 5. Meta capabilities (often mapped but not directly assigned)
        $grouped['meta'] = [
            'edit_post',
            'read_post',
            'delete_post',
            'edit_page',
            'read_page',
            'delete_page',
            'edit_comment',
            'publish_post',
            'read',
            'upload_files',
            'edit_files',
            'edit_published_posts',
            'edit_private_posts',
            'edit_others_posts',
            'publish_pages',
            'edit_published_pages',
            'edit_private_pages',
            'edit_others_pages',
            'delete_posts',
            'delete_private_posts',
            'delete_published_posts',
            'delete_others_posts',
            'delete_pages',
            'delete_private_pages',
            'delete_published_pages',
            'delete_others_pages'
        ];
        $all_capabilities = array_merge($all_capabilities, $grouped['meta']);
        
        // 6. Primitive capabilities (usually from core)
        $grouped['primitives'] = [
            'manage_options',
            'activate_plugins',
            'deactivate_plugins',
            'install_plugins',
            'update_plugins',
            'delete_plugins',
            'edit_plugins',
            'upload_plugins',
            'manage_network',
            'manage_network_options',
            'manage_network_plugins',
            'manage_network_themes',
            'manage_network_users',
            'install_themes',
            'update_themes',
            'delete_themes',
            'edit_themes',
            'edit_theme_options',
            'customize',
            'edit_dashboard',
            'read',
            'export',
            'import',
            'manage_categories',
            'edit_categories',
            'delete_categories',
            'manage_links',
            'moderate_comments',
            'manage_comments',
            'unfiltered_html',
            'edit_files',
            'upload_files',
            'unfiltered_upload'
        ];
        $all_capabilities = array_merge($all_capabilities, $grouped['primitives']);
        
        // 7. Get multisite capabilities if in multisite
        if (is_multisite()) {
            $grouped['multisite'] = [
                'manage_sites',
                'create_sites',
                'delete_sites',
                'manage_network',
                'manage_site_options',
                'manage_network_users',
                'manage_network_plugins',
                'manage_network_themes',
                'manage_network_options',
                'upload_space',
                'view_site_activity'
            ];
            $all_capabilities = array_merge($all_capabilities, $grouped['multisite']);
        }
        
        // 8. Get plugin-specific capabilities (from active plugins)
        $grouped['plugins'] = $this->get_plugin_capabilities();
        if (!empty($grouped['plugins'])) {
            foreach ($grouped['plugins'] as $plugin_caps) {
                $all_capabilities = array_merge($all_capabilities, $plugin_caps);
            }
        }
        
        // 9. Get theme-specific capabilities (from active theme)
        $grouped['themes'] = $this->get_theme_capabilities();
        if (!empty($grouped['themes'])) {
            $all_capabilities = array_merge($all_capabilities, $grouped['themes']);
        }
        
        // 10. Find custom capabilities (capabilities that exist but aren't in other groups)
        $all_capabilities = array_unique($all_capabilities);
        $grouped_capabilities = array_merge(
            $grouped['core'] ?? [],
            $grouped['user'],
            $grouped['meta'],
            $grouped['primitives'],
            $grouped['multisite'] ?? []
        );
        
        // Flatten post type and taxonomy capabilities
        foreach ($grouped['post_types'] as $pt_caps) {
            $grouped_capabilities = array_merge($grouped_capabilities, $pt_caps);
        }
        foreach ($grouped['taxonomies'] as $tax_caps) {
            $grouped_capabilities = array_merge($grouped_capabilities, $tax_caps);
        }
        foreach ($grouped['plugins'] as $plugin_caps) {
            $grouped_capabilities = array_merge($grouped_capabilities, $plugin_caps);
        }
        foreach ($grouped['themes'] as $theme_caps) {
            $grouped_capabilities = array_merge($grouped_capabilities, $theme_caps);
        }
        
        $grouped_capabilities = array_unique($grouped_capabilities);
        $grouped['custom'] = array_diff($all_capabilities, $grouped_capabilities);
        
        return $grouped;
    }

    /**
     * Get capabilities from active plugins
     */
    private function get_plugin_capabilities(): array
    {
        $plugin_caps = [];
        $active_plugins = get_option('active_plugins', []);
        
        foreach ($active_plugins as $plugin) {
            $plugin_name = dirname($plugin);
            if ($plugin_name === '.') {
                $plugin_name = basename($plugin, '.php');
            }
            
            // This is where you'd parse plugin headers or known plugin capabilities
            // For now, we'll check for common plugin capability patterns
            $plugin_caps[$plugin_name] = $this->scan_plugin_for_capabilities($plugin);
        }
        
        return $plugin_caps;
    }

    /**
     * Scan a plugin file for capability definitions
     */
    private function scan_plugin_for_capabilities($plugin_file): array
    {
        $caps = [];
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
        
        if (file_exists($plugin_path)) {
            $content = file_get_contents($plugin_path);
            
            // Look for common capability patterns
            preg_match_all('/current_user_can\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches);
            if (!empty($matches[1])) {
                $caps = array_merge($caps, $matches[1]);
            }
            
            // Look for capability definitions
            preg_match_all('/add_cap\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches);
            if (!empty($matches[1])) {
                $caps = array_merge($caps, $matches[1]);
            }
            
            // Look in plugin headers for capability declarations
            $plugin_data = get_plugin_data($plugin_path);
            // Some plugins declare capabilities in headers
        }
        
        return array_unique($caps);
    }

    /**
     * Get capabilities from active theme
     */
    private function get_theme_capabilities(): array
    {
        $theme_caps = [];
        $theme = wp_get_theme();
        
        if ($theme->exists()) {
            // Check theme's functions.php and other files for capabilities
            $theme_files = [
                $theme->get_template_directory() . '/functions.php',
                $theme->get_template_directory() . '/inc/customizer.php',
            ];
            
            foreach ($theme_files as $file) {
                if (file_exists($file)) {
                    $content = file_get_contents($file);
                    
                    // Look for common capability patterns
                    preg_match_all('/current_user_can\(\s*[\'"]([^\'"]+)[\'"]/', $content, $matches);
                    if (!empty($matches[1])) {
                        $theme_caps = array_merge($theme_caps, $matches[1]);
                    }
                }
            }
        }
        
        return array_unique($theme_caps);
    }

    /**
     * Get core capabilities
     */
    private function get_core_capabilities(): array
    {
        return [
            // Administrator capabilities
            'manage_options',
            'activate_plugins',
            'deactivate_plugins',
            'install_plugins',
            'update_plugins',
            'delete_plugins',
            'edit_plugins',
            'upload_plugins',
            
            // Editor capabilities
            'moderate_comments',
            'manage_categories',
            'manage_links',
            'edit_others_posts',
            'edit_published_posts',
            'edit_posts',
            'delete_others_posts',
            'delete_published_posts',
            'delete_posts',
            'publish_posts',
            'edit_pages',
            'edit_others_pages',
            'edit_published_pages',
            'publish_pages',
            'delete_pages',
            'delete_others_pages',
            'delete_published_pages',
            'delete_private_pages',
            'edit_private_pages',
            'read_private_pages',
            'delete_private_posts',
            'edit_private_posts',
            'read_private_posts',
            
            // Author capabilities
            'upload_files',
            'edit_posts',
            'edit_published_posts',
            'delete_posts',
            'delete_published_posts',
            'publish_posts',
            
            // Contributor capabilities
            'edit_posts',
            'delete_posts',
            'read',
            
            // Subscriber capabilities
            'read',
            
            // Common meta capabilities
            'edit_post',
            'read_post',
            'delete_post',
            'edit_comment',
            'edit_user',
            'delete_user',
        ];
    }
    
    /**
     * Get all capabilities as a flat unique sorted array
     */
    public function get_all_possible_capabilities(): array 
    {
        $grouped = $this->get_all_capabilities_grouped();
        $capabilities = [];
        
        // 1. Add capabilities from all roles
        foreach ($grouped['roles'] as $role_caps) 
        {
            $capabilities = array_merge($capabilities, $role_caps);
        }
        
        // 2. Add capabilities from all post types
        foreach ($grouped['post_types'] as $post_type_caps) 
        {
            $capabilities = array_merge($capabilities, $post_type_caps);
        }
        
        // 3. Add capabilities from all taxonomies
        foreach ($grouped['taxonomies'] as $taxonomy_caps) 
        {
            $capabilities = array_merge($capabilities, $taxonomy_caps);
        }
        
        // 4. Add core capabilities
        $capabilities = array_merge($capabilities, $grouped['core']);
        
        // 5. Add user capabilities
        $capabilities = array_merge($capabilities, $grouped['user']);
        
        // 6. Add meta capabilities
        $capabilities = array_merge($capabilities, $grouped['meta']);
        
        // 7. Add primitive capabilities
        $capabilities = array_merge($capabilities, $grouped['primitives']);
        
        // 8. Add multisite capabilities (if they exist)
        if (!empty($grouped['multisite'])) 
        {
            $capabilities = array_merge($capabilities, $grouped['multisite']);
        }
        
        // 9. Add plugin capabilities (flatten the array)
        foreach ($grouped['plugins'] as $plugin_caps) 
        {
            $capabilities = array_merge($capabilities, $plugin_caps);
        }
        
        // 10. Add theme capabilities
        $capabilities = array_merge($capabilities, $grouped['themes']);
        
        // 11. Add custom capabilities (capabilities found but not in other groups)
        $capabilities = array_merge($capabilities, $grouped['custom']);
        
        // Clean up and sort
        $capabilities = array_values(array_unique(array_filter($capabilities)));
        sort($capabilities);
        
        return $capabilities;
    }

    public function get_core_capabilities_by_group(): array
    {
        return [
            'plugin_management' => $this->get_core_plugin_capabilities(),
            'theme_management' => $this->get_core_theme_capabilities(),
            'user_management' => $this->get_core_user_capabilities(),
            'system_operations' => $this->get_core_system_capabilities(),
            'content_management' => $this->get_core_content_capabilities(),
            'post_capabilities' => $this->get_core_posttype_capabilities(),
            'page_capabilities' => $this->get_core_page_capabilities()
        ];
    }

    public function get_core_plugin_capabilities(): array
    {
        return [
            'activate_plugins' => true,
            'delete_plugins' => true,
            'edit_plugins' => true,
            'install_plugins' => true,
            'update_plugins' => true
        ];
    }

    public function get_core_theme_capabilities(): array
    {
        return [
            'delete_themes' => true,
            'edit_themes' => true,
            'edit_theme_options' => true,
            'install_themes' => true,
            'switch_themes' => true,
            'update_themes' => true
        ];
    }

    public function get_core_user_capabilities(): array
    {
        return [
            'create_users' => true,
            'delete_users' => true,
            'edit_users' => true,
            'list_users' => true,
            'promote_users' => true,
            'remove_users' => true
        ];
    }

    public function get_core_system_capabilities(): array
    {
        return [
            'update_core' => true,
            'edit_dashboard' => true,
            'edit_files' => true,
            'export' => true,
            'import' => true,
            'manage_options' => true
        ];
    }

    public function get_core_content_capabilities(): array
    {
        return [
            'manage_categories' => true,
            'moderate_comments' => true,
            'unfiltered_html' => true,
            'upload_files' => true
        ];
    }

    public function get_core_page_capabilities(): array
    {
        return [
            'edit_pages' => true,
            'edit_others_pages' => true,
            'edit_published_pages' => true,
            'edit_private_pages' => true,
            'publish_pages' => true,
            'delete_pages' => true,
            'delete_others_pages' => true,
            'delete_published_pages' => true,
            'delete_private_pages' => true,
            'read_private_pages' => true
        ];
    }

    public function get_core_posttype_capabilities(): array
    {
        return [

            'read' => true,
            'read_post' => true,
            'read_private_posts' => true,

            "create_posts" => true,

            'edit_post' => true,
            'edit_posts' => true,
            'edit_others_posts' => true,
            'edit_private_posts' => true,
            'edit_published_posts' => true,

            'publish_posts' => true,
            
            'delete_post' => true,            
            'delete_posts' => true,
            'delete_private_posts' => true,
            'delete_published_posts' => true,
            'delete_others_posts' => true,
        ];
    }

    public function map_core_posttype_capabilities(): array
    {
        return [

            'read' => true,
            'read_post' => true,
            'read_private_posts' => true,

            "create_posts" => true,

            'edit_post' => true,
            'edit_posts' => true,
            'edit_others_posts' => true,
            'edit_private_posts' => true,
            'edit_published_posts' => true,

            'publish_posts' => true,
            
            'delete_post' => true,            
            'delete_posts' => true,
            'delete_private_posts' => true,
            'delete_published_posts' => true,
            'delete_others_posts' => true,
        ];
    }

    public function map_custom_posttype_capabilities(string $posttype, string $posttype_singular_base, string $posttype_plural_base): array
    {
        return [ 
            $posttype => [

                "read" => true,
                "read_{$posttype_singular_base}" => true,
                "read_private_{$posttype_plural_base}" => true,

                "create_{$posttype_plural_base}" => true,

                "edit_{$posttype_singular_base}" => true,
                "edit_{$posttype_plural_base}" => true,
                "edit_others_{$posttype_plural_base}" => true,
                "edit_private_{$posttype_plural_base}" => true,
                "edit_published_{$posttype_plural_base}" => true,

                "publish_{$posttype_plural_base}" => true,
                
                "delete_{$posttype_singular_base}" => true,            
                "delete_{$posttype_plural_base}" => true,
                "delete_private_{$posttype_plural_base}" => true,
                "delete_published_{$posttype_plural_base}" => true,
                "delete_others_{$posttype_plural_base}" => true,
        
            ]
        ];
    }

    public function map_custom_posttype_capabilities_to_core(string $posttype_singular_base, string $posttype_plural_base): array
    {
        return [

            'read' => true,
            'read_post' => "read_{$posttype_singular_base}",
            'read_private_posts' => "read_private_{$posttype_plural_base}",

            "create_posts" => true,

            'edit_post' => "edit_{$posttype_singular_base}",
            'edit_posts' => "edit_{$posttype_plural_base}",
            'edit_others_posts' => "edit_others_{$posttype_plural_base}",
            'edit_private_posts' => "edit_private_{$posttype_plural_base}",
            'edit_published_posts' => "edit_published_{$posttype_plural_base}",

            'publish_posts' => "publish_{$posttype_plural_base}",
            
            'delete_post' => "delete_{$posttype_singular_base}",           
            'delete_posts' => "delete_{$posttype_plural_base}",
            'delete_private_posts' => "delete_private_{$posttype_plural_base}",
            'delete_published_posts' => "delete_published_{$posttype_plural_base}",
            'delete_others_posts' => "delete_others_{$posttype_plural_base}",
        ];

    }

    public function format_allowed_posttype_capabilities( string $mode = "replace", string $output_option = "without_key"): array
    {
        $formatted_allowed_posttype_capabilities = [];

        if(is_array($this->allowed_posttypes) && count($this->allowed_posttypes))
        {
            foreach ($this->allowed_posttypes as $key => $value) 
            {
                $singular_base = $value['singular_base'];
                $plural_base = $value['plural_base'];

                if($mode == "replace" && $output_option == "with_key")
                {
                    $temporary = [];
                    $temporary[$key] = [
                        "read" => ($value['capabilities']['read'] == true) ? true : false,
                        "read_{$singular_base}" => ($value['capabilities']['read_post'] == true) ? true : false,
                        "read_private_{$plural_base}" => ($value['capabilities']['read_private_posts'] == true) ? true : false,
                        
                        "create_{$plural_base}" => ($value['capabilities']['create_posts'] == true) ? true : false,
                        
                        "edit_{$singular_base}" => ($value['capabilities']['edit_post'] == true) ? true : false,
                        "edit_{$plural_base}" => ($value['capabilities']['edit_posts'] == true) ? true : false,
                        "edit_others_{$plural_base}" => ($value['capabilities']['edit_others_posts'] == true) ? true : false,
                        "edit_private_{$plural_base}" => ($value['capabilities']['edit_private_posts'] == true) ? true : false,
                        "edit_published_{$plural_base}" => ($value['capabilities']['edit_published_posts'] == true) ? true : false,
                        
                        "publish_{$plural_base}" => ($value['capabilities']['publish_posts'] == true) ? true : false,
                        
                        "delete_{$singular_base}" => ($value['capabilities']['delete_post'] == true) ? true : false,
                        "delete_{$plural_base}" => ($value['capabilities']['delete_posts'] == true) ? true : false,
                        "delete_private_{$plural_base}" => ($value['capabilities']['delete_private_posts'] == true) ? true : false,
                        "delete_published_{$plural_base}" => ($value['capabilities']['delete_published_posts'] == true) ? true : false,
                        "delete_others_{$plural_base}" => ($value['capabilities']['delete_others_posts'] == true) ? true : false,
                    ];

                    $formatted_allowed_posttype_capabilities = array_merge(
                        $formatted_allowed_posttype_capabilities,
                        $temporary
                    );
                    
                }
                elseif($mode == "replace" && $output_option == "without_key") 
                {
                    $formatted_allowed_posttype_capabilities = array_merge(
                        $formatted_allowed_posttype_capabilities,
                        [
                            "read" => ($value['capabilities']['read'] == true) ? true : false,
                            "read_{$singular_base}" => ($value['capabilities']['read_post'] == true) ? true : false,
                            "read_private_{$plural_base}" => ($value['capabilities']['read_private_posts'] == true) ? true : false,
                            
                            "create_{$plural_base}" => ($value['capabilities']['create_posts'] == true) ? true : false,
                            
                            "edit_{$singular_base}" => ($value['capabilities']['edit_post'] == true) ? true : false,
                            "edit_{$plural_base}" => ($value['capabilities']['edit_posts'] == true) ? true : false,
                            "edit_others_{$plural_base}" => ($value['capabilities']['edit_others_posts'] == true) ? true : false,
                            "edit_private_{$plural_base}" => ($value['capabilities']['edit_private_posts'] == true) ? true : false,
                            "edit_published_{$plural_base}" => ($value['capabilities']['edit_published_posts'] == true) ? true : false,
                            
                            "publish_{$plural_base}" => ($value['capabilities']['publish_posts'] == true) ? true : false,
                            
                            "delete_{$singular_base}" => ($value['capabilities']['delete_post'] == true) ? true : false,
                            "delete_{$plural_base}" => ($value['capabilities']['delete_posts'] == true) ? true : false,
                            "delete_private_{$plural_base}" => ($value['capabilities']['delete_private_posts'] == true) ? true : false,
                            "delete_published_{$plural_base}" => ($value['capabilities']['delete_published_posts'] == true) ? true : false,
                            "delete_others_{$plural_base}" => ($value['capabilities']['delete_others_posts'] == true) ? true : false,
                        ]
                    );
                }              
                
            }
            
        }
        
        return $formatted_allowed_posttype_capabilities;
    }

    public function add_allowed_posttype_capabilities_to_role(string $mode = 'replace'): void
    {
        $formatted_allowed_posttype_capabilities = $this->format_allowed_posttype_capabilities('replace');

        if(is_array($formatted_allowed_posttype_capabilities) && count($formatted_allowed_posttype_capabilities))
        {
            $role = get_role($this->role_slug);

            if ($mode == 'replace') 
            {            
                foreach ($formatted_allowed_posttype_capabilities as $posttype => $capabilities) 
                {
                    if(is_array($capabilities) && count($capabilities))
                    {
                        foreach($capabilities as $capability => $grant)
                        {

                            if ($grant) 
                            {
                                $role->add_cap($capability);
                            }
                            else
                            {
                                $role->remove_cap($capability);
                            }
                            
                        }
                        
                    }                
                    
                }
                
            }
            
        }
        
    }
}