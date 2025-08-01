<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Role\V1\Manager;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexPhp\Package\Manager\V1\Base\Manager as BaseManager,
    FlexAuthorization\Package\Plugin\Role\V1\Contract\Role as RoleContract, 
    FlexAuthorization\Package\Plugin\Role\V1\Factory\Role as RoleFactory,
    FlexAuthorization\Package\Plugin\Role\V1\Concrete\DirectorAdmin\Role as DirectorAdminRole,
};

class  Role extends BaseManager
{
    public function __construct()
    {
        $this->init();
    }
    
    public function init()
    {
        $this->set_items(
            [
                DirectorAdminRole::class,                   
            ]
        );
    }

    public function boot(): void 
    {
        foreach ($this->get_items() as $item) 
        {
            $item_instance = RoleFactory::get($item);

            if ($item_instance instanceof RoleContract) 
            {
                $item_instance->register();
            }
        }
    }
}