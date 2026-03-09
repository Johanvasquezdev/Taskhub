<?php

use App\Http\Controllers\NoteController;
use App\Http\Controllers\TaskController;
use App\Models\Note;
use App\Models\Task;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $tareas = request()->user()
            ->tasks()
            ->latest()
            ->get(['id', 'title', 'priority', 'due_date', 'is_completed', 'completed_at']);
        $notas = request()->user()
            ->notes()
            ->latest()
            ->get(['id', 'title', 'content']);

        return Inertia::render('Dashboard', [
            'stats' => [
                'total' => $tareas->count(),
                'completed' => $tareas->where('is_completed', true)->count(),
                'remaining' => $tareas->where('is_completed', false)->count(),
                'notes' => $notas->count(),
            ],
            'latestTasks' => $tareas
                ->take(5)
                ->map(fn (Task $tarea) => [
                    'id' => $tarea->id,
                    'title' => $tarea->title,
                    'priority' => $tarea->priority,
                    'due_date' => $tarea->due_date?->toDateString(),
                    'is_completed' => $tarea->is_completed,
                ])
                ->values(),
            'latestNotes' => $notas
                ->take(3)
                ->map(fn (Note $nota) => [
                    'id' => $nota->id,
                    'title' => $nota->title,
                    'content' => $nota->content,
                ])
                ->values(),
        ]);
    })->name('dashboard');

    Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('tasks/{tarea}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('tasks/{tarea}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    Route::get('notes', [NoteController::class, 'index'])->name('notes.index');
    Route::post('notes', [NoteController::class, 'store'])->name('notes.store');
    Route::delete('notes/{nota}', [NoteController::class, 'destroy'])->name('notes.destroy');
});

require __DIR__.'/settings.php';
