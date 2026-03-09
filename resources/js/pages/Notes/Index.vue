<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Nota = {
    id: number;
    title: string;
    content: string;
    created_at: string | null;
};

const propiedades = defineProps<{
    notes: Nota[];
    stats: {
        total: number;
    };
}>();

const pagina = usePage();
const formularioCrear = useForm({
    title: '',
    content: '',
});

const mensajeExito = computed(() => pagina.props.flash?.success as string | undefined);

const migasDePan: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Notas',
        href: '/notes',
    },
];

const enviar = () => {
    formularioCrear.post('/notes', {
        preserveScroll: true,
        onSuccess: () => formularioCrear.reset(),
    });
};

const eliminarNota = (idNota: number) => {
    useForm({}).delete(`/notes/${idNota}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Notas" />

    <AppLayout :breadcrumbs="migasDePan">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="grid gap-4 md:grid-cols-[1fr_2fr]">
                <Card>
                    <CardHeader class="gap-2">
                        <CardDescription>Total de notas</CardDescription>
                        <CardTitle class="text-3xl">{{ propiedades.stats.total }}</CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader>
                        <CardTitle>Espacio para ideas</CardTitle>
                        <CardDescription>
                            Guarda decisiones, pendientes rapidos o contexto de trabajo.
                        </CardDescription>
                    </CardHeader>
                </Card>
            </div>

            <Card>
                <CardHeader class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <CardTitle>Nueva nota</CardTitle>
                        <CardDescription>
                            Captura conocimiento del proyecto sin sacarlo de TaskHub.
                        </CardDescription>
                    </div>
                    <Button as-child variant="outline">
                        <Link href="/tasks">Ir a tareas</Link>
                    </Button>
                </CardHeader>
                <CardContent class="space-y-4">
                    <form class="space-y-4" @submit.prevent="enviar">
                        <div class="space-y-2">
                            <Label for="note-title">Titulo</Label>
                            <Input
                                id="note-title"
                                v-model="formularioCrear.title"
                                placeholder="Idea para el proximo sprint"
                            />
                            <InputError :message="formularioCrear.errors.title" />
                        </div>
                        <div class="space-y-2">
                            <Label for="note-content">Contenido</Label>
                            <textarea
                                id="note-content"
                                v-model="formularioCrear.content"
                                class="border-input bg-background min-h-32 w-full rounded-md border px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]"
                                placeholder="Escribe aqui la nota completa..."
                            />
                            <InputError :message="formularioCrear.errors.content" />
                        </div>
                        <Button type="submit" :disabled="formularioCrear.processing">
                            Guardar nota
                        </Button>
                    </form>

                    <div
                        v-if="mensajeExito"
                        class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
                    >
                        {{ mensajeExito }}
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Notas recientes</CardTitle>
                    <CardDescription>
                        Un historial rapido para revisar ideas y decisiones.
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div
                        v-if="propiedades.notes.length === 0"
                        class="rounded-lg border border-dashed p-6 text-sm text-muted-foreground"
                    >
                        Aun no hay notas. Crea la primera desde el formulario.
                    </div>
                    <div
                        v-for="nota in propiedades.notes"
                        :key="nota.id"
                        class="rounded-xl border p-4"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="space-y-2">
                                <p class="font-medium">{{ nota.title }}</p>
                                <p class="max-w-3xl text-sm text-muted-foreground">
                                    {{ nota.content }}
                                </p>
                            </div>
                            <Button variant="ghost" @click="eliminarNota(nota.id)">
                                Eliminar
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
