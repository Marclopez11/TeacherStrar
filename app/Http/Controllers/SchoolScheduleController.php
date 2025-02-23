<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Group;
use App\Models\TimeSlot;
use App\Models\ScheduleEntry;
use App\Models\TeacherScheduleEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SchoolScheduleController extends Controller
{
    use AuthorizesRequests;

    public function index(School $school)
    {
        $this->authorize('view', [TeacherScheduleEntry::class, $school]);

        $timeSlots = TimeSlot::where('school_id', $school->id)
            ->orderBy('order')
            ->get();

        $scheduleEntries = TeacherScheduleEntry::where('school_id', $school->id)
            ->where('user_id', auth()->id())
            ->get();

        return view('schools.schedule', compact('school', 'timeSlots', 'scheduleEntries'));
    }

    public function storeTimeSlots(School $school, Request $request)
    {
        $validated = $request->validate([
            'slots' => 'required|array',
            'slots.*.name' => 'required|string',
            'slots.*.start_time' => 'required|date_format:H:i',
            'slots.*.end_time' => 'required|date_format:H:i',
            'slots.*.is_break' => 'nullable|string'
        ]);

        DB::transaction(function () use ($school, $validated) {
            // Obtener las franjas horarias actuales
            $currentTimeSlots = TimeSlot::where('school_id', $school->id)->get();
            $currentSlotIds = $currentTimeSlots->pluck('id');

            // Crear o actualizar franjas horarias
            $newSlotIds = [];
            foreach ($validated['slots'] as $index => $slotData) {
                // Intentar encontrar una franja horaria existente con el mismo orden
                $existingSlot = $currentTimeSlots->where('order', $index)->first();

                if ($existingSlot) {
                    // Actualizar franja existente
                    $existingSlot->update([
                        'name' => $slotData['name'],
                        'start_time' => $slotData['start_time'],
                        'end_time' => $slotData['end_time'],
                        'is_break' => $slotData['is_break'] === 'true',
                        'order' => $index
                    ]);
                    $newSlotIds[] = $existingSlot->id;
                } else {
                    // Crear nueva franja
                    $newSlot = TimeSlot::create([
                        'school_id' => $school->id,
                        'name' => $slotData['name'],
                        'start_time' => $slotData['start_time'],
                        'end_time' => $slotData['end_time'],
                        'is_break' => $slotData['is_break'] === 'true',
                        'order' => $index
                    ]);
                    $newSlotIds[] = $newSlot->id;
                }
            }

            // Identificar y eliminar las franjas que ya no existen
            $deletedSlotIds = $currentSlotIds->diff($newSlotIds);
            if ($deletedSlotIds->isNotEmpty()) {
                ScheduleEntry::whereIn('time_slot_id', $deletedSlotIds)->delete();
                TimeSlot::whereIn('id', $deletedSlotIds)->delete();
            }
        });

        return back()->with('success', 'Franjas horarias actualizadas correctamente');
    }

    public function updateSchedule(School $school, Request $request)
    {
        $this->authorize('manage', [TeacherScheduleEntry::class, $school]);

        $entries = $request->input('entries', []);

        DB::transaction(function () use ($school, $entries) {
            TeacherScheduleEntry::where('school_id', $school->id)
                ->where('user_id', auth()->id())
                ->delete();

            foreach ($entries as $timeSlotId => $days) {
                foreach ($days as $day => $data) {
                    if (!empty($data['subject'])) {
                        TeacherScheduleEntry::create([
                            'school_id' => $school->id,
                            'user_id' => auth()->id(),
                            'time_slot_id' => $timeSlotId,
                            'day' => $day,
                            'subject' => $data['subject']
                        ]);
                    }
                }
            }
        });

        return back()->with('success', 'Horario actualizado correctamente');
    }

    public function downloadPdf(School $school)
    {
        $this->authorize('view', [TeacherScheduleEntry::class, $school]);

        $timeSlots = TimeSlot::where('school_id', $school->id)
            ->orderBy('order')
            ->get();

        $scheduleEntries = TeacherScheduleEntry::where('school_id', $school->id)
            ->where('user_id', auth()->id())
            ->get();

        $user = auth()->user();

        $pdf = Pdf::loadView('pdf.schedule', compact('school', 'timeSlots', 'scheduleEntries', 'user'));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("horario-profesor.pdf");
    }
}
