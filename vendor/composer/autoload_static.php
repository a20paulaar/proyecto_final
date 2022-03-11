<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc2019505f0e67ad704efd5affb1ca8e5
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc2019505f0e67ad704efd5affb1ca8e5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc2019505f0e67ad704efd5affb1ca8e5::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc2019505f0e67ad704efd5affb1ca8e5::$classMap;

        }, null, ClassLoader::class);
    }
}