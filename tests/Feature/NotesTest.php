<?php

use App\Models\Note;
use App\Models\User;

test('guests are redirected away from notes', function () {
    $this->get(route('notes.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users can create notes', function () {
    $usuario = User::factory()->create();

    $this->actingAs($usuario)
        ->post(route('notes.store'), [
            'title' => 'Idea de producto',
            'content' => 'Convertir tareas y notas en un dashboard de productividad.',
        ])
        ->assertRedirect();

    $nota = Note::first();

    expect($nota)
        ->not->toBeNull()
        ->and($nota->user_id)->toBe($usuario->id)
        ->and($nota->title)->toBe('Idea de producto');
});

test('users cannot delete notes that do not belong to them', function () {
    $propietario = User::factory()->create();
    $intruso = User::factory()->create();
    $nota = $propietario->notes()->create([
        'title' => 'Privada',
        'content' => 'Solo el propietario debe borrarla.',
    ]);

    $this->actingAs($intruso)
        ->delete(route('notes.destroy', $nota))
        ->assertNotFound();
});
