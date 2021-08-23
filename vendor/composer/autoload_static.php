<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite533d99c452f5b6c4493c7a01832ae28
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite533d99c452f5b6c4493c7a01832ae28::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite533d99c452f5b6c4493c7a01832ae28::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite533d99c452f5b6c4493c7a01832ae28::$classMap;

        }, null, ClassLoader::class);
    }
}
