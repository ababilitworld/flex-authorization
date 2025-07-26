<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\OptionBoxContent\V1\Manager;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexPhp\Package\Manager\V1\Base\Manager as BaseManager,
    FlexWordpress\Package\OptionBoxContent\V1\Factory\OptionBoxContent as OptionBoxContentFactory,
    FlexWordpress\Package\OptionBoxContent\V1\Contract\OptionBoxContent as OptionBoxContentContract, 
    FlexAuthorization\Package\Plugin\OptionBoxContent\V1\Concrete\VerticalTabBox\OptionBoxContent as VerticalTabOptionBoxContent,   
};

class OptionBoxContent extends BaseManager
{
    public function __construct()
    {
        $this->init();
    }

    protected function init(): void
    {
        $this->set_items([
            VerticalTabOptionBoxContent::class,
            // Add more posttype classes here...
        ]);
    }

    public function boot(): void 
    {
        foreach ($this->get_items() as $itemClass) 
        {
            $posttype = OptionBoxContentFactory::get($itemClass);

            if ($posttype instanceof OptionBoxContentContract) 
            {
                $posttype->register();
            }
        }
    }
}
