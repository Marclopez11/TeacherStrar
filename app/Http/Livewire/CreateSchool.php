<?php

namespace App\Http\Livewire;

use App\Models\School;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CreateSchool extends Component
{
    public $name = '';
    public $city = '';
    public $description = '';

    protected $rules = [
        'name' => ['required', 'string', 'max:255'],
        'city' => ['nullable', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
    ];

    public function save()
    {
        $validated = $this->validate();

        $school = School::create($validated);
        $school->users()->attach(Auth::id(), ['role' => 'admin']);

        if (!Auth::user()->current_school_id) {
            Auth::user()->forceFill(['current_school_id' => $school->id])->save();
        }

        $this->dispatch('modal.close');

        return redirect()->route('schools.show', $school->id)
            ->with('success', 'Escuela creada exitosamente');
    }

    public function render()
    {
        return view('livewire.create-school');
    }
}
