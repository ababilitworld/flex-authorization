<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Permission\PostMeta\PostMetaBoxContent\Concrete\Section\General;

use Ababilithub\{
    FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Permission\POSTTYPE as PermissionPosttype,
    FlexWordpress\Package\PostMeta\V1\Mixin\PostMeta as PostMetaMixin,
    FlexWordpress\Package\PostMetaBoxContent\V1\Base\PostMetaBoxContent as BasePostMetaBoxContent,
    FlexPhp\Package\Form\Field\V1\Factory\Field as FieldFactory,
    FlexPhp\Package\Form\Field\V1\Concrete\Text\Field as TextField,
    FlexPhp\Package\Form\Field\V1\Concrete\File\Document\Field as DocField,
    FlexPhp\Package\Form\Field\V1\Concrete\File\Image\Field as ImageField
};

use const Ababilithub\{
    FlexMasterPro\PLUGIN_PRE_HYPH,
    FlexMasterPro\PLUGIN_PRE_UNDS,
};

class PostMetaBoxContent extends BasePostMetaBoxContent
{
    use PostMetaMixin;
    public function init(array $data = []) : static
    {
        $this->posttype = PermissionPosttype::POSTTYPE;
        $this->post_id = get_the_ID();
        $this->tab_item_id = $this->posttype.'-'.'section-general';
        $this->tab_item_label = esc_html__('General Information');
        $this->tab_item_icon = 'fas fa-edit';
        $this->tab_item_status = 'active';

        $this->init_service();
        $this->init_hook();

        return $this;
    }

    public function init_service():void
    {

    }

    public function init_hook() : void
    {
        add_action(PLUGIN_PRE_UNDS.'_'.$this->posttype.'_'.'meta_box_tab_item',[$this,'tab_item']);
        add_action(PLUGIN_PRE_UNDS.'_'.$this->posttype.'_'.'meta_box_tab_content', [$this,'tab_content']);
        //add_action('save_post', [$this, 'save'], 10, 3);
    }

    public function render() : void
    {
        $meta_values = $this->get_meta_values(get_the_ID());
        //echo "<pre>";print_r($meta_values);echo "</pre>";exit;
        ?>
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Company Contact Details</h2>
                </div>
                <div class="panel-body">
                    <div class="panel-row  two-columns">
                        <?php
                            $mobileNumberField = FieldFactory::get(TextField::class);
                            $mobileNumberField->init([
                                'name' => 'mobile-number',
                                'id' => 'mobile-number',
                                'label' => 'Mobile Number',
                                'class' => 'custom-file-input',
                                'required' => true,
                                'help_text' => 'Enter full mobile number of the company',
                                'value' => $meta_values['mobile_number'],
                                'data' => [
                                    'custom' => 'value'
                                ],
                                'attributes' => [
                                    'data-preview-size' => '150'
                                ]
                            ])->render();
                        ?>
                        <?php
                            $emailAddressField = FieldFactory::get(TextField::class);
                            $emailAddressField->init([
                                'name' => 'email-address',
                                'id' => 'email-address',
                                'label' => 'Email Address',
                                'class' => 'custom-file-input',
                                'required' => true,
                                'help_text' => 'Enter E-mail address of the company',
                                'value' => $meta_values['email_address'],
                                'data' => [
                                    'custom' => 'value'
                                ],
                                'attributes' => [
                                    'data-preview-size' => '150'
                                ]
                            ])->render();
                        ?>
                    </div>
                    <div class="panel-row">                        
                        <?php
                            $physicalAddressField = FieldFactory::get(TextField::class);
                            $physicalAddressField->init([
                                'name' => 'physical-address',
                                'id' => 'physical-address',
                                'label' => 'Physical Address',
                                'class' => 'custom-file-input',
                                'required' => true,
                                'help_text' => 'Enter physical address of the company',
                                'value' => $meta_values['physical_address'],
                                'data' => [
                                    'custom' => 'value'
                                ],
                                'attributes' => [
                                    'data-preview-size' => '150'
                                ]
                            ])->render();
                        ?>
                        
                    </div>
                    <div class="panel-row">
                        <?php
                            $googleMapLocationField = FieldFactory::get(TextField::class);
                            $googleMapLocationField->init([
                                'name' => 'google-map-location',
                                'id' => 'google-map-location',
                                'label' => 'Google Map Location',
                                'class' => 'custom-file-input',
                                'required' => false,
                                'help_text' => 'Enter google map Location of the company',
                                'value' => $meta_values['google_map_location'],
                                'data' => [
                                    'custom' => 'value'
                                ],
                                'attributes' => [
                                    'data-preview-size' => '150'
                                ]
                            ])->render();
                        ?>
                    </div>
                </div>
            </div>
        <?php

    }

    public function get_meta_values($post_id): array 
    {
        return [
            'mobile_number' => get_post_meta($post_id, 'mobile-number', true) ?: '',
            'email_address' => get_post_meta($post_id, 'email-address', true) ?: '',
            'physical_address' => get_post_meta($post_id, 'physical-address', true) ?: '',
            'google_map_location' => get_post_meta($post_id, 'google-map-location', true) ?: '',
            
        ];
    }

    public function save($post_id, $post, $update): void 
    {
        if (!$this->is_valid_save($post_id, $post)) 
        {
            return;
        }

        // Save text fields
        $this->save_text_field($post_id, 'mobile-number', sanitize_text_field($_POST['mobile-number'] ?? ''));
        $this->save_text_field($post_id, 'email-address', sanitize_text_field($_POST['email-address'] ?? ''));
        $this->save_text_field($post_id, 'physical-address', sanitize_text_field($_POST['physical-address'] ?? ''));
        $this->save_text_field($post_id, 'google-map-location', sanitize_text_field($_POST['google-map-location'] ?? ''));
   
    }
}