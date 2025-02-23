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

class GroupController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index(School $school)
    {
        $groups = $school->groups()
            ->withCount(['students', 'attitudes'])
            ->get()
            ->sortBy('name');

        return view('groups.index', compact('school', 'groups'));
    }

    public function store(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'new_attitudes' => ['nullable', 'array'],
            'new_attitudes.*' => ['required', 'string', 'max:255'],
            'new_attitude_points' => ['nullable', 'array'],
            'new_attitude_points.*' => ['required', 'integer'],
        ]);

        $group = $school->groups()->create([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

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

        return back()->with('success', 'Grupo creado exitosamente');
    }

    public function show(School $school, $groupId)
    {
        $group = Group::belongsToSchool($school->id)->findOrFail($groupId);
        $group->load(['students', 'attitudes']);
        return view('groups.show', compact('school', 'group'));
    }

    public function update(Request $request, School $school, Group $group)
    {
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
        $group->delete();
        return back()->with('success', 'Grupo eliminado exitosamente');
    }

    public function create(School $school)
    {
        // Obtener todas las actitudes existentes de la escuela
        $existingAttitudes = Attitude::whereHas('group', function($query) use ($school) {
            $query->where('school_id', $school->id);
        })->get();

        return view('groups.create', compact('school', 'existingAttitudes'));
    }

    public function regenerateAvatar(School $school, Group $group)
    {
        $group->update([
            'avatar_seed' => Str::random(10),
            'avatar_style' => 'avataaars'
        ]);

        $group->avatar_path = $group->generateAvatar();
        $group->save();

        return back()->with('success', 'Avatar regenerado exitosamente');
    }

    public function ranking(School $school, Group $group)
    {
        $sort = request('sort', 'alpha'); // Default to alphabetical sorting

        $students = $group->students; // Get the collection first

        if ($sort === 'points') {
            $students = $students->sortByDesc(function($student) use ($group) {
                return $student->getPointsByGroup($group->id);
            });
        } else {
            $students = $students->sortBy('name');
        }

        return view('groups.ranking', [
            'school' => $school,
            'group' => $group,
            'students' => $students
        ]);
    }
}
