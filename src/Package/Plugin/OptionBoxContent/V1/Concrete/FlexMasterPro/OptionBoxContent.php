<?php
namespace Ababilithub\FlexWordpress\Package\OptionBoxContent\V1\Concrete\FlexAuthorization;

use Ababilithub\{
    FlexWordpress\Package\Option\V1\Mixin\Option as OptionMixin,
    FlexWordpress\Package\OptionBox\V1\Concrete\VerticalTabBox\OptionBox as OptionBox,
    FlexWordpress\Package\OptionBoxContent\V1\Base\OptionBoxContent as BaseOptionBoxContent,
    FlexPhp\Package\Form\Field\V1\Factory\Field as FieldFactory,
    FlexPhp\Package\Form\Field\V1\Concrete\Text\Field as TextField,
    FlexPhp\Package\Form\Field\V1\Concrete\File\Document\Field as DocField,
    FlexPhp\Package\Form\Field\V1\Concrete\File\Image\Field as ImageField
};

use const Ababilithub\{
    FlexAuthorization\PLUGIN_PRE_HYPH,
    FlexAuthorization\PLUGIN_PRE_UNDS,
};

class OptionBoxContent extends BaseOptionBoxContent
{
    use OptionMixin;
    public function init(array $data =[]) : static
    {
        $this->tab_id = OptionBox::id;
        $this->tab_item_id = $this->tab_id.'-'.'general-settings';
        $this->tab_item_label = esc_html__('General Settings');
        $this->tab_item_icon = 'fas fa-home';
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
        add_action($this->unique_id.'_'.'meta_box_tab_item',[$this,'tab_item']);
        add_action($this->unique_id.'_'.'meta_box_tab_content', [$this,'tab_content']);
        add_action('admin_init', [$this, 'save']);
    }

    public function render() : void
    {
        $option_values = $this->getOptionBoxContentValues();
        ?>
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Deed Details</h2>
                </div>
                <div class="panel-body">
                    <div class="panel-row">
                        <?php
                            $deedDateField = FieldFactory::get(TextField::class);
                            $deedDateField->init([
                                'name' => 'deed-date',
                                'id' => 'deed-date',
                                'label' => 'Deed Date',
                                'class' => 'custom-file-input',
                                'required' => true,
                                'help_text' => 'Enter Deed Date used in the Deed',
                                'value' => $option_values['deed_date'],
                                'data' => [
                                    'custom' => 'value'
                                ],
                                'attributes' => [
                                    'data-preview-size' => '150'
                                ]
                            ])->render();
                        ?>
                    </div>
                    <div class="panel-row two-columns">
                        <?php
                            $deedNumberField = FieldFactory::get(TextField::class);
                            $deedNumberField->init([
                                'name' => 'deed-number',
                                'id' => 'deed-number',
                                'label' => 'Deed Number',
                                'class' => 'custom-file-input',
                                'required' => true,
                                'help_text' => 'Enter Deed number of the deed',
                                'value' => $option_values['deed_number'],
                                'data' => [
                                    'custom' => 'value'
                                ],
                                'attributes' => [
                                    'data-preview-size' => '150'
                                ]
                            ])->render();
                        ?>
                        <?php
                            $plotNumberField = FieldFactory::get(TextField::class);
                            $plotNumberField->init([
                                'name' => 'plot-number',
                                'id' => 'plot-number',
                                'label' => 'Plot Number',
                                'class' => 'custom-file-input',
                                'required' => true,
                                'help_text' => 'Enter Plot number according to the Respective Survey',
                                'value' => $option_values['plot_number'],
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
                            $landQuantityField = FieldFactory::get(TextField::class);
                            $landQuantityField->init([
                                'name' => 'land-quantity',
                                'id' => 'land-quantity',
                                'label' => 'Land Quantity (Decimal)',
                                'class' => 'custom-file-input',
                                'required' => true,
                                'help_text' => 'Enter Land Quantity in decimal used in the Deed',
                                'value' => $option_values['land_quantity'],
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
            
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Deed Images</h2>
                </div>
                <div class="panel-body">
                    <div class="panel-row">
                        <?php
                            $imageField = FieldFactory::get(ImageField::class);
                            $imageField->init([
                                'name' => 'deed-thumbnail-image',
                                'id' => 'deed-thumbnail-image',
                                'label' => 'Deed Thumbnail',
                                'class' => 'custom-file-input',
                                'required' => true,
                                'multiple' => false,
                                'allowed_types' => ['.jpg', '.jpeg', '.png', '.gif', '.webp'],
                                'max_size' => 2097152, // 2MB
                                'enable_media_library' => true,
                                'upload_action_text' => 'Select Images',
                                'help_text' => 'Only jpg, png, gif, webp files are allowed',
                                'preview_items' => $$option_values['deed_thumbnail_image'],
                                'data' => [
                                    'custom' => 'value'
                                ],
                                'attributes' => [
                                    'data-preview-size' => '150'
                                ]
                            ])->render();
                        ?>
                        <?php
                            $imageField = FieldFactory::get(ImageField::class);
                            $imageField->init([
                                'name' => 'deed-images',
                                'id' => 'deed-images',
                                'label' => 'Deed Images',
                                'class' => 'custom-file-input',
                                'required' => true,
                                'multiple' => true,
                                'allowed_types' => ['.jpg', '.jpeg', '.png', '.gif', '.webp'],
                                'max_size' => 2097152, // 2MB
                                'enable_media_library' => true,
                                'upload_action_text' => 'Select Images',
                                'help_text' => 'Only jpg, png, gif, webp files are allowed',
                                'preview_items' => $option_values['images'],
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

            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Deed Documents</h2>
                </div>
                <div class="panel-body">
                    <div class="panel-row">
                        <?php
                            $deedPdfField = FieldFactory::get(DocField::class);
                            $deedPdfField->init([
                                'name' => 'deed-docs',
                                'id' => 'deed-docs',
                                'label' => 'Deed Documents',
                                'class' => 'custom-file-input',
                                'required' => true,
                                'multiple' => true,
                                'allowed_types' => ['.pdf', '.doc', '.docx', '.xls', '.xlsx'],
                                'upload_action_text' => 'Select Documents',
                                'help_text' => 'Only PDF, Word, and Excel files are allowed',
                                'max_size' => 5242880, // 5MB
                                'enable_media_library' => true,
                                'preview_items' => $option_values['docs'],
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

    /**
     * Get all option values in a structured array
     */
    private function getOptionBoxContentValues(): array
    {
        return [
            'deed_date' => get_option('deed_date') ?: '',
            'deed_number' => get_option('deed_number') ?: '',
            'plot_number' => get_option('plot_number') ?: '',
            'land_quantity' => get_option('land_quantity') ?: '',
            'deed_thumbnail_image' => get_option('deed_thumbnail_image') ?: '',
            'images' => get_option('deed_images') ?: [],
            'docs' => get_option('deed_attachments') ?: []
        ];
    }

    /**
     * Save all options from the submitted form data
     */
    public function save(array $data = []): void 
    {
        if (!$this->isValidOptionBoxContentSave()) 
        {
            return;
        }

        // Save text fields
        $this->saveTextOptionBoxContent('deed_date', sanitize_text_field($_POST['deed_date'] ?? ''));
        $this->saveTextOptionBoxContent('deed_number', sanitize_text_field($_POST['deed_number'] ?? ''));
        $this->saveTextOptionBoxContent('plot_number', sanitize_text_field($_POST['plot_number'] ?? ''));
        $this->saveTextOptionBoxContent('land_quantity', sanitize_text_field($_POST['land_quantity'] ?? ''));

        // Save media fields
        $this->saveImageOptionBoxContent(
            'deed_thumbnail_image', 
            absint($_POST['deed_thumbnail_image'] ?? 0)
        );

        $this->saveMultipleImagesOptionBoxContent(
            'deed_images',
            array_map('absint', $_POST['deed_images'] ?? [])
        );

        $this->saveMultipleAttachmentsOptionBoxContent(
            'deed_attachments',
            array_map('absint', $_POST['deed_attachments'] ?? [])
        );
    }
}