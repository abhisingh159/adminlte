<?php

namespace AbhiLaravelPreset\AdminLtePreset;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\Presets\Preset;

class AdminLtePreset extends Preset
{
    const STUBSPATH = __DIR__.'/white-stubs/';

    /**
     * Install the preset.
     *
     * @return void
     */
    public static function install()
    {
        static::updatePackages();
        static::updateAssets();
        
        static::updateWelcomePage();
        static::updateAuthViews();
        static::updateLayoutViews();
        static::updateDashboardPage();
        
        static::addPagesViews();
        
        static::addUserManagement();
        
        // static::removeNodeModules();
    }

    /**
     * Update the given package array.
     *
     * @param  array  $packages
     * @return array
     */
    protected static function updatePackageArray(array $packages)
    {
        return $packages;
    }

    /**
     * Update the assets
     *
     * @return void
     */
    protected static function updateAssets()
    {
        static::copyDirectory('resources/assets', public_path('white'));
    }

    /**
     * Update the default welcome page file.
     *
     * @return void
     */
    protected static function updateWelcomePage()
    {
        // remove default welcome page
        static::deleteResource(('views/welcome.blade.php'));

        // copy new one from your stubs folder
        static::copyFile('resources/views/welcome.blade.php', resource_path('views/welcome.blade.php'));
    }

    /**
     * Update the default dashboard page file.
     *
     * @return void
     */
    protected static function updateDashboardPage()
    {
        // remove default welcome page
        static::deleteResource(('views/dashboard.blade.php'));

        // copy new one from your stubs folder
        static::copyFile('resources/views/dashboard.blade.php', resource_path('views/dashboard.blade.php'));
    }

    /**
     * Update the default layout files
     *
     * @return void
     */
    protected static function updateLayoutViews()
    {
        // copy new one from your stubs folder
        static::copyDirectory('resources/views/layouts', resource_path('views/layouts'));
    }

    /**
     * Copy Auth view templates.
     *
     * @return void
     */
    protected static function updateAuthViews()
    {
        // Add Home controller
        static::copyFile('app/Http/Controllers/HomeController.php', app_path('Http/Controllers/HomeController.php'));

        // Add Auth routes in 'routes/web.php'
        file_put_contents(
            './routes/web.php',
            "Auth::routes();\n\nRoute::get('/home', 'HomeController@index')->name('home')->middleware('auth');\n\n",
            FILE_APPEND
        );
        
        // Copy white auth views from the stubs folder
        static::deleteResource('views/home.blade.php');
        static::copyDirectory('resources/views/auth', resource_path('views/auth'));
        static::copyDirectory('resources/views/alerts', resource_path('views/alerts'));
        static::copyFile('app/Http/Controllers/Auth/RegisterController.php', app_path('Http/Controllers/Auth/RegisterController.php'));
    }
    
    /**
     * Copy user management and profile edit files
     *
     * @return void
     */
    public static function addUserManagement()
    {
        // Add seeder, controllers, requests and rules
        static::copyDirectory('database/seeds', app_path('../database/seeds'));
               
        static::copyFile('app/Http/Controllers/UserController.php', app_path('Http/Controllers/UserController.php'));
        static::copyFile('app/Http/Controllers/ProfileController.php', app_path('Http/Controllers/ProfileController.php'));
        static::copyDirectory('app/Http/Requests', app_path('Http/Requests'));
        static::copyDirectory('app/Rules', app_path('Rules'));

        // Add routes
        file_put_contents(
            './routes/web.php',
            "Route::group(['middleware' => 'auth'], function () {\n\tRoute::resource('user', 'UserController', ['except' => ['show']]);\n\tRoute::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);\n\tRoute::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);\n\tRoute::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);\n});\n\n",
            FILE_APPEND
        );

        // Copy views
        static::copyDirectory('resources/views/users', resource_path('views/users'));
        static::copyDirectory('resources/views/profile', resource_path('views/profile'));
    }

    /**
     * Copy internal pages files
     *
     * @return void
     */
    public static function addPagesViews()
    {
        // Add routes
        file_put_contents(
            './routes/web.php',
            "Route::group(['middleware' => 'auth'], function () {\n\t\tRoute::get('icons', ['as' => 'pages.icons', 'uses' => 'PageController@icons']);\n\t\tRoute::get('maps', ['as' => 'pages.maps', 'uses' => 'PageController@maps']);\n\t\tRoute::get('notifications', ['as' => 'pages.notifications', 'uses' => 'PageController@notifications']);\n\t\tRoute::get('rtl', ['as' => 'pages.rtl', 'uses' => 'PageController@rtl']);\n\t\tRoute::get('tables', ['as' => 'pages.tables', 'uses' => 'PageController@tables']);\n\t\tRoute::get('typography', ['as' => 'pages.typography', 'uses' => 'PageController@typography']);\n\t\tRoute::get('upgrade', ['as' => 'pages.upgrade', 'uses' => 'PageController@upgrade']);\n});\n\n",
            FILE_APPEND
        );

        static::copyFile('app/Http/Controllers/PageController.php', app_path('Http/Controllers/PageController.php'));

        // Copy views
        static::copyDirectory('resources/views/pages', resource_path('views/pages'));
    }

    /**
     * Delete a resource
     *
     * @param string $resource
     * @return void
     */
    private static function deleteResource($resource)
    {
        (new Filesystem)->delete(resource_path($resource));
    }

    /**
     * Copy a directory
     *
     * @param string $file
     * @param string $destination
     * @return void
     */
    private static function copyFile($file, $destination)
    {
        (new Filesystem)->copy(static::STUBSPATH.$file, $destination);
    }

    /**
     * Copy a directory
     *
     * @param string $directory
     * @param string $destination
     * @return void
     */
    private static function copyDirectory($directory, $destination)
    {
        (new Filesystem)->copyDirectory(static::STUBSPATH.$directory, $destination);
    }
}