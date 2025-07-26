<?php
namespace Ababilithub\FlexAuthorization\Package\Plugin\Audit\V1\Factory;

(defined('ABSPATH') && defined('WPINC')) || exit();

use Ababilithub\{
    FlexPhp\Package\Factory\V1\Base\Factory as BaseFactory,
    FlexAuthorization\Package\Plugin\Audit\V1\Contract\Audit as AuditContract,
};

class Audit extends BaseFactory
{
    /**
     * Resolve the shortcode class instance
     *
     * @param string $targetClass
     * @return AuditContract
     */
    protected static function resolve(string $targetClass): AuditContract
    {
        $instance = new $targetClass();

        if (!$instance instanceof AuditContract) 
        {
            throw new \InvalidArgumentException("{$targetClass} must implement AuditContract");
        }

        return $instance;
    }
}