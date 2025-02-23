<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'school_id',
        'group_id',
        'avatar_path'
    ];

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar_path) {
            // Añadir parámetro de fondo si es necesario
            $path = 'images/' . $this->avatar_path;
            if (str_ends_with($path, '.svg')) {
                // Puedes personalizar el color de fondo aquí
                return asset($path) . '?background=f8fafc'; // Color gris muy claro
            }
            return asset($path);
        }
        return asset('images/avatars/default.svg');
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

    public function attitudePoints()
    {
        return $this->belongsToMany(Attitude::class, 'student_attitude')
            ->withPivot('points')
            ->withTimestamps();
    }

    /**
     * Obtener los avatares disponibles para el grupo
     */
    public function getAvailableAvatars(): array
    {
        // Obtener todos los archivos de avatar de la carpeta
        $avatarFiles = glob(public_path('images/avatars/*.svg'));
        if (empty($avatarFiles)) {
            Log::warning('No se encontraron avatares en public/images/avatars/');
            return [];
        }

        // Convertir rutas absolutas a relativas
        $allAvatars = array_map(function($path) {
            return 'avatars/' . basename($path);
        }, $avatarFiles);

        // Excluir default.svg si existe
        $allAvatars = array_filter($allAvatars, function($path) {
            return $path !== 'avatars/default.svg';
        });

        // Obtener avatares en uso en el mismo grupo
        $usedAvatars = Student::where('group_id', $this->group_id)
            ->where('id', '!=', $this->id ?? 0)
            ->pluck('avatar_path')
            ->toArray();

        // Retornar solo los avatares disponibles
        $availableAvatars = array_values(array_diff($allAvatars, $usedAvatars));

        Log::info('Avatares disponibles:', [
            'total' => count($allAvatars),
            'en_uso' => count($usedAvatars),
            'disponibles' => count($availableAvatars)
        ]);

        return $availableAvatars;
    }

    /**
     * Asignar un avatar aleatorio disponible
     */
    public function assignRandomAvatar(): bool
    {
        $availableAvatars = $this->getAvailableAvatars();

        if (empty($availableAvatars)) {
            Log::warning('No hay avatares disponibles para asignar');
            return false;
        }

        $this->avatar_path = $availableAvatars[array_rand($availableAvatars)];
        return $this->save();
    }

    /**
     * Verificar si se puede cambiar el avatar
     */
    public function canChangeAvatar(): bool
    {
        return count($this->getAvailableAvatars()) > 0;
    }

    public function getCurrentTotalPoints($groupId): int
    {
        // Obtener todas las actitudes ordenadas por fecha
        $attitudes = $this->attitudePoints()
            ->where('group_id', $groupId)
            ->orderBy('student_attitude.created_at')
            ->get();

        $total = 0;

        foreach ($attitudes as $attitude) {
            if ($attitude->pivot->points > 0) {
                // Siempre sumar puntos positivos
                $total += $attitude->pivot->points;
            } else if ($total > 0) {
                // Solo restar si hay puntos acumulados
                $total += $attitude->pivot->points;
                // No permitir que el total sea negativo
                $total = max(0, $total);
            }
            // Si el total es 0 y los puntos son negativos, no hacer nada
        }

        return $total;
    }

    public function getPointsByGroup($groupId): int
    {
        return $this->getCurrentTotalPoints($groupId);
    }
}
