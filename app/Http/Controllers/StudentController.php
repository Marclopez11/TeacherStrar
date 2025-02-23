<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Student;
use App\Models\Group;
use App\Models\Attitude;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StudentController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index(School $school)
    {
        $students = $school->students()
            ->with(['group'])
            ->get()
            ->groupBy('group.name');

        return view('students.index', compact('school', 'students'));
    }

    public function store(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'group_id' => ['required', 'exists:groups,id'],
        ]);

        // Obtener el school_id del grupo
        $group = Group::findOrFail($validated['group_id']);
        $validated['school_id'] = $group->school_id;

        // Crear el estudiante sin avatar inicialmente
        $student = Student::create($validated);

        // Intentar asignar un avatar aleatorio
        if (!$student->assignRandomAvatar()) {
            // Si no hay avatares disponibles, asignar el default
            $student->update(['avatar_path' => 'avatars/default.svg']);
        }

        return redirect()->back()
            ->with('success', 'Estudiante creado exitosamente');
    }

    public function updateAvatar(School $school, Student $student)
    {
        // Verificar que el estudiante pertenece a la escuela
        if ($student->school_id !== $school->id) {
            return redirect()->back()
                ->with('error', 'El estudiante no pertenece a esta escuela');
        }

        if (!$student->canChangeAvatar()) {
            return redirect()->back()
                ->with('error', 'No hay avatares disponibles para cambiar');
        }

        if ($student->assignRandomAvatar()) {
            return redirect()->back()
                ->with('success', 'Avatar actualizado exitosamente');
        }

        return redirect()->back()
            ->with('error', 'No se pudo actualizar el avatar');
    }

    public function registerAttitude(Request $request, School $school, Student $student)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'attitude_id' => ['required', 'exists:attitudes,id'],
                'multiplier' => ['required', 'integer', 'in:-1,1']
            ]);

            $attitude = Attitude::findOrFail($validated['attitude_id']);

            if ($attitude->group_id !== $student->group_id) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'La actitud no pertenece al grupo del estudiante'
                ], 400);
            }

            // Obtener el total actual de puntos
            $currentTotal = $student->getCurrentTotalPoints($attitude->group_id);

            if ($validated['multiplier'] > 0) {
                // Siempre añadir puntos positivos
                $student->attitudes()->attach($attitude->id, [
                    'points' => $attitude->points,
                    'is_positive' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $message = 'Actitud positiva registrada correctamente';
            } else {
                // Para puntos negativos, solo restar si hay puntos positivos acumulados
                if ($currentTotal > 0) {
                    $student->attitudes()->attach($attitude->id, [
                        'points' => $attitude->points, // Ya viene negativo de la BD
                        'is_positive' => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $message = 'Actitud negativa registrada correctamente';
                } else {
                    // Si el total es 0, registrar la actitud pero con 0 puntos
                    $student->attitudes()->attach($attitude->id, [
                        'points' => 0,
                        'is_positive' => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $message = 'Actitud negativa registrada (sin efecto en puntos)';
                }
            }

            DB::commit();

            // Recalcular el total final
            $finalTotal = $student->getCurrentTotalPoints($attitude->group_id);

            return response()->json([
                'success' => true,
                'message' => $message,
                'new_points' => $finalTotal,
                'current_count' => DB::table('student_attitude')
                    ->where([
                        'student_id' => $student->id,
                        'attitude_id' => $attitude->id,
                    ])
                    ->where('created_at', '>=', now()->startOfDay())
                    ->count()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar actitud:', [
                'error' => $e->getMessage(),
                'student_id' => $student->id,
                'attitude_id' => $validated['attitude_id'] ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeAttitude(School $school, Student $student, Attitude $attitude)
    {
        $student->attitudes()->detach($attitude->id);

        return response()->json([
            'success' => true,
            'message' => 'Actitud eliminada correctamente',
            'new_points' => $student->getPointsByGroup($attitude->group_id)
        ]);
    }

    public function quickAttitude(Request $request, School $school, Student $student)
    {
        $validated = $request->validate([
            'points' => ['required', 'integer', 'in:-1,1']
        ]);

        // Crear o encontrar una actitud rápida
        $name = $validated['points'] > 0 ? 'Actitud Positiva' : 'Actitud Negativa';
        $attitude = Attitude::firstOrCreate(
            [
                'group_id' => $student->group_id,
                'name' => $name,
                'points' => $validated['points']
            ]
        );

        // Registrar la actitud
        $student->attitudes()->attach($attitude->id, [
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Actitud registrada correctamente',
            'new_points' => $student->getPointsByGroup($attitude->group_id)
        ]);
    }
}
