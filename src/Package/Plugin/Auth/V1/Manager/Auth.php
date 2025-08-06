<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Auth\V1\Manager;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexPhp\Package\Manager\V1\Base\Manager as BaseManager,
    FlexAuthorization\Package\Plugin\Auth\V1\Contract\Auth as AuthContract, 
    FlexAuthorization\Package\Plugin\Auth\V1\Factory\Auth as AuthFactory,
    FlexAuthorization\Package\Plugin\Auth\V1\Concrete\SuperAdministrator\Auth as SuperAdministratorAuth,
    FlexAuthorization\Package\Plugin\Auth\V1\Concrete\DirectorAdmin\Auth as DirectorAdminAuth,
};

class  Auth extends BaseManager
{
    public function __construct()
    {
        $this->init();
    }
    
    public function init()
    {
        $this->set_items(
            [
                SuperAdministratorAuth::class,
                DirectorAdminAuth::class,                   
            ]
        );
    }

    public function boot(): void 
    {
        foreach ($this->get_items() as $item) 
        {
            $item_instance = AuthFactory::get($item);

            //echo "<pre>";print_r($item_instance);echo "</pre>";exit;

            if ($item_instance instanceof AuthContract) 
            {
                $item_instance->init()->register();
            }
        }
    }
}