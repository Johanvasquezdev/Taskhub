<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(): Response
    {
        $filtro = request()->string('estado')->toString();

        $tareas = request()->user()
            ->tasks()
            ->when($filtro === 'pendientes', fn ($consulta) => $consulta->where('is_completed', false))
            ->when($filtro === 'completadas', fn ($consulta) => $consulta->where('is_completed', true))
            ->latest()
            ->get()
            ->map(fn (Task $tarea) => [
                'id' => $tarea->id,
                'title' => $tarea->title,
                'description' => $tarea->description,
                'priority' => $tarea->priority,
                'due_date' => $tarea->due_date?->toDateString(),
                'is_completed' => $tarea->is_completed,
                'completed_at' => $tarea->completed_at?->toIso8601String(),
                'created_at' => $tarea->created_at?->toIso8601String(),
            ]);

        return Inertia::render('Tasks/Index', [
            'tasks' => $tareas,
            'stats' => [
                'total' => $tareas->count(),
                'completed' => $tareas->where('is_completed', true)->count(),
                'remaining' => $tareas->where('is_completed', false)->count(),
            ],
            'filtro' => $filtro ?: 'todas',
        ]);
    }

    public function store(StoreTaskRequest $solicitud): RedirectResponse
    {
        $solicitud->user()->tasks()->create($solicitud->validated());

        return back()->with('success', 'Tarea agregada.');
    }

    public function update(UpdateTaskRequest $solicitud, Task $tarea): RedirectResponse
    {
        abort_unless($tarea->user_id === $solicitud->user()->id, 404);

        $tarea->update([
            'title' => $solicitud->validated('title'),
            'description' => $solicitud->validated('description'),
            'priority' => $solicitud->validated('priority'),
            'due_date' => $solicitud->validated('due_date'),
            'is_completed' => $solicitud->boolean('is_completed'),
            'completed_at' => $solicitud->boolean('is_completed') ? now() : null,
        ]);

        return back()->with('success', 'Tarea actualizada.');
    }

    public function destroy(Task $tarea): RedirectResponse
    {
        abort_unless($tarea->user_id === request()->user()->id, 404);

        $tarea->delete();

        return back()->with('success', 'Tarea eliminada.');
    }
}
