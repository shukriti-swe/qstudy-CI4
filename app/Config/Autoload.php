<?php

namespace Config;

use CodeIgniter\Config\AutoloadConfig;

/**
 * -------------------------------------------------------------------
 * AUTOLOADER CONFIGURATION
 * -------------------------------------------------------------------
 *
 * This file defines the namespaces and class maps so the Autoloader
 * can find the files as needed.
 *
 * NOTE: If you use an identical key in $psr4 or $classmap, then
 * the values in this file will overwrite the framework's values.
 */

class Autoload extends AutoloadConfig
{
    /**
     * -------------------------------------------------------------------
     * Namespaces
     * -------------------------------------------------------------------
     * This maps the locations of any namespaces in your application to
     * their location on the file system. These are used by the autoloader
     * to locate files the first time they have been instantiated.
     *
     * The '/app' and '/system' directories are already mapped for you.
     * you may change the name of the 'App' namespace if you wish,
     * but this should be done prior to creating any namespaced classes,
     * else you will need to modify all of those classes for this to work.
     *
     * Prototype:
     *```
     *   $psr4 = [
     *       'CodeIgniter' => SYSTEMPATH,
     *       'App'	       => APPPATH
     *   ];
     *```
     *
     * @var array<string, string>
     */
    public $psr4 = [
        APP_NAMESPACE => APPPATH, // For custom app namespace
        'Config'      => APPPATH . 'Config',
    ];

    /**
     * -------------------------------------------------------------------
     * Class Map
     * -------------------------------------------------------------------
     * The class map provides a map of class names and their exact
     * location on the drive. Classes loaded in this manner will have
     * slightly faster performance because they will not have to be
     * searched for within one or more directories as they would if they
     * were being autoloaded through a namespace.
     *
     * Prototype:
     *```
     *   $classmap = [
     *       'MyClass'   => '/path/to/class/file.php'
     *   ];
     *```
     *
     * @var array<string, string>
     */
 
    public $classmap = [
        'StudentClass'=> FCPATH.'../app/Libraries/StudentClass.php', 
        'FaqClass'=> FCPATH.'../app/Libraries/FaqClass.php', 
        'AdminClass'=>FCPATH.'../app/Libraries/AdminClass.php',
        'RegisterClass'=>FCPATH.'../app/Libraries/RegisterClass.php',
        'ParentClass'=>FCPATH.'../app/Libraries/ParentClass.php',
        'UpperLevelClass'=>FCPATH.'../app/Libraries/UpperLevelClass.php',
        'TutorClass'=>FCPATH.'../app/Libraries/TutorClass.php',
        'MessageClass'=>FCPATH.'../app/Libraries/MessageClass.php',
        'PreviewClass'=>FCPATH.'../app/Libraries/PreviewClass.php',
        'QuestionClass'=>FCPATH.'../app/Libraries/QuestionClass.php',
        'QstudyClass'=>FCPATH.'../app/Libraries/QstudyClass.php',
        'ModuleClass'=>FCPATH.'../app/Libraries/ModuleClass.php',
        'SettingClass'=>FCPATH.'../app/Libraries/SettingClass.php',
        'SubjectClass'=>FCPATH.'../app/Libraries/SubjectClass.php'
    ];
    /**
     * -------------------------------------------------------------------
     * Files
     * -------------------------------------------------------------------
     * The files array provides a list of paths to __non-class__ files
     * that will be autoloaded. This can be useful for bootstrap operations
     * or for loading functions.
     *
     * Prototype:
     * ```
     *	  $files = [
     *	 	   '/path/to/my/file.php',
     *    ];
     * ```
     *
     * @var array<int, string>
     */
    public $files = [];
}
