<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Role\PostMeta\PostMetaBoxContent\Concrete\Section\Image;

use Ababilithub\{
    FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Role\POSTTYPE as RolePosttype,
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
        $this->posttype = RolePosttype::POSTTYPE;
        $this->post_id = get_the_ID();
        $this->tab_item_id = $this->posttype.'-'.'section-image';
        $this->tab_item_label = esc_html__('Images');
        $this->tab_item_icon = 'fas fa-edit';
        $this->tab_item_status = '';

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
                    <h2 class="panel-title">Company Images</h2>
                </div>
                <div class="panel-body">
                    <div class="panel-row two-columns">
                        <?php
                            unset($imageField);
                            $imageField = FieldFactory::get(ImageField::class);
                            $imageField->init([
                                'name' => 'post-logo',
                                'id' => 'post-logo',
                                'label' => 'Company Logo',
                                'class' => 'custom-file-input',
                                'required' => true,
                                'multiple' => false,
                                'allowed_types' => ['.jpg', '.jpeg', '.png', '.gif', '.webp'],
                                'max_size' => 2097152, // 2MB
                                'enable_media_library' => true,
                                'upload_action_text' => 'Select Image',
                                'help_text' => 'Only jpg, png, gif, webp files are allowed (max size: 2MB)',
                                'preview_items' => $meta_values['post_logo'],
                                'data' => [
                                    'custom' => 'value'
                                ],
                                'attributes' => [
                                    'data-preview-size' => '150'
                                ]
                            ])->render();
                        ?>
                        <?php
                            unset($imageField);
                            $imageField = FieldFactory::get(ImageField::class);
                            $imageField->init([
                                'name' => 'post-thumbnail-image',
                                'id' => 'post-thumbnail-image',
                                'label' => 'Company Thumbnail',
                                'class' => 'custom-file-input',
                                'required' => true,
                                'multiple' => false,
                                'allowed_types' => ['.jpg', '.jpeg', '.png', '.gif', '.webp'],
                                'max_size' => 2097152, // 2MB
                                'enable_media_library' => true,
                                'upload_action_text' => 'Select Image',
                                'help_text' => 'Only jpg, png, gif, webp files are allowed (max size: 2MB)',
                                'preview_items' => $meta_values['post_thumbnail_image'],
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
                            unset($imageField);
                            $imageField = FieldFactory::get(ImageField::class);
                            $imageField->init([
                                'name' => 'post-images',
                                'id' => 'post-images',
                                'label' => 'Company Images',
                                'class' => 'custom-file-input',
                                'required' => true,
                                'multiple' => true,
                                'allowed_types' => ['.jpg', '.jpeg', '.png', '.gif', '.webp'],
                                'max_size' => 2097152, // 2MB
                                'enable_media_library' => true,
                                'upload_action_text' => 'Select Images',
                                'help_text' => 'Only jpg, png, gif, webp files are allowed (max size: 2MB)',
                                'preview_items' => $meta_values['post_images'],
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
            'post_logo' => get_post_meta($post_id, 'post-logo', true) ?: 0, // Default to 0 (no image)
            'post_thumbnail_image' => get_post_meta($post_id, 'post-thumbnail-image', true) ?: 0, // Default to 0 (no image)
            'post_images' => get_post_meta($post_id, 'post-images', true) ?: [],
            
        ];
    }

    public function save($post_id, $post, $update): void 
    {
        if (!$this->is_valid_save($post_id, $post)) 
        {
            return;
        }

        // Save media (IDs)
        $this->save_single_image($post_id, 'post-logo', absint($_POST['post-logo'] ?? 0));
        $this->save_thumbnail_image($post_id, 'post-thumbnail-image', absint($_POST['post-thumbnail-image'] ?? 0));
        $this->save_multiple_images($post_id, 'post-images', array_map('absint', $_POST['post-images'] ?? []));
        
    }
}