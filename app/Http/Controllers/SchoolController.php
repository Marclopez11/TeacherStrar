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
use Illuminate\Support\Facades\Hash;

class SchoolController extends BaseController
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'password' => ['required', 'string', 'min:6'],
            'logo_path' => ['nullable', 'url', 'max:255'],
        ]);

        $school = School::create($validated);

        /** @var User $user */
        $user = Auth::user();

        $school->users()->attach($user->id, ['role' => 'admin']);

        if (!$user->current_school_id) {
            $user->current_school_id = $school->id;
            $user->save();
        }

        return to_route('schools.show', $school)
            ->with('success', 'Escuela creada exitosamente');
    }

    public function join(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => ['required', 'string', 'exists:schools,code'],
                'password' => ['required', 'string'],
            ]);

            /** @var User $user */
            $user = Auth::user();
            $school = School::where('code', $validated['code'])->firstOrFail();

            if ($validated['password'] !== $school->password) {
                return back()->with('error', 'La contraseña es incorrecta');
            }

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
        $this->checkSchoolMembership($school);

        // Cargar la escuela con los contadores necesarios
        $school = $school->load(['groups' => function($query) {
            $query->withCount(['students', 'attitudes'])->orderBy('name', 'asc');
        }])
        ->loadCount(['groups', 'students', 'users']);

        return view('schools.show', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $this->checkSchoolAdmin($school);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:6'],
            'logo_path' => ['nullable', 'url', 'max:255'],
        ]);

        if (!empty($validated['password'])) {
            // La contraseña se guarda sin encriptar
        } else {
            unset($validated['password']);
        }

        $school->update($validated);

        return back()->with('success', 'Escuela actualizada exitosamente');
    }

    public function search(Request $request)
    {
        // Solo devolver escuelas donde el usuario es miembro
        return School::whereHas('users', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->where('name', 'like', "%{$request->q}%")
        ->orWhere('city', 'like', "%{$request->q}%")
        ->take(5)
        ->get();
    }

    public function regenerateLogo(School $school)
    {
        $this->checkSchoolAdmin($school);

        // Generar nueva imagen
        $school->logo_path = $school->generateRandomSchoolImage();
        $school->save();

        return back()->with('success', 'Logo actualizado exitosamente');
    }
}
