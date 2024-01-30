<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ontology extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }
}
