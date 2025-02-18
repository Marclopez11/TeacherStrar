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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'group_id' => ['required', 'exists:groups,id'],
            'avatar_seed' => ['required', 'string'],
            'avatar_style' => ['required', 'string'],
        ]);

        // Obtener el school_id del grupo
        $group = Group::findOrFail($validated['group_id']);
        $validated['school_id'] = $group->school_id;

        $student = Student::create($validated);

        return redirect()->back()
            ->with('success', 'Estudiante creado exitosamente');
    }

    public function updateAvatar(Student $student)
    {
        // Cambiar aleatoriamente el estilo del avatar
        $student->update([
            'avatar_style' => Student::getRandomAvatarStyle(),
            'avatar_seed' => Str::random(10), // Nuevo seed para nueva apariencia
        ]);

        return redirect()->back()
            ->with('success', 'Avatar actualizado exitosamente');
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

            if ($validated['multiplier'] > 0) {
                // Añadir una nueva actitud
                $student->attitudes()->attach($attitude->id, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $message = 'Actitud registrada correctamente';
            } else {
                // Buscar y eliminar una sola ocurrencia de la actitud
                $attitudeToDelete = DB::table('student_attitude')
                    ->where([
                        'student_id' => $student->id,
                        'attitude_id' => $attitude->id,
                    ])
                    ->where('created_at', '>=', now()->startOfDay())
                    ->first();

                if ($attitudeToDelete) {
                    DB::table('student_attitude')
                        ->where('id', $attitudeToDelete->id)
                        ->limit(1)
                        ->delete();

                    $message = 'Actitud eliminada correctamente';
                } else {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'No hay actitudes para eliminar hoy'
                    ], 400);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'new_points' => $student->getPointsByGroup($attitude->group_id),
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
