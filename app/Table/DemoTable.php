<?php

namespace App\Table;

use App\YaskaTable\Yaska;
use App\Models\User;

class DemoTable extends Yaska
{
    protected $arguments;

    public function __construct(...$arguments)
    {
        $this->arguments = $arguments;
        parent::__construct();
    }

    /**
     * Must define the model query.
     */

    protected function setModel()
    {
        return User::query()->select(['id', 'name']);
    }

    /**
     * Transform the data format before sending response.
     */

    public function configure($value)
    {
        return [
            'id'         => $value->id,
            'name'       => $value->name,
        ];
    }
}
