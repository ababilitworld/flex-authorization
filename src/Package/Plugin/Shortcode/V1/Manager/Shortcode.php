<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Shortcode\V1\Manager;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexPhp\Package\Manager\V1\Base\Manager as BaseManager,
    FlexWordpress\Package\Shortcode\V1\Factory\Shortcode as ShortcodeFactory,
    FlexWordpress\Package\Shortcode\V1\Contract\Shortcode as ShortcodeContract,
    FlexWordpress\Package\Shortcode\V1\Concrete\System\Status\Shortcode as SystemStatusShortcode,
    FlexAuthorization\Package\Plugin\Shortcode\V1\Concrete\System\Wordpress\Development\Roadmap\Shortcode as WordpressDevelopmentRoadmapShortcode,
    
};

class Shortcode extends BaseManager
{
    public function __construct()
    {
        $this->init();
    }

    protected function init(): void
    {
        $this->set_items([
            SystemStatusShortcode::class,
            WordpressDevelopmentRoadmapShortcode::class,
            // Add more shortcode classes here...
        ]);
    }

    public function boot(): void 
    {
        foreach ($this->get_items() as $itemClass) 
        {
            $shortcode = ShortcodeFactory::get($itemClass);

            if ($shortcode instanceof ShortcodeContract) 
            {
                $shortcode->register();
            }
        }
    }
}
