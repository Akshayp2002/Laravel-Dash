<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateTableClass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:table {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ctreate Table Class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $className = $this->argument('name');
        $path = app_path('Table/' . $className . '.php');

        if (File::exists($path)) {
            $this->error("The table class $className already exists.");
            return;
        }

        $this->createClassFile($className);
        $this->info("Table class $className created successfully.");
    }


    protected function createClassFile($className)
    {
        // Define stub path
        $stubPath = base_path('stubs/yaska-table.stub');

        // Read the stub file
        if (!File::exists($stubPath)) {
            throw new \Exception("Stub file not found at: " . $stubPath);
        }

        $stub = File::get($stubPath);

        // Replace placeholder with actual class name
        $stub = str_replace('{{className}}', $className, $stub);

        // Ensure Table directory exists
        File::ensureDirectoryExists(app_path('Table'));

        // Save the generated file
        File::put(app_path("Table/{$className}.php"), $stub);
    }



}
