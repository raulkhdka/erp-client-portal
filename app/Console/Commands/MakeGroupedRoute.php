<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeGroupedRoute extends Command
{
    protected $signature = 'make:grouped-route {name}';
    protected $description = 'Generate a grouped route block for a given resource name and controller';

    public function handle()
    {
        $name = trim($this->argument('name'), '/');
        $route = Str::slug($name);
        $controller = Str::studly(Str::singular($name)) . 'Controller';
        $prefix = Str::slug($name);
        $routeName = 'admin.' . Str::snake($name) . '.';

        $stub = <<<EOT
Route::prefix('$prefix')
    ->name('$routeName')
    ->controller(App\\Http\\Controllers\\$controller::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::get('/{id}', 'show')->name('show');
    });
EOT;

        $this->line("\nCopy the following route block into your `routes/web.php` file:\n");
        $this->line($stub);
    }
}
