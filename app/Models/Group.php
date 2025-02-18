<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'school_id',
        'description',
        'avatar_path',
        'avatar_seed',
        'avatar_style'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($group) {
            $group->avatar_seed = $group->avatar_seed ?? Str::random(10);
            $group->avatar_style = $group->avatar_style ?? 'avataaars';
            $group->avatar_path = $group->generateAvatar();
        });
    }

    public function generateAvatar()
    {
        try {
            $imageUrl = "https://api.dicebear.com/7.x/{$this->avatar_style}/svg?seed={$this->avatar_seed}&backgroundColor=transparent";
            $imageContent = Http::get($imageUrl)->body();

            if (!file_exists(public_path('images/groups'))) {
                mkdir(public_path('images/groups'), 0755, true);
            }

            $filename = 'groups/' . $this->avatar_seed . '.svg';
            file_put_contents(public_path('images/' . $filename), $imageContent);

            return $filename;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function attitudes(): HasMany
    {
        return $this->hasMany(Attitude::class);
    }

    public function scopeBelongsToSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}
