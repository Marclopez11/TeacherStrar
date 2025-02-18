<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'school_id',
        'group_id',
        'avatar_seed',
        'avatar_style',
        'avatar_path'
    ];

    protected $appends = ['avatar_url'];

    // Estilos disponibles de avatares
    public static function getAvatarStyles()
    {
        return [
            'avataaars' => [
                'name' => 'Personaje',
                'options' => '&mouth[]=smile&eyes[]=happy&top[]=hat&accessories[]=round&facialHair[]=none&clothing[]=hoodie'
            ],
            'bottts' => [
                'name' => 'Robot',
                'options' => '&textureChance=50&mouthChance=50&sidesChance=50&topChance=50'
            ],
            'notionists' => [
                'name' => 'Monstruito',
                'options' => '&rotation=0'
            ],
            'open-peeps' => [
                'name' => 'Peeps',
                'options' => '&mood=happy'
            ],
            'big-smile' => [
                'name' => 'Sonriente',
                'options' => ''
            ],
            'micah' => [
                'name' => 'Colorido',
                'options' => '&baseColor[]=indigo&mouth[]=smile&hair[]=messy'
            ],
        ];
    }

    // Seleccionar estilo aleatorio
    public static function getRandomAvatarStyle(): string
    {
        $styles = [
            'avataaars',
            'bottts',
            'pixelart',
            'lorelei',
            'adventurer',
            'big-ears',
            'croodles'
        ];

        return $styles[array_rand($styles)];
    }

    // URL del avatar usando DiceBear
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_path && file_exists(public_path('images/students/' . $this->avatar_path))) {
            return asset('images/students/' . $this->avatar_path);
        }

        return "https://api.dicebear.com/7.x/{$this->avatar_style}/svg?seed={$this->avatar_seed}&backgroundColor=transparent";
    }

    // URL alternativa usando Multiavatar (más colorido y divertido)
    public function getMultiavatarUrlAttribute()
    {
        return "https://api.multiavatar.com/{$this->avatar_seed}.svg";
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function attitudes(): BelongsToMany
    {
        return $this->belongsToMany(Attitude::class, 'student_attitude')
            ->withTimestamps();
    }

    // Obtener puntos totales por grupo
    public function getPointsByGroup($groupId): int
    {
        return $this->attitudes()
            ->where('group_id', $groupId)
            ->sum('points');
    }

    public function generateAndSaveAvatar(): bool
    {
        try {
            $imageUrl = "https://api.dicebear.com/7.x/{$this->avatar_style}/svg?seed={$this->avatar_seed}&backgroundColor=transparent";
            $imageContent = Http::get($imageUrl)->body();

            if (!file_exists(public_path('images/students'))) {
                mkdir(public_path('images/students'), 0755, true);
            }

            $filename = "avatar_{$this->id}_{$this->avatar_seed}.svg";
            file_put_contents(public_path('images/students/' . $filename), $imageContent);

            $this->avatar_path = 'students/' . $filename;
            return $this->save();
        } catch (\Exception $e) {
            \Log::error('Error generando avatar: ' . $e->getMessage());
            return false;
        }
    }
}
