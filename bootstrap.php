<?php
namespace Ababilithub\FlexAuthorization;

class Bootstrap
{
    //
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) 
{
    require __DIR__ . '/vendor/autoload.php';
}

new Bootstrap();

