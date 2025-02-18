<?php

namespace App\Http\Livewire;

use App\Models\School;
use Livewire\Component;

class SchoolsList extends Component
{
    public $search = '';
    public $searchJoin = '';

    protected $listeners = ['schoolCreated' => '$refresh'];

    public function joinSchool(School $school)
    {
        if (!$school->users()->where('user_id', auth()->id())->exists()) {
            $school->users()->attach(auth()->id(), ['role' => 'teacher']);
            session()->flash('success', 'Te has unido a la escuela exitosamente');
            $this->emit('schoolJoined');
        }
    }

    public function render()
    {
        $mySchools = auth()->user()->schools()
            ->withCount(['groups', 'students', 'users'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('city', 'like', '%' . $this->search . '%');
            })
            ->get();

        $availableSchools = School::whereDoesntHave('users', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->when($this->searchJoin, function ($query) {
                $query->where('name', 'like', '%' . $this->searchJoin . '%')
                    ->orWhere('city', 'like', '%' . $this->searchJoin . '%');
            })
            ->withCount(['groups', 'students', 'users'])
            ->latest()
            ->get();

        return view('livewire.schools-list', [
            'mySchools' => $mySchools,
            'availableSchools' => $availableSchools,
        ]);
    }
}
