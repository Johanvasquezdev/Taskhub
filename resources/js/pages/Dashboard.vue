<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '../routes/index';
import type { BreadcrumbItem } from '@/types';

defineProps<{
    stats: {
        total: number;
        completed: number;
        remaining: number;
        notes: number;
    };
    latestTasks: Array<{
        id: number;
        title: string;
        priority: string;
        due_date: string | null;
        is_completed: boolean;
    }>;
    latestNotes: Array<{
        id: number;
        title: string;
        content: string;
    }>;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="grid gap-4 xl:grid-cols-4">
                <Card>
                    <CardHeader class="gap-2">
                        <CardDescription>Total de tareas</CardDescription>
                        <CardTitle class="text-3xl">{{ stats.total }}</CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader class="gap-2">
                        <CardDescription>Completadas</CardDescription>
                        <CardTitle class="text-3xl">{{ stats.completed }}</CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader class="gap-2">
                        <CardDescription>Tareas pendientes</CardDescription>
                        <CardTitle class="text-3xl">{{ stats.remaining }}</CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader class="gap-2">
                        <CardDescription>Notas guardadas</CardDescription>
                        <CardTitle class="text-3xl">{{ stats.notes }}</CardTitle>
                    </CardHeader>
                </Card>
            </div>

            <div class="grid gap-4 xl:grid-cols-[1.4fr_1fr]">
            <Card class="border-sidebar-border/70 dark:border-sidebar-border">
                <CardHeader class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <CardTitle>Tareas recientes</CardTitle>
                        <CardDescription>
                            Una vista rapida de lo que necesita atencion ahora.
                        </CardDescription>
                    </div>
                    <Button as-child>
                        <Link href="/tasks">Abrir tablero de tareas</Link>
                    </Button>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div
                        v-if="latestTasks.length === 0"
                        class="rounded-lg border border-dashed p-6 text-sm text-muted-foreground"
                    >
                        Aun no hay tareas. Crea la primera desde el tablero.
                    </div>
                    <div
                        v-for="task in latestTasks"
                        :key="task.id"
                        class="flex items-center justify-between rounded-lg border px-4 py-3"
                    >
                        <div class="space-y-1">
                            <span class="block font-medium">{{ task.title }}</span>
                            <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                <span class="capitalize">{{ task.priority }}</span>
                                <span v-if="task.due_date">Entrega {{ task.due_date }}</span>
                            </div>
                        </div>
                        <Badge :variant="task.is_completed ? 'secondary' : 'outline'">{{ task.is_completed ? 'Hecha' : 'Abierta' }}</Badge>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <CardTitle>Notas recientes</CardTitle>
                        <CardDescription>
                            Ideas y recordatorios para seguir avanzando.
                        </CardDescription>
                    </div>
                    <Button as-child variant="outline">
                        <Link href="/notes">Abrir notas</Link>
                    </Button>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div
                        v-if="latestNotes.length === 0"
                        class="rounded-lg border border-dashed p-6 text-sm text-muted-foreground"
                    >
                        Aun no hay notas. Crea la primera desde el modulo de notas.
                    </div>
                    <div
                        v-for="nota in latestNotes"
                        :key="nota.id"
                        class="rounded-lg border px-4 py-3"
                    >
                        <p class="font-medium">{{ nota.title }}</p>
                        <p class="mt-1 line-clamp-3 text-sm text-muted-foreground">
                            {{ nota.content }}
                        </p>
                    </div>
                </CardContent>
            </Card>
            </div>
        </div>
    </AppLayout>
</template>
