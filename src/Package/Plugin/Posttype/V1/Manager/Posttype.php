<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Posttype\V1\Manager;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexPhp\Package\Manager\V1\Base\Manager as BaseManager,
    FlexWordpress\Package\Posttype\V1\Factory\Posttype as PosttypeFactory,
    FlexWordpress\Package\Posttype\V1\Contract\Posttype as PosttypeContract, 
    FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Permission\Posttype as PermissionPosttype,
    FlexAuthorization\Package\Plugin\Posttype\V1\Concrete\Role\Posttype as RolePosttype,   
};

class Posttype extends BaseManager
{
    public function __construct()
    {
        $this->init();
    }

    protected function init(): void
    {
        $this->set_items([
            PermissionPosttype::class,
            RolePosttype::class,
            // Add more posttype classes here...
        ]);
    }

    public function boot(): void 
    {
        foreach ($this->get_items() as $itemClass) 
        {
            $posttype = PosttypeFactory::get($itemClass);

            if ($posttype instanceof PosttypeContract) 
            {
                $posttype->register();
            }
        }
    }
}
