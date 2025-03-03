<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\School;
use App\Models\Attitude;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    private function checkSchoolMembership(School $school)
    {
        if (!$school->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'No tienes acceso a esta escuela.');
        }
    }

    private function checkSchoolAdmin(School $school)
    {
        if (!$school->users()->where('user_id', Auth::id())->wherePivot('role', 'admin')->exists()) {
            abort(403, 'No tienes permisos de administrador en esta escuela.');
        }
    }

    private function checkGroupAccess(Group $group)
    {
        if (!$group->school->users()->where('user_id', Auth::id())->exists()) {
            abort(403, 'No tienes acceso a este grupo.');
        }
    }

    public function index(School $school)
    {
        $this->checkSchoolMembership($school);

        $groups = $school->groups()
            ->withCount(['students', 'attitudes'])
            ->get()
            ->sortBy('name');

        return view('groups.index', compact('school', 'groups'));
    }

    public function store(Request $request, School $school)
    {
        $this->checkSchoolAdmin($school);

        // Limpiar y preparar los datos antes de la validación
        $data = $request->all();

        // Convertir string "[]" a array vacío
        if (isset($data['existing_attitude_points']) && $data['existing_attitude_points'] === '[]') {
            $data['existing_attitude_points'] = [];
        }

        $validated = validator($data, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'new_attitudes' => ['nullable', 'array'],
            'new_attitudes.*' => ['nullable', 'string', 'max:255'],
            'new_attitude_points' => ['nullable', 'array'],
            'new_attitude_points.*' => ['nullable', 'integer'],
            'existing_attitudes' => ['nullable', 'array'],
            'existing_attitudes.*' => ['nullable', 'exists:attitudes,id'],
            'existing_attitude_points' => ['nullable', 'array'],
            'existing_attitude_points.*' => ['nullable', 'integer'],
            'avatar_seed' => ['nullable', 'string'],
            'avatar_style' => ['nullable', 'string'],
        ])->validate();

        try {
            DB::beginTransaction();

            // Crear el grupo
            $group = $school->groups()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'avatar_seed' => $validated['avatar_seed'] ?? Str::random(10),
                'avatar_style' => $validated['avatar_style'] ?? 'shapes',
            ]);

            if (!$group) {
                throw new \Exception('No se pudo crear el grupo');
            }

            // Generar avatar
            $group->avatar_path = $group->generateAvatar();
            $group->save();

            // Procesar actitudes existentes si existen
            if (!empty($validated['existing_attitudes'])) {
                foreach ($validated['existing_attitudes'] as $index => $attitudeId) {
                    if (!empty($attitudeId)) {
                        $existingAttitude = Attitude::find($attitudeId);
                        if ($existingAttitude) {
                            $points = $validated['existing_attitude_points'][$index] ?? $existingAttitude->points;

                            $group->attitudes()->create([
                                'name' => $existingAttitude->name,
                                'points' => $points,
                            ]);
                        }
                    }
                }
            }

            // Procesar nuevas actitudes si existen
            if (!empty($validated['new_attitudes']) && is_array($validated['new_attitudes'])) {
                foreach ($validated['new_attitudes'] as $index => $name) {
                    if (!empty($name)) {
                        $points = isset($validated['new_attitude_points'][$index]) ?
                                 intval($validated['new_attitude_points'][$index]) : 0;

                        $group->attitudes()->create([
                            'name' => $name,
                            'points' => $points,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('groups.show', ['school' => $school->id, 'group' => $group->id])
                            ->with('success', 'Grupo creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el grupo: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function show(School $school, $groupId)
    {
        $this->checkSchoolMembership($school);

        $group = Group::belongsToSchool($school->id)->findOrFail($groupId);
        $this->checkGroupAccess($group);

        $group->load(['students', 'attitudes']);
        return view('groups.show', compact('school', 'group'));
    }

    public function update(Request $request, School $school, Group $group)
    {
        $this->checkSchoolAdmin($school);
        $this->checkGroupAccess($group);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'avatar_seed' => ['required', 'string'],
            'avatar_style' => ['required', 'string'],
            'attitudes' => ['nullable', 'array'],
            'attitudes.*.name' => ['required', 'string', 'max:255'],
            'attitudes.*.points' => ['required', 'integer'],
            'new_attitudes' => ['nullable', 'array'],
            'new_attitudes.*' => ['required', 'string', 'max:255'],
            'new_attitude_points' => ['nullable', 'array'],
            'new_attitude_points.*' => ['required', 'integer'],
            'delete_attitudes' => ['nullable', 'array'],
            'delete_attitudes.*' => ['required', 'integer'],
        ]);

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'avatar_seed' => $validated['avatar_seed'],
            'avatar_style' => $validated['avatar_style'],
        ]);

        // Generar nuevo avatar si cambió el seed
        if ($group->wasChanged('avatar_seed')) {
            $group->avatar_path = $group->generateAvatar();
            $group->save();
        }

        // Actualizar actitudes existentes
        if (!empty($validated['attitudes'])) {
            foreach ($validated['attitudes'] as $id => $data) {
                $group->attitudes()->where('id', $id)->update([
                    'name' => $data['name'],
                    'points' => $data['points'],
                ]);
            }
        }

        // Eliminar actitudes marcadas para eliminar
        if (!empty($validated['delete_attitudes'])) {
            $group->attitudes()->whereIn('id', $validated['delete_attitudes'])->delete();
        }

        // Crear nuevas actitudes
        if (!empty($validated['new_attitudes'])) {
            foreach ($validated['new_attitudes'] as $index => $name) {
                if (!empty($name)) {
                    $points = $validated['new_attitude_points'][$index];
                    $group->attitudes()->create([
                        'name' => $name,
                        'points' => $points,
                    ]);
                }
            }
        }

        return back()->with('success', 'Grupo actualizado exitosamente');
    }

    public function destroy(School $school, Group $group)
    {
        $this->checkSchoolAdmin($school);
        $this->checkGroupAccess($group);

        $group->delete();
        return back()->with('success', 'Grupo eliminado exitosamente');
    }

    public function create(School $school)
    {
        $this->checkSchoolAdmin($school);

        // Obtener todas las actitudes existentes de la escuela
        $existingAttitudes = Attitude::whereHas('group', function($query) use ($school) {
            $query->where('school_id', $school->id);
        })->get();

        return view('groups.create', compact('school', 'existingAttitudes'));
    }

    public function regenerateAvatar(School $school, Group $group)
    {
        $this->checkSchoolAdmin($school);
        $this->checkGroupAccess($group);

        $group->update([
            'avatar_seed' => Str::random(10),
            'avatar_style' => 'shapes'
        ]);

        $group->avatar_path = $group->generateAvatar();
        $group->save();

        return back()->with('success', 'Avatar regenerado exitosamente');
    }

    public function ranking(School $school, Group $group)
    {
        $this->checkSchoolMembership($school);
        $this->checkGroupAccess($group);

        $sort = request('sort', 'alpha'); // Default to alphabetical sorting

        // Load students with their points
        $students = $group->students;

        // Calculate points for each student and add it as a temporary attribute
        $students = $students->map(function($student) use ($group) {
            $student->calculated_points = $student->getPointsByGroup($group->id);
            return $student;
        });

        // Sort the collection
        if ($sort === 'points') {
            $students = $students->sortByDesc('calculated_points')->values();
        } else {
            $students = $students->sortBy('name')->values();
        }

        return view('groups.ranking', [
            'school' => $school,
            'group' => $group,
            'students' => $students
        ]);
    }
}
