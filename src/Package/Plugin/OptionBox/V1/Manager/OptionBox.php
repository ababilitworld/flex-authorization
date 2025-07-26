<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\OptionBox\V1\Manager;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexPhp\Package\Manager\V1\Base\Manager as BaseManager,
    FlexWordpress\Package\OptionBox\V1\Factory\OptionBox as OptionBoxFactory,
    FlexWordpress\Package\OptionBox\V1\Contract\OptionBox as OptionBoxContract, 
    FlexAuthorization\Package\Plugin\OptionBox\V1\Concrete\VerticalTabBox\OptionBox as VerticalTabOptionBox,   
};

class OptionBox extends BaseManager
{
    public function __construct()
    {
        $this->init();
    }

    protected function init(): void
    {
        $this->set_items([
            VerticalTabOptionBox::class,
            // Add more posttype classes here...
        ]);
    }

    public function boot(): void 
    {
        foreach ($this->get_items() as $itemClass) 
        {
            $posttype = OptionBoxFactory::get($itemClass);

            if ($posttype instanceof OptionBoxContract) 
            {
                $posttype->register();
            }
        }
    }
}
