<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Audit\V1\Concrete\Term;

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
        $this->object_type = 'term';
        $this->object_id = absint($_GET['object_id'] ?? 0);
        $this->action_id = sanitize_text_field($_GET['action_id'] ?? '');

        $this->init_service();
        $this->init_hook();

        return $this;
    }

    public function init_service()
    {
        // Initialize any term-specific services
    }

    public function init_hook()
    {  
        // Add term-specific hooks if needed
    }

    public function render(): void
    {
        $this->init();
        $this->handleAuditRequest();
    }

    public function applyAuditAction(): void 
    {
        if ($this->action_id == 'view_details') {
            $this->view_details();
        }

        if ($this->action_id == 'view_meta') {
            $this->view_all_meta();
        }
    }

    public function view_details(): void 
    {
        // Validate and get term object
        $term = get_term($this->object_id);
        if (!$term || is_wp_error($term)) {
            $this->render_notice('Term not found.', 'error');
            return;
        }

        // Get all term meta
        $all_meta = get_term_meta($this->object_id);
        
        // Begin output
        $this->render_header($term);
        
        // Term details section
        ?>
        <h3><?php printf(esc_html__('Term details of #%d', 'flex-authorization'), 
                $term->term_id); ?>
        </h3>
        <?php
        $this->render_term_details($term);

        // Term meta section
        if (empty($all_meta)) {
            $this->render_notice('No meta data found for this term.');
            return;
        }

        ?>
        <h3><?php printf(esc_html__('Term Metas of "%s"', 'flex-authorization'), 
                esc_html($term->name)); ?>
        </h3>
        <?php
        $this->render_meta_table($all_meta);
    }

    protected function render_header(\WP_Term $term): void
    {
        $edit_link = get_edit_term_link($term->term_id, $term->taxonomy);
        ?>
        <div class="wrap">
            <h1><?php printf(esc_html__('Term Data for #%d: %s', 'flex-authorization'), 
                $term->term_id, 
                esc_html($term->name)); ?>
            </h1>
            <?php if ($edit_link): ?>
                <p>
                    <a href="<?php echo esc_url($edit_link); ?>" class="button">
                        &larr; <?php esc_html_e('Back to term editor', 'flex-authorization'); ?>
                    </a>
                </p>
            <?php endif; ?>
        <?php
    }

    protected function render_term_details(\WP_Term $term): void
    {
        ?>
        <table class="widefat fixed striped">
            <tbody>
                <tr>
                    <th width="25%"><?php esc_html_e('Term ID', 'flex-authorization'); ?></th>
                    <td><?php echo esc_html($term->term_id); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Name', 'flex-authorization'); ?></th>
                    <td><?php echo esc_html($term->name); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Slug', 'flex-authorization'); ?></th>
                    <td><?php echo esc_html($term->slug); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Taxonomy', 'flex-authorization'); ?></th>
                    <td><?php echo esc_html($term->taxonomy); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Description', 'flex-authorization'); ?></th>
                    <td><?php echo esc_html($term->description); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Count', 'flex-authorization'); ?></th>
                    <td><?php echo esc_html($term->count); ?></td>
                </tr>
            </tbody>
        </table>
        <?php
    }

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

    // The following methods remain the same as in Post Audit
    protected function render_meta_value($value): void
    {
        $unserialized = maybe_unserialize($value);
        
        if (is_array($unserialized) || is_object($unserialized)) {
            echo '<pre>'. esc_html(print_r($unserialized, true)) .'</pre>';
        } else {
            echo esc_html($value);
        }
    }

    protected function render_notice(string $message, string $type = 'info'): void
    {
        ?>
        <div class="notice notice-<?php echo esc_attr($type); ?>">
            <p><?php echo esc_html($message); ?></p>
        </div>
        <?php
    }

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
        // Implement if you need a meta-only view
    }
}