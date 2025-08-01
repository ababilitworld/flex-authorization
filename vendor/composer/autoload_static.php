<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3789480e7fdd24274440fefd59524edd
{
    public static $files = array (
        'a2111ff052e9e9f4111d74e30709d484' => __DIR__ . '/../..' . '/includes.php',
    );

    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Ababilithub\\FlexAuthorization\\' => 30,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Ababilithub\\FlexAuthorization\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
            1 => __DIR__ . '/../..' . '/Test',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3789480e7fdd24274440fefd59524edd::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3789480e7fdd24274440fefd59524edd::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3789480e7fdd24274440fefd59524edd::$classMap;

        }, null, ClassLoader::class);
    }
}
