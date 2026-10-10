<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'schema',
    ];

    protected $casts = [
        'schema' => 'array',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
