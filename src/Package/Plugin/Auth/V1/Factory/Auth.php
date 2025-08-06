<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Auth\V1\Factory;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexPhp\Package\Factory\V1\Base\Factory as BaseFactory,
    FlexAuthorization\Package\Plugin\Auth\V1\Contract\Auth as AuthContract,
};

class Auth extends BaseFactory
{
    /**
     * Resolve the shortcode class instance
     *
     * @param string $targetClass
     * @return AuthContract
     */
    protected static function resolve(string $targetClass): AuthContract
    {
        $instance = new $targetClass();

        if (!$instance instanceof AuthContract) 
        {
            throw new \InvalidArgumentException("{$targetClass} must implement AuthContract");
        }

        return $instance;
    }
}