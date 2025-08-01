<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Role\V1\Factory;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexPhp\Package\Factory\V1\Base\Factory as BaseFactory,
    FlexAuthorization\Package\Plugin\Role\V1\Contract\Role as RoleContract,
};

class Role extends BaseFactory
{
    /**
     * Resolve the shortcode class instance
     *
     * @param string $targetClass
     * @return RoleContract
     */
    protected static function resolve(string $targetClass): RoleContract
    {
        $instance = new $targetClass();

        if (!$instance instanceof RoleContract) 
        {
            throw new \InvalidArgumentException("{$targetClass} must implement RoleContract");
        }

        return $instance;
    }
}