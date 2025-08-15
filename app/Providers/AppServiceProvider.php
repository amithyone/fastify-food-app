<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\AssetHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Add custom Blade directive for assets
        Blade::directive('assets', function () {
            return "<?php echo \App\Helpers\AssetHelper::getAssetTags(); ?>";
        });
        
        // Add directive for CSS only
        Blade::directive('css', function () {
            return "<?php echo '<link rel=\"stylesheet\" href=\"' . \App\Helpers\AssetHelper::getCssUrl() . '\">'; ?>";
        });
        
        // Add directive for JS only
        Blade::directive('js', function () {
            return "<?php echo '<script type=\"module\" src=\"' . \App\Helpers\AssetHelper::getJsUrl() . '\"></script>'; ?>";
        });
    }
}
