<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Tarea = {
    id: number;
    title: string;
    description: string | null;
    priority: 'baja' | 'media' | 'alta';
    due_date: string | null;
    is_completed: boolean;
    completed_at: string | null;
    created_at: string | null;
};

const propiedades = defineProps<{
    tasks: Tarea[];
    stats: {
        total: number;
        completed: number;
        remaining: number;
    };
    filtro: 'todas' | 'pendientes' | 'completadas';
}>();

const pagina = usePage();

const formularioCrear = useForm({
    title: '',
    description: '',
    priority: 'media',
    due_date: '',
});

const mensajeExito = computed(() => pagina.props.flash?.success as string | undefined);

const migasDePan: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Tareas',
        href: '/tasks',
    },
];

const filtros = [
    { etiqueta: 'Todas', valor: 'todas' },
    { etiqueta: 'Pendientes', valor: 'pendientes' },
    { etiqueta: 'Completadas', valor: 'completadas' },
] as const;

const enviar = () => {
    formularioCrear.post('/tasks', {
        preserveScroll: true,
        onSuccess: () => formularioCrear.reset('title', 'description', 'due_date'),
    });
};

const alternarTarea = (tarea: Tarea) => {
    useForm({
        title: tarea.title,
        description: tarea.description ?? '',
        priority: tarea.priority,
        due_date: tarea.due_date ?? '',
        is_completed: !tarea.is_completed,
    }).patch(`/tasks/${tarea.id}`, {
        preserveScroll: true,
    });
};

const eliminarTarea = (idTarea: number) => {
    useForm({}).delete(`/tasks/${idTarea}`, {
        preserveScroll: true,
    });
};

const cambiarFiltro = (valor: string) => {
    router.get(
        '/tasks',
        valor === 'todas' ? {} : { estado: valor },
        { preserveState: true, preserveScroll: true, replace: true },
    );
};

const clasePrioridad = (prioridad: Tarea['priority']) => {
    if (prioridad === 'alta') return 'destructive';
    if (prioridad === 'media') return 'default';

    return 'outline';
};
</script>

<template>
    <Head title="Tareas" />

    <AppLayout :breadcrumbs="migasDePan">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="grid gap-4 xl:grid-cols-[1.4fr_1fr_1fr]">
                <Card>
                    <CardHeader class="gap-2">
                        <CardDescription>Total de tareas</CardDescription>
                        <CardTitle class="text-3xl">{{ propiedades.stats.total }}</CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader class="gap-2">
                        <CardDescription>Completadas</CardDescription>
                        <CardTitle class="text-3xl">{{ propiedades.stats.completed }}</CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader class="gap-2">
                        <CardDescription>Pendientes</CardDescription>
                        <CardTitle class="text-3xl">{{ propiedades.stats.remaining }}</CardTitle>
                    </CardHeader>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <CardTitle>Modulo de tareas</CardTitle>
                            <CardDescription>
                                Organiza entregas, prioridad y seguimiento desde un solo lugar.
                            </CardDescription>
                        </div>
                        <Button as-child variant="outline">
                            <Link href="/notes">Ir a notas</Link>
                        </Button>
                    </div>
                </CardHeader>
                <CardContent class="space-y-4">
                    <form class="grid gap-4 xl:grid-cols-[1.4fr_1fr_1fr_auto]" @submit.prevent="enviar">
                        <div class="space-y-2 xl:col-span-2">
                            <Label for="title">Titulo</Label>
                            <Input
                                id="title"
                                v-model="formularioCrear.title"
                                placeholder="Preparar la vista de reportes"
                                :disabled="formularioCrear.processing"
                            />
                            <InputError :message="formularioCrear.errors.title" />
                        </div>
                        <div class="space-y-2">
                            <Label for="priority">Prioridad</Label>
                            <Select v-model="formularioCrear.priority">
                                <SelectTrigger id="priority" class="w-full">
                                    <SelectValue placeholder="Selecciona prioridad" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="baja">Baja</SelectItem>
                                    <SelectItem value="media">Media</SelectItem>
                                    <SelectItem value="alta">Alta</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="formularioCrear.errors.priority" />
                        </div>
                        <div class="space-y-2">
                            <Label for="due_date">Fecha limite</Label>
                            <Input
                                id="due_date"
                                v-model="formularioCrear.due_date"
                                type="date"
                                :disabled="formularioCrear.processing"
                            />
                            <InputError :message="formularioCrear.errors.due_date" />
                        </div>
                        <div class="space-y-2 xl:col-span-4">
                            <Label for="description">Descripcion</Label>
                            <textarea
                                id="description"
                                v-model="formularioCrear.description"
                                class="border-input bg-background min-h-24 w-full rounded-md border px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]"
                                placeholder="Anota detalles o el siguiente paso..."
                            />
                            <InputError :message="formularioCrear.errors.description" />
                        </div>
                        <div class="xl:col-span-4">
                            <Button type="submit" :disabled="formularioCrear.processing">
                                Agregar tarea
                            </Button>
                        </div>
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
                <CardHeader class="gap-4">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <CardTitle>Tareas</CardTitle>
                            <CardDescription>
                                Filtra por estado y mantente enfocado en lo que sigue.
                            </CardDescription>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <Button
                                v-for="opcion in filtros"
                                :key="opcion.valor"
                                :variant="propiedades.filtro === opcion.valor ? 'default' : 'outline'"
                                size="sm"
                                @click="cambiarFiltro(opcion.valor)"
                            >
                                {{ opcion.etiqueta }}
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div
                        v-if="propiedades.tasks.length === 0"
                        class="rounded-lg border border-dashed p-6 text-sm text-muted-foreground"
                    >
                        No hay tareas para este filtro. Crea una nueva o cambia la vista.
                    </div>

                    <div
                        v-for="tarea in propiedades.tasks"
                        :key="tarea.id"
                        class="flex flex-col gap-4 rounded-xl border p-4"
                    >
                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                            <div class="flex items-start gap-3">
                                <Checkbox
                                    :model-value="tarea.is_completed"
                                    @update:model-value="alternarTarea(tarea)"
                                />
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p
                                            class="font-medium"
                                            :class="{ 'text-muted-foreground line-through': tarea.is_completed }"
                                        >
                                            {{ tarea.title }}
                                        </p>
                                        <Badge :variant="clasePrioridad(tarea.priority)">
                                            {{ tarea.priority }}
                                        </Badge>
                                        <Badge :variant="tarea.is_completed ? 'secondary' : 'outline'">
                                            {{ tarea.is_completed ? 'Hecha' : 'Abierta' }}
                                        </Badge>
                                    </div>
                                    <p
                                        v-if="tarea.description"
                                        class="max-w-3xl text-sm text-muted-foreground"
                                    >
                                        {{ tarea.description }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                                        <span v-if="tarea.due_date">Fecha limite: {{ tarea.due_date }}</span>
                                        <span v-if="tarea.completed_at">Completada recientemente</span>
                                    </div>
                                </div>
                            </div>
                            <Button variant="ghost" @click="eliminarTarea(tarea.id)">
                                Eliminar
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
