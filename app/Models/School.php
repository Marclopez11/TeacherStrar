<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'code',
        'description',
        'logo_path'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($school) {
            $school->code = strtoupper(Str::random(6));
            $school->logo_path = $school->generateRandomSchoolImage();
        });
    }

    public function generateRandomSchoolImage()
    {
        $seed = Str::random(10);
        $style = collect(['initials', 'shapes', 'pixel-art'])->random();

        try {
            $imageUrl = "https://api.dicebear.com/7.x/{$style}/svg?seed={$seed}&size=200&backgroundColor=ffffff";
            $imageContent = Http::get($imageUrl)->body();

            // Crear el directorio si no existe
            if (!file_exists(public_path('images/schools'))) {
                mkdir(public_path('images/schools'), 0755, true);
            }

            // Guardar en public/images/schools
            $filename = 'schools/' . $seed . '.svg';
            file_put_contents(public_path('images/' . $filename), $imageContent);

            return $filename;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function students(): HasManyThrough
    {
        return $this->hasManyThrough(
            Student::class,
            Group::class,
            'school_id', // Foreign key on groups table...
            'id', // Foreign key on students table...
            'id', // Local key on schools table...
            'id'  // Local key on groups table...
        )->distinct();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('role')
                    ->withTimestamps();
    }
}
