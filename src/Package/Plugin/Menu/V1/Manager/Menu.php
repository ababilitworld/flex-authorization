<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Menu\V1\Manager;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexPhp\Package\Manager\V1\Base\Manager as BaseManager,
    FlexWordpress\Package\Menu\V1\Contract\Menu as MenuContract, 
    FlexWordpress\Package\Menu\V1\Factory\Menu as MenuFactory,
    FlexAuthorization\Package\Plugin\Menu\V1\Concrete\Main\Menu as MainMenu,
    FlexAuthorization\Package\Plugin\Menu\V1\Concrete\Posttype\Permission\Menu as PermissionMenu,
    FlexAuthorization\Package\Plugin\Menu\V1\Concrete\Posttype\ImportantLink\Menu as ImportantLinkMenu,
    FlexAuthorization\Package\Plugin\Menu\V1\Concrete\Posttype\Typography\Menu as TypographyMenu,
    FlexAuthorization\Package\Plugin\Menu\V1\Concrete\Posttype\ColorScheme\Menu as ColorSchemeMenu,
    FlexAuthorization\Package\Plugin\Menu\V1\Concrete\Option\Menu as OptionBoxMenu,
    FlexAuthorization\Package\Plugin\Menu\V1\Concrete\System\Wordpress\Development\Roadmap\Menu as WordpressDevelopmentRoadmapMenu,
    FlexAuthorization\Package\Plugin\Menu\V1\Concrete\System\Status\Menu as SystemStatusMenu, 
};

class  Menu extends BaseManager
{
    public function __construct()
    {
        $this->init();
    }
    
    public function init()
    {
        $this->set_items(
            [
                MainMenu::class,
                PermissionMenu::class,
                // ImportantLinkMenu::class,
                // ColorSchemeMenu::class,
                // TypographyMenu::class,
                OptionBoxMenu::class,
                WordpressDevelopmentRoadmapMenu::class,
                SystemStatusMenu::class,                    
            ]
        );
    }

    public function boot(): void 
    {
        foreach ($this->get_items() as $item) 
        {
            $item_instance = MenuFactory::get($item);

            if ($item_instance instanceof MenuContract) 
            {
                $item_instance->register();
            }
        }
    }
}