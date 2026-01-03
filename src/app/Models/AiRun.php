<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiRun extends Model
{
    protected $fillable = [
        'run_type', 'entity_type', 'entity_id', 'requested_by',
        'provider', 'model', 'prompt', 'response',
        'status', 'duration_ms', 'error_message', 'attempt'
    ];

    public function entity()
    {
        return $this->morphTo();
    }
}
