<?php

namespace App\Models;

use App\Casts\Url;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'url' => Url::class,
        'website' => Url::class,
        'validated' => 'boolean',
        'validated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $fillable = [
        'url',
        'name',
        'email',
        'comment',
    ];

    public function validatedBy()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->using(SubmissionTag::class);
    }
}
