<?php

namespace App\YaskaTable;

use Yajra\DataTables\DataTables;

abstract class Yaska
{
    protected $model;
    protected $dataTables;

    public function __construct()
    {
        $this->model      = $this->setModel();
        $this->dataTables = app(DataTables::class);
    }

    /**
     * Create and handle the Test instance.
     * 
     * @return mixed
     */

    public static function make(...$arguments)
    {
        return (new static(...$arguments))->handle();
    }

    /**
     * Define the model query in child class.
     */

    abstract protected function setModel();

    /**
     * Process DataTable request.
     */

    public function handle()
    {
        return $this->dataTables
            ->eloquent($this->model)
            ->filter(function ($query) {
                if (request()->has('search') && !empty(request('search')['value'])) {
                    $search = strtolower(request('search')['value']); // Get search value
                    $this->applySearch($query, $search);
                }
            })
            ->toJson();
    }

    /**
     * Dynamically apply search filters based on the model's columns.
     */

    // strpos() is a built-in PHP function used to find the position of the first occurrence of a substring in a string.

    protected function applySearch($query, $search)
    {
        $columns = $this->getSearchableColumns(); // Get dynamically defined searchable columns

        $query->where(function ($q) use ($search, $columns) {
            foreach ($columns as $column) {
                $q->orWhereRaw("LOWER(" . $this->resolveColumn($column) . ") LIKE ?", ["%{$search}%"]);
            }
        });
    }

    /**
     * Resolves column names dynamically for deeper relationships.
     * Converts relations like "user.company.team.name" → "teams.name"
     */
    
    protected function resolveColumn($column)
    {
        return str_contains($column, '.') ? $column : "{$this->model->getModel()->getTable()}.{$column}";
    }
    
    /**
     * Get searchable columns dynamically from the model.
     */

    protected function getSearchableColumns()
    {
        $columns = $this->searchableColumns ?? [];

        // Get excluded columns from the child class or default exclusions
        $excluded = $this->excludedColumns ?? ['id', 'created_at', 'updated_at', 'deleted_at'];

        // Return only columns that are not in the excluded list
        return array_diff($columns, $excluded);
    }
}
