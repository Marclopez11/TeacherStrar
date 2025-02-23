<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Group;
use App\Models\TimeSlot;
use App\Models\ScheduleEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SchoolScheduleController extends Controller
{
    public function index(School $school, Request $request)
    {
        $selectedGroup = null;
        $timeSlots = TimeSlot::where('school_id', $school->id)
            ->orderBy('order')
            ->get();
        $scheduleEntries = collect();

        if ($request->has('group')) {
            $selectedGroup = $school->groups()->findOrFail($request->group);
            $scheduleEntries = ScheduleEntry::where('school_id', $school->id)
                ->where('group_id', $selectedGroup->id)
                ->get();
        }

        return view('schools.schedule', compact('school', 'selectedGroup', 'timeSlots', 'scheduleEntries'));
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

    public function updateSchedule(School $school, Group $group, Request $request)
    {
        $entries = $request->input('entries', []);

        DB::transaction(function () use ($school, $group, $entries) {
            // Eliminar solo las entradas existentes para las franjas horarias que se están actualizando
            $timeSlotIds = array_keys($entries);
            if (!empty($timeSlotIds)) {
                ScheduleEntry::where('school_id', $school->id)
                    ->where('group_id', $group->id)
                    ->whereIn('time_slot_id', $timeSlotIds)
                    ->delete();
            }

            // Crear nuevas entradas
            foreach ($entries as $timeSlotId => $days) {
                foreach ($days as $day => $data) {
                    if (!empty($data['subject'])) {
                        ScheduleEntry::create([
                            'school_id' => $school->id,
                            'group_id' => $group->id,
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

    public function downloadPdf(School $school, Group $group)
    {
        $timeSlots = TimeSlot::where('school_id', $school->id)
            ->orderBy('order')
            ->get();

        $scheduleEntries = ScheduleEntry::where('school_id', $school->id)
            ->where('group_id', $group->id)
            ->get();

        $pdf = Pdf::loadView('pdf.schedule', compact('school', 'group', 'timeSlots', 'scheduleEntries'));

        // Configurar el PDF en horizontal
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("horario-{$group->name}.pdf");
    }
}
