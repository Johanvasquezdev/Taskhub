<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class NoteController extends Controller
{
    public function index(): Response
    {
        $notas = request()->user()
            ->notes()
            ->latest()
            ->get()
            ->map(fn (Note $nota) => [
                'id' => $nota->id,
                'title' => $nota->title,
                'content' => $nota->content,
                'created_at' => $nota->created_at?->toIso8601String(),
            ]);

        return Inertia::render('Notes/Index', [
            'notes' => $notas,
            'stats' => [
                'total' => $notas->count(),
            ],
        ]);
    }

    public function store(StoreNoteRequest $solicitud): RedirectResponse
    {
        $solicitud->user()->notes()->create($solicitud->validated());

        return back()->with('success', 'Nota agregada.');
    }

    public function destroy(Note $nota): RedirectResponse
    {
        abort_unless($nota->user_id === request()->user()->id, 404);

        $nota->delete();

        return back()->with('success', 'Nota eliminada.');
    }
}
