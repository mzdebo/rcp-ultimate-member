<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf6622c997b7bb7571d287508e03c43f5
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SkillfulPlugins\\' => 16,
        ),
        'R' => 
        array (
            'RCP_UM\\' => 7,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SkillfulPlugins\\' => 
        array (
            0 => __DIR__ . '/..' . '/skillfulplugins/toolbox',
        ),
        'RCP_UM\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf6622c997b7bb7571d287508e03c43f5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf6622c997b7bb7571d287508e03c43f5::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}