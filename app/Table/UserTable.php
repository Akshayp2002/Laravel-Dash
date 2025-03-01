<?php

namespace App\Table;

use App\Models\TeamInvitation;
use App\YaskaTable\Yaska;
use App\Models\User;

class UserTable extends Yaska
{
    protected $arguments;

    public function __construct(...$arguments)
    {
        $this->arguments = $arguments;
        parent::__construct();
    }

    /**
     * Must define the model query.
     * 
     * ⚡ To achieve the full potential of this DataTable with Eloquent relationships, 
     *  consider using optimized joins instead of multiple queries.
     * 
     * 🚀 Recommended: Use 'Power Joins' for better performance and easier relationship handling.
     * 📖 Reference: https://kirschbaumdevelopment.com/insights/power-joins
     */

    protected function setModel()
    {
        return TeamInvitation::query()
            ->joinRelationship('team')
            ->select(
                'team_invitations.id',
                'team_invitations.email',
                'team_invitations.role',
                'team_invitations.team_id',
                'teams.name as team_name',
                'teams.personal_team as personal_team'
            );
    }

    // Define which columns should be searchable in the DataTable.
    protected $searchableColumns = [
        'team_invitations.email',
        'teams.name',
        'teams.personal_team',
        'team_invitations.role',
    ];

    // Define columns that should be excluded from searches.
    // These columns will not be included in the filtering process.
    protected $excludedColumns = [
        'id',
    ];
}
