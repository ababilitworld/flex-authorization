<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Permission;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexPhp\Package\Mixin\V1\Standard\Mixin as StandardMixin,
    FlexWordpress\Package\Posttype\V1\Mixin\Posttype as WpPosttypeMixin,
    FlexWordpress\Package\Posttype\V1\Base\Posttype as BasePosttype,
    FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Permission\Presentation\Template\Single\Template as PosttypeTemplate,
    FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Permission\PostMeta\PostMetaBox\Manager\PostMetaBox as PermissionPostMetaBoxManager,
    FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Permission\PostMeta\PostMetaBoxContent\Manager\PostMetaBoxContent as PermissionPostMetaBoxContentManager,
    
};

use const Ababilithub\{
    FlexMasterPro\PLUGIN_PRE_UNDS,
    FlexMasterPro\PLUGIN_DIR,
};

class Posttype extends BasePosttype 
{ 
    use WpPosttypeMixin;

    public const POSTTYPE = 'fpermisn';

    private $template_service;
    
    public function init() : void
    {
        $this->posttype = self::POSTTYPE;
        $this->slug = self::POSTTYPE;

        $this->set_labels([
            'name' => esc_html__('Permissions', 'flex-authorization'),
            'singular_name' => esc_html__('Permission', 'flex-authorization'),
            'menu_name' => esc_html__('Permissions', 'flex-authorization'),
            'name_admin_bar' => esc_html__('Permissions', 'flex-authorization'),
            'archives' => esc_html__('Permission List', 'flex-authorization'),
            'attributes' => esc_html__('Permission List', 'flex-authorization'),
            'parent_item_colon' => esc_html__('Permission Item : ', 'flex-authorization'),
            'all_items' => esc_html__('All Permission', 'flex-authorization'),
            'add_new_item' => esc_html__('Add new Permission', 'flex-authorization'),
            'add_new' => esc_html__('Add new Permission', 'flex-authorization'),
            'new_item' => esc_html__('New Permission', 'flex-authorization'),
            'edit_item' => esc_html__('Edit Permission', 'flex-authorization'),
            'update_item' => esc_html__('Update Permission', 'flex-authorization'),
            'view_item' => esc_html__('View Permission', 'flex-authorization'),
            'view_items' => esc_html__('View Permissions', 'flex-authorization'),
            'search_items' => esc_html__('Search Permissions', 'flex-authorization'),
            'not_found' => esc_html__('Permission Not found', 'flex-authorization'),
            'not_found_in_trash' => esc_html__('Permission Not found in Trash', 'flex-authorization'),
            'featured_image' => esc_html__('Permission Feature Image', 'flex-authorization'),
            'set_featured_image' => esc_html__('Set Permission Feature Image', 'flex-authorization'),
            'remove_featured_image' => esc_html__('Remove Feature Image', 'flex-authorization'),
            'use_featured_image' => esc_html__('Use as Permission featured image', 'flex-authorization'),
            'insert_into_item' => esc_html__('Insert into Permission', 'flex-authorization'),
            'uploaded_to_this_item' => esc_html__('Uploaded to this ', 'flex-authorization'),
            'items_list' => esc_html__('Permission list', 'flex-authorization'),
            'items_list_navigation' => esc_html__('Permission list navigation', 'flex-authorization'),
            'filter_items_list' => esc_html__('Filter Permission List', 'flex-authorization')
        ]);

        $this->set_posttype_supports(
            array('title', 'thumbnail', 'editor')
        );

        $this->set_taxonomies([]);

        $this->set_args([
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'show_in_menu' => false, // Don't show in menu by default
            'labels' => $this->labels,
            'menu_icon' => "dashicons-admin-post",
            'rewrite' => array('slug' => $this->slug,'with_front' => false),
            'has_archive' => true,        // If you want archive pages
            'supports' => $this->posttype_supports,
            'taxonomies' => $this->taxonomies,
        ]);

        $this->init_service();
        $this->init_hook();

    }

    public function init_service(): void
    {
       $this->template_service = new PosttypeTemplate();
    }

    public function init_hook(): void
    {
        add_action('after_setup_theme', [$this, 'init_theme_supports'],0);

        add_action('add_meta_boxes', function () {
            (new PermissionPostMetaBoxManager())->boot();
        });

        add_action('add_meta_boxes', function () {
            (new PermissionPostMetaBoxContentManager())->boot();
        });

        add_action('save_post', function ($post_id, $post, $update) {
            (new PermissionPostMetaBoxContentManager())->save_post($post_id, $post, $update);
        }, 10, 3);

        add_filter('the_content', [$this, 'single_post']);
        
        add_filter('post_row_actions', [$this, 'add_action_view_details'], 10, 2);
        add_filter('page_row_actions', [$this, 'add_action_view_details'], 10, 2);


    }

    public function init_theme_supports()
    {
        add_theme_support('post-thumbnails', [$this->posttype]);
        add_theme_support('editor-color-palette', [
            [
                'name'  => 'Primary Blue',
                'slug'  => 'primary-blue',
                'color' => '#3366FF',
            ],
        ]);
        add_theme_support('align-wide');
        add_theme_support('responsive-embeds');
    }

    public function single_post($content)
    {
        // Only modify content on single post pages of specific post types
        if (!is_singular() || !in_the_loop() || !is_main_query()) 
        {
            return $content;
        }

        global $post;
        
        if ($post->post_type !== $this->posttype) 
        {
            return $content;
        }

        // Prevent infinite recursion
        remove_filter('the_content', [$this, 'single_post']);
        
        // Get template content
        $template_content = $this->template_service::single_post($post);
        
        // Re-add our filter
        add_filter('the_content', [$this, 'single_post']);
        
        // Combine with original content
        return $template_content;
    }

}