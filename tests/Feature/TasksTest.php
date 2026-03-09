<?php

use App\Models\Task;
use App\Models\User;

test('guests are redirected away from tasks', function () {
    $this->get(route('tasks.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users can create and complete their own tasks', function () {
    $usuario = User::factory()->create();

    $this->actingAs($usuario)
        ->post(route('tasks.store'), [
            'title' => 'Terminar la primera funcionalidad',
            'description' => 'Agregar validaciones y completar la interfaz.',
            'priority' => 'alta',
            'due_date' => now()->addDay()->toDateString(),
        ])
        ->assertRedirect();

    $tarea = Task::first();

    expect($tarea)
        ->not->toBeNull()
        ->and($tarea->user_id)->toBe($usuario->id)
        ->and($tarea->is_completed)->toBeFalse();

    $this->patch(route('tasks.update', $tarea), [
        'title' => $tarea->title,
        'description' => $tarea->description,
        'priority' => $tarea->priority,
        'due_date' => optional($tarea->due_date)->toDateString(),
        'is_completed' => true,
    ])->assertRedirect();

    expect($tarea->fresh())
        ->is_completed->toBeTrue()
        ->completed_at->not->toBeNull();
});

test('users cannot modify tasks that do not belong to them', function () {
    $propietario = User::factory()->create();
    $intruso = User::factory()->create();
    $tarea = $propietario->tasks()->create([
        'title' => 'Tarea privada',
        'description' => 'No deberia poder cambiarse.',
        'priority' => 'media',
    ]);

    $this->actingAs($intruso)
        ->patch(route('tasks.update', $tarea), [
            'title' => 'Tarea robada',
            'description' => 'Cambio no permitido',
            'priority' => 'alta',
            'due_date' => now()->toDateString(),
            'is_completed' => true,
        ])
        ->assertNotFound();
});
