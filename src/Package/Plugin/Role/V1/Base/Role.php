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

    public function get_core_capabilities(): array
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
    
    /**
     * Get all capabilities as a flat unique sorted array
     */
    public function get_all_possible_capabilities(): array 
    {
        $grouped = $this->get_all_capabilities_grouped();
        $capabilities = [];
        
        foreach ($grouped['roles'] as $role_caps) 
        {
            $capabilities = array_merge($capabilities, $role_caps);
        }
        
        foreach ($grouped['post_types'] as $post_type_caps) 
        {
            $capabilities = array_merge($capabilities, $post_type_caps);
        }
        
        foreach ($grouped['taxonomies'] as $taxonomy_caps) 
        {
            $capabilities = array_merge($capabilities, $taxonomy_caps);
        }
        
        $capabilities = array_merge($capabilities, $grouped['core']);
        
        $capabilities = array_values(array_unique(array_filter($capabilities)));
        sort($capabilities);
        
        return $capabilities;
    }

    public function get_core_capabilities_by_group(): array
    {
        return [
            'plugin_management' => [
                'activate_plugins' => true,
                'delete_plugins' => true,
                'edit_plugins' => true,
                'install_plugins' => true,
                'update_plugins' => true
            ],
            'theme_management' => [
                'delete_themes' => true,
                'edit_themes' => true,
                'edit_theme_options' => true,
                'install_themes' => true,
                'switch_themes' => true,
                'update_themes' => true
            ],
            'user_management' => [
                'create_users' => true,
                'delete_users' => true,
                'edit_users' => true,
                'list_users' => true,
                'promote_users' => true,
                'remove_users' => true
            ],
            'system_operations' => [
                'update_core' => true,
                'edit_dashboard' => true,
                'edit_files' => true,
                'export' => true,
                'import' => true,
                'manage_options' => true
            ],
            'content_management' => [
                'manage_categories' => true,
                'moderate_comments' => true,
                'unfiltered_html' => true,
                'upload_files' => true
            ],
            'post_capabilities' => $this->get_core_posttype_capabilities(),
            'page_capabilities' => [
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
            ]
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