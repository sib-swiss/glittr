<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ontology extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'term_set',
    ];

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }
}
