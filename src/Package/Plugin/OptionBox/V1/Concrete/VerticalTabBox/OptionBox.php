<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\OptionBox\V1\Concrete\VerticalTabBox;

use Ababilithub\{
    FlexWordpress\Package\OptionBox\V1\Base\OptionBox as BaseOptionBox
};

use const Ababilithub\{
    FlexAuthorization\PLUGIN_PRE_HYPH,
    FlexAuthorization\PLUGIN_PRE_UNDS,
};

class OptionBox extends BaseOptionBox 
{
    public function init(array $data = []) : static
    {
        $this->id = $data['tab_id']??PLUGIN_PRE_HYPH.'-'.'vertical-tab-options';
        $this->title = $data['title']??'Attributes';

        return $this;
    }

    public function render(): void
    {
        ?>
        <div class="fpba">
            <div class="meta-box">
                <div class="app-container">
                    <div class="vertical-tabs">
                        <div class="tabs-header">
                            <button class="toggle-tabs" id="toggleTabs">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span class="tabs-title"><?php echo $this->title;?></span>
                        </div>
                        <ul class="tab-items">
                            <?php do_action($this->id.'_'.'tab_item'); ?>
                        </ul>
                    </div>
                    <main class="content-area">
                        <?php do_action($this->id.'_'.'tab_content'); ?>
                    </main>
                </div>
            </div>
        </div>
        <?php
    }
}