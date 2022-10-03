<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Update extends Model
{
    use HasFactory;

    protected $fillable = [
        'started_at',
        'total',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'started_at' => 'datetime:Y-m-d H:i:s',
        'finished_at' => 'datetime:Y-m-d H:i:s',
        'errors' => 'array',
    ];

    public function percentSuccess(): int
    {
        if ($this->total > 0) {
            return ($this->success * 100) / $this->total;
        }

        return 100;
    }
}
