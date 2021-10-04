<?php

namespace App\Providers;

use App\Models\Setting;

use Laravel\Passport\Console\ClientCommand;
use Laravel\Passport\Console\InstallCommand;
use Laravel\Passport\Console\KeysCommand;
use Laravel\Passport\Passport;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //$data['settingss'] = Settings::all();
        if (isset($data['settingss'])){
            foreach ($data['settingss'] as $key){
                $settingss[$key->setting_key]=$key->setting_value;
            }
            view()->share($settingss);
        }

        /*ADD THIS LINES*/
    $this->commands([
        InstallCommand::class,
        ClientCommand::class,
        KeysCommand::class,
    ]);
    }
}
