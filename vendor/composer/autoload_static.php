<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit67309048166fd7e044a9fbb98049e651
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Contracts\\Service\\' => 26,
            'Symfony\\Contracts\\HttpClient\\' => 29,
            'Symfony\\Component\\HttpClient\\' => 29,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
            'Psr\\Container\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Contracts\\Service\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/service-contracts',
        ),
        'Symfony\\Contracts\\HttpClient\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/http-client-contracts',
        ),
        'Symfony\\Component\\HttpClient\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/http-client',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/src',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit67309048166fd7e044a9fbb98049e651::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit67309048166fd7e044a9fbb98049e651::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit67309048166fd7e044a9fbb98049e651::$classMap;

        }, null, ClassLoader::class);
    }
}
