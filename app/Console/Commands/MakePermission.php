<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:permission {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Permission Enum for Spatie Permission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $enumName = $this->argument('name');
        $path = app_path('Enum/Permissions/' . $enumName . '.php');

        if (File::exists($path)) {
            $this->error("The permission enum class $enumName already exists.");
            return;
        }

        $this->createClassFile($enumName);
        $this->info("The permission enum class $enumName created successfully.");
    }

    protected function createClassFile($enumName)
    {
        // Define stub path
        $stubPath = base_path('stubs/permission.stub');

        // Read the stub file
        if (!File::exists($stubPath)) {
            throw new \Exception("Stub file not found at: " . $stubPath);
        }

        $stub = File::get($stubPath);

        // Replace placeholder with actual class name
        $stub = str_replace('{{ enumName }}', $enumName, $stub);

        // Ensure Table directory exists
        File::ensureDirectoryExists(app_path('Enum/Permissions'));

        // Save the generated file
        File::put(app_path("Enum/Permissions/{$enumName}.php"), $stub);
    }

}
