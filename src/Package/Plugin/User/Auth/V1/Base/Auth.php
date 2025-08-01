<?php 
namespace Ababilithub\FlexAuthorization\Package\Plugin\User\Auth\V1\Base;

(defined( 'ABSPATH' ) && defined( 'WPINC' )) || exit();

use Ababilithub\{
    FlexAuthorization\Package\Plugin\User\Auth\V1\Contract\Auth as AuthContract,
    FlexAuthorization\Package\Plugin\User\Item\V1\Contract\Item as ItemContract
};

abstract class Auth implements AuthContract 
{
    protected $config;
    protected $item_handler;
    protected $user_meta_key = 'user_assigned_items';

    public function __construct(array $config, ItemContract $item_handler) 
    {
        $this->config = wp_parse_args($config, [
            'capability' => 'administrator',
            'field_title' => 'Assigned Items',
            'field_description' => '',
            'multiple_select' => true,
            'include_authored' => true
        ]);

        $this->item_handler = $item_handler;
    }

    public function init_hooks(): void 
    {
        add_action('user_new_form', [$this, 'add_user_field']);
        add_action('edit_user_profile', [$this, 'add_user_field']);
        add_action('show_user_profile', [$this, 'add_user_field']);
        
        add_action('user_register', [$this, 'save_user_items']);
        add_action('personal_options_update', [$this, 'save_user_items']);
        add_action('edit_user_profile_update', [$this, 'save_user_items']);
    }

    public function add_user_field($user): void 
    {
        if (!current_user_can($this->config['capability'])) 
        {
            return;
        }

        $selected_items = $this->item_handler->get_user_items($user->ID);
        $all_items = $this->item_handler->get_all_items();
        
        $multiple = $this->config['multiple_select'] ? 'multiple="multiple"' : '';
        $name_attr = $this->config['multiple_select'] ? 'user_items[]' : 'user_items';
        
        ?>
        <h2><?php echo esc_html($this->config['field_title']); ?></h2>
        <table class="form-table" role="presentation">
            <tbody>
                <tr class="user-role-wrap">
                    <th><label for="user_items"><?php echo esc_html($this->config['field_title']); ?></label></th>
                    <td>
                        <?php wp_nonce_field('save_user_items', 'save_user_items_nonce'); ?>
                        <select name="<?php echo esc_attr($name_attr); ?>" id="user_items" <?php echo $multiple; ?>>
                            <?php if ($this->config['multiple_select']) : ?>
                                <option value="">— Select items —</option>
                            <?php else: ?>
                                <option value="">— No item selected —</option>
                            <?php endif; ?>
                            
                            <?php foreach ($all_items as $item) : ?>
                                <?php $selected = in_array($item['value'], $selected_items) ? 'selected' : ''; ?>
                                <option value="<?php echo esc_attr($item['value']); ?>" <?php echo $selected; ?>>
                                    <?php echo esc_html($item['label']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <?php if (!empty($this->config['field_description'])) : ?>
                            <p class="description"><?php echo esc_html($this->config['field_description']); ?></p>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
    }

    public function save_user_items(int $user_id): bool 
    {
        if (!current_user_can($this->config['capability']) || 
            !check_admin_referer('save_user_items', 'save_user_items_nonce')) {
            return false;
        }

        $items = isset($_POST['user_items']) ? (array) $_POST['user_items'] : [];
        $items = array_map('sanitize_text_field', $items);
        
        update_user_meta($user_id, $this->get_meta_key(), $items);
        return true;
    }

    protected function get_meta_key(): string 
    {
        return $this->user_meta_key . '_' . $this->item_handler->get_item_type();
    }
}