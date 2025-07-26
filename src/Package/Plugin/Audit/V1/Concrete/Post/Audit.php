<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Audit\V1\Concrete\Post;

use Ababilithub\{
    FlexAuthorization\Package\Plugin\Audit\V1\Base\Audit as BaseAudit
};

use const Ababilithub\{
    FlexAuthorization\PLUGIN_NAME,
    FlexAuthorization\PLUGIN_DIR,
    FlexAuthorization\PLUGIN_URL,
    FlexAuthorization\PLUGIN_FILE,
    FlexAuthorization\PLUGIN_PRE_UNDS,
    FlexAuthorization\PLUGIN_PRE_HYPH,
    FlexAuthorization\PLUGIN_VERSION
};

class Audit extends BaseAudit
{
    public function init(array $data = null): static
    {
        $this->object_type = 'post';
        $this->object_id = absint($_GET['object_id'] ?? 0);
        $this->action_id = sanitize_text_field($_GET['action_id'] ?? '');

        //echo "<pre>";print_r($this);echo "</pre>";exit;

        $this->init_service();
        $this->init_hook();

        return $this;
    }

    public function init_service()
    {

    }

    public function init_hook()
    {  
        //add_filter(PLUGIN_PRE_UNDS.'_admin_menu', [$this, 'add_menu_items']);
    }

    public function render(): void
    {
        $this->init();
        $this->handleAuditRequest();
    }

    /**
     * Display all post meta for a specific post in a table
     * 
     */
    public function applyAuditAction(): void 
    {
        
        if($this->action_id == 'view_details')
        {
            $this->view_details();
        }

        if($this->action_id == 'view_meta')
        {
            $this->view_all_meta();
        }
        
    }

    public function view_details(): void 
    {
        // Validate and get post object
        $post = get_post($this->object_id);
        if (!$post) {
            $this->render_notice('Post not found.', 'error');
            return;
        }

        // Get all post meta
        $all_meta = get_post_meta($this->object_id);
        
        // Begin output
        $this->render_header($post);
        
        if (empty($all_meta)) {
            $this->render_notice('No meta data found for this post.');
            return;
        }

        ?>
        <h3><?php printf(esc_html__('Post details of #%d', 'flex-authorization'), 
                $post->ID, 
                esc_html($post->post_title)); ?>
        </h3>
        <?php

        $this->render_post_details($post);

        ?>
         <h3><?php printf(esc_html__('Post Metas of #%s', 'flex-authorization'), 
                
                esc_html($post->post_title)); ?>
        </h3>
        <?php

        $this->render_meta_table($all_meta);
    }

    /**
     * Render the page header with back link
     */
    protected function render_header(\WP_Post $post): void
    {
        ?>
        <div class="wrap">
            <h1><?php printf(esc_html__('Post Data for #%d: %s', 'flex-authorization'), 
                $post->ID, 
                esc_html($post->post_title)); ?>
            </h1>
            <p>
                <a href="<?php echo esc_url(get_edit_post_link($post->ID)); ?>" class="button">
                    &larr; <?php esc_html_e('Back to post editor', 'flex-authorization'); ?>
                </a>
            </p>
        <?php
    }

        /**
     * Render meta data in a table
     */
    protected function render_post_details($post): void
    {
        ?>
        <?php echo "<pre>";print_r($post);echo "</pre>"; ?>
        <?php
    }

    /**
     * Render meta data in a table
     */
    protected function render_meta_table(array $all_meta): void
    {
        ?>
        
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th width="25%"><?php esc_html_e('Meta Key', 'flex-authorization'); ?></th>
                    <th width="75%"><?php esc_html_e('Meta Value', 'flex-authorization'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_meta as $meta_key => $meta_values): ?>
                    <tr>
                        <td><strong><?php echo esc_html($meta_key); ?></strong></td>
                        <td>
                            <?php foreach ($meta_values as $value): ?>
                                <?php $this->render_meta_value($value); ?>
                                <hr class="meta-value-separator">
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        $this->render_styles();
    }

    /**
     * Render individual meta value
     */
    protected function render_meta_value($value): void
    {
        $unserialized = maybe_unserialize($value);
        
        if (is_array($unserialized) || is_object($unserialized)) {
            echo '<pre>'. esc_html(print_r($unserialized, true)) .'</pre>';
        } else {
            echo esc_html($value);
        }
    }

    /**
     * Render admin notice
     */
    protected function render_notice(string $message, string $type = 'info'): void
    {
        ?>
        <div class="notice notice-<?php echo esc_attr($type); ?>">
            <p><?php echo esc_html($message); ?></p>
        </div>
        <?php
    }

    /**
     * Add custom styles
     */
    protected function render_styles(): void
    {
        ?>
        <style>
            .wrap { 
                margin: 20px; 
            }
            table.widefat { 
                border-collapse: collapse; 
                margin-top: 20px; 
            }
            table.widefat th, 
            table.widefat td { 
                padding: 10px; 
                border: 1px solid #e5e5e5; 
                vertical-align: top; 
            }
            table.widefat th { 
                background-color: #f7f7f7; 
                font-weight: 600; 
            }
            table.widefat pre { 
                margin: 0; 
                white-space: pre-wrap; 
                max-width: 100%; 
                overflow-x: auto;
                background: #f5f5f5;
                padding: 5px;
                border-radius: 3px;
            }
            .notice { 
                margin: 20px 0 !important; 
            }
            hr.meta-value-separator {
                margin: 5px 0; 
                border: 0; 
                border-top: 1px dashed #ccc;
            }
        </style>
        <?php
    }

    public function view_all_meta()
    {
        
    }
    
}