<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Production;

(defined( 'ABSPATH' ) && defined( 'WPINC' )) || exit();

use Ababilithub\{
    FlexPhp\Package\Mixin\V1\Standard\Mixin as StandardMixin,
    FlexAuthorization\Package\Plugin\Menu\V1\Manager\Menu as MenuManager,
    FlexAuthorization\Package\Plugin\Role\V1\Manager\Role as RoleManager,
    FlexAuthorization\Package\Plugin\Posttype\V1\Manager\Posttype as PosttypeManager,
    FlexAuthorization\Package\Plugin\Shortcode\V1\Manager\Shortcode as ShortcodeManager, 
    FlexAuthorization\Package\Plugin\OptionBox\V1\Manager\OptionBox as OptionBoxManager,
};

if (!class_exists(__NAMESPACE__.'\Production')) 
{
    class Production 
    {
        use StandardMixin;

        public function __construct($data = []) 
        {
            $this->init();      
        }

        public function init() 
        {
            // add_action('init', function () {
            //     (new TaxonomyManager())->boot();
            // });

            // add_action('init', function () {
            //     (new PosttypeManager())->boot();
            // });

            // add_action('init', function () {
            //     (new ShortcodeManager())->boot();
            // });

            // add_action('init', function () {
            //     (new OptionBoxManager())->boot();
            // });

            add_action('init', function() {
                (new RoleManager())->boot();
            });

            // Initialize only once on admin_menu
            add_action('init', function () {
                (new MenuManager())->boot();
            });

        }
        
    }
}