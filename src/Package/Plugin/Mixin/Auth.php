<?php
namespace Ababilithub\FlexWordpress\Package\Auth\V1\Mixin;

(defined('ABSPATH') && defined('WPINC')) || exit();

trait Auth 
{
    private static $post_type_cache = [];
    private static $menu_cache = [];
    private static $submenu_cache = null;
    private $auth_config = [];

    /**
     * Initialize the access control system
     *
     * @param array $config {
     *     @type string   $post_type       Required. The post type to control
     *     @type array    $hidden_actions  Row actions to hide (default: ['duplicate'])
     *     @type array    $hidden_views    Views to hide (default: ['all', 'publish', 'draft'])
     *     @type bool     $hide_add_new    Whether to hide "Add New" button (default: true)
     *     @type array    $allowed_caps    Capabilities that bypass restrictions (default: ['administrator'])
     *     @type array    $required_caps   Required capabilities to see elements (default: ['create_posts'])
     *     @type bool     $cache_enabled   Whether to enable menu/post type caching (default: true)
     * }
     */
    public function init_auth(array $config) 
    {
        $this->auth_config = wp_parse_args($config, [
            'hidden_actions' => ['duplicate'],
            'hidden_views'   => ['all', 'publish', 'draft'],
            'hide_add_new'  => true,
            'allowed_caps'  => ['administrator'],
            'required_caps' => ['create_posts'],
            'cache_enabled' => true,
        ]);

        add_filter('post_row_actions', [$this, 'filter_row_actions'], 10, 2);
        add_filter("views_edit-{$this->auth_config['post_type']}", [$this, 'filter_views']);
        
        if ($this->auth_config['hide_add_new']) {
            add_action('admin_head', [$this, 'hide_add_new_button']);
        }
    }

    // Post Type Capabilities
    public function get_caps($post_type): mixed 
    {
        if ($this->use_cache() && isset(self::$post_type_cache[$post_type])) 
        {
            return self::$post_type_cache[$post_type];
        }

        global $wp_post_types;
        
        if (!isset($wp_post_types[$post_type])) 
        {
            return false;
        }

        $caps = get_object_vars($wp_post_types[$post_type]->cap);
        
        if ($this->use_cache()) 
        {
            self::$post_type_cache[$post_type] = $caps;
        }
        
        return $caps;
    }

    // Menu Management
    public function get_menus($post_type): mixed 
    {
        $cache_key = 'menus_' . $post_type;
        
        if ($this->use_cache() && isset(self::$menu_cache[$cache_key])) 
        {
            return self::$menu_cache[$cache_key];
        }

        $this->load_menus();
        $menus = [];
        $target = 'post_type=' . $post_type;

        foreach (self::$menu_cache['main'] as $item) 
        {
            if (strpos($item[2], $target) === false) continue;

            $slug = $item[2];
            $menus[] = $this->format_menu_item($item, $slug);

            if (isset(self::$submenu_cache[$slug])) 
            {
                foreach (self::$submenu_cache[$slug] as $subitem) 
                {
                    $menus[] = $this->format_menu_item($subitem, $subitem[2]);
                }
            }
        }

        if ($this->use_cache()) 
        {
            self::$menu_cache[$cache_key] = $menus;
        }
        return $menus;
    }

    public function get_menu_caps($post_type): mixed 
    {
        $cache_key = 'caps_' . $post_type;
        
        if ($this->use_cache() && isset(self::$menu_cache[$cache_key])) 
        {
            return self::$menu_cache[$cache_key];
        }

        $this->load_menus();
        $caps = [];
        $target = 'post_type=' . $post_type;

        foreach (self::$menu_cache['main'] as $item) 
        {
            if (strpos($item[2], $target) === false) continue;

            $slug = $item[2];
            $caps[$this->encode_slug($slug)] = $item[1];

            if (isset(self::$submenu_cache[$slug])) 
            {
                foreach (self::$submenu_cache[$slug] as $subitem) 
                {
                    $caps[$this->encode_slug($subitem[2])] = $subitem[1];
                }
            }
        }

        if ($this->use_cache()) 
        {
            self::$menu_cache[$cache_key] = $caps;
        }
        return $caps;
    }

    // Access Control Filters
    public function filter_row_actions($actions, $post): mixed 
    {
        if ($post->post_type === $this->auth_config['post_type'] && $this->needs_restriction()) 
        {
            foreach ($this->auth_config['hidden_actions'] as $action) 
            {
                unset($actions[$action]);
            }
        }
        return $actions;
    }

    public function filter_views($views): mixed 
    {
        if ($this->needs_restriction()) 
        {
            foreach ($this->auth_config['hidden_views'] as $view) 
            {
                unset($views[$view]);
            }
        }
        return $views;
    }

    public function hide_add_new_button(): void 
    {
        global $current_screen;
        
        if ($current_screen->post_type === $this->auth_config['post_type'] && $this->needs_restriction()) 
        {
            echo '<style>.page-title-action { display: none !important; }</style>';
        }
    }

    // Core Checks
    protected function needs_restriction(): bool 
    {
        foreach ($this->auth_config['allowed_caps'] as $cap) 
        {
            if (current_user_can($cap)) return false;
        }

        foreach ($this->auth_config['required_caps'] as $cap) 
        {
            if (!current_user_can($cap)) return true;
        }

        return false;
    }

    // Helpers
    private function use_cache(): mixed 
    {
        return $this->auth_config['cache_enabled'];
    }

    private function load_menus(): void 
    {
        if (!isset(self::$menu_cache['main'])) 
        {
            global $menu, $submenu;
            self::$menu_cache['main'] = $menu;
            self::$submenu_cache = $submenu;
        }
    }

    private function format_menu_item($item, $slug) 
    {
        return [
            'title'        => $item[0],
            'capability'   => $item[1],
            'slug'         => $slug,
            'encoded_slug' => $this->encode_slug($slug)
        ];
    }

    private function encode_slug($slug): string 
    {
        return base64_encode($slug);
    }
}