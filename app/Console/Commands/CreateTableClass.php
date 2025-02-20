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
        $tableClassName = $this->argument('name'); // Get the table class name from the command argument

        // Define the path to save the new file
        $path = app_path('Table/' . $tableClassName . '.php');

        // Check if the file already exists
        if (File::exists($path)) {
            $this->error("The table class $tableClassName already exists.");
            return;
        }

        // Generate the table class file
        $this->createTableClassFile($tableClassName);

        $this->info("Table class $tableClassName created successfully.");
    }


    // Method to create the table class file
    protected function createTableClassFile($tableClassName)
    {
        // Define the stub content for the table class
        $stub = "<?php

namespace App\Table;

use App\YaskaTable\Yaska;
use App\Models\User;

class $tableClassName extends Yaska
{
    protected \$arguments;

    public function __construct(...\$arguments)
    {
        \$this->arguments = \$arguments;
        parent::__construct();
    }

     /**
     * Must define the model query.
     * 
     * ⚡ To achieve the full potential of this DataTable with Eloquent relationships, 
     * consider using optimized joins instead of multiple queries.
     * 
     * 🚀 Recommended: Use 'Power Joins' for better performance and easier relationship handling.
     * 📖 Reference: https://kirschbaumdevelopment.com/insights/power-joins
     */

    protected function setModel()
    {
        return User::query();
    }

    // Define which columns should be searchable in the DataTable.
    protected \$searchableColumns = [

    ];

    // Define columns that should be excluded from searches.
    // These columns will not be included in the filtering process.
    protected \$excludedColumns = [
        'id',
    ];




}

                    ";

        // Save the generated file to the app/Table directory
        File::ensureDirectoryExists(app_path('Table')); // Ensure the Table directory exists
        File::put(app_path('Table/' . $tableClassName . '.php'), $stub);
    }



}
