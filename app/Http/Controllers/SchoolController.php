<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class SchoolController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $school = School::create($validated);

        /** @var User $user */
        $user = Auth::user();

        $school->users()->attach($user->id, ['role' => 'admin']);

        if (!$user->current_school_id) {
            $user->current_school_id = $school->id;
            $user->save();
        }

        return to_route('schools.show', $school->id)
            ->with('success', 'Escuela creada exitosamente');
    }

    public function join(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => ['required', 'string', 'exists:schools,code'],
            ]);

            /** @var User $user */
            $user = Auth::user();
            $school = School::where('code', $validated['code'])->firstOrFail();

            // Verificar si el usuario ya está en la escuela
            if ($school->users()->where('user_id', $user->id)->exists()) {
                return back()->with('error', 'Ya eres miembro de esta escuela');
            }

            // Asignar al usuario como profesor
            $school->users()->attach($user->id, ['role' => 'teacher']);

            // Si el usuario no tiene una escuela actual, establecer esta como actual
            if (!$user->current_school_id) {
                $user->current_school_id = $school->id;
                $user->save();
            }

            return redirect()->route('dashboard')
                ->with('success', 'Te has unido a la escuela exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al unirse a la escuela: ' . $e->getMessage());
        }
    }

    public function show(School $school)
    {
        $school->load(['groups' => function($query) {
            $query->withCount(['students', 'attitudes'])->orderBy('name', 'asc');
        }]);

        return view('schools.show', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $school->update($validated);

        return back()->with('success', 'Escuela actualizada exitosamente');
    }

    public function search(Request $request)
    {
        return School::where('name', 'like', "%{$request->q}%")
            ->orWhere('city', 'like', "%{$request->q}%")
            ->take(5)
            ->get();
    }

    public function regenerateLogo(School $school)
    {
        // Generar nueva imagen
        $school->logo_path = $school->generateRandomSchoolImage();
        $school->save();

        return back()->with('success', 'Logo actualizado exitosamente');
    }
}
