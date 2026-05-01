<div
    x-data="window.commandCenterUi()"
    x-on:notify.window="pushToast($event.detail)"
    x-on:command-center-drop.window="$wire.handleDrop(Number($event.detail.entryId), $event.detail.targetType, Number($event.detail.targetId))"
    x-on:command-center-hydrate-dnd.window="$nextTick(() => window.initializeCommandCenterDnD())"
    class="font-sans antialiased -mx-4 sm:-mx-6 lg:-mx-10 -my-8 min-h-[calc(100dvh-1px)]"
>
    {{-- Full-bleed dark shell — negates page-shell padding so the CC owns the entire viewport --}}
    <div class="relative bg-[#060913] text-slate-300 px-4 py-6 sm:px-6 lg:px-8 min-h-[calc(100dvh-1px)]">

        {{-- Subtle ambient glow --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -left-40 h-[500px] w-[500px] rounded-full bg-teal-500/[0.07] blur-[120px]"></div>
            <div class="absolute -bottom-40 -right-40 h-[400px] w-[400px] rounded-full bg-coral-500/[0.05] blur-[120px]"></div>
        </div>

        {{-- Loading overlay --}}
        <div wire:loading.flex class="fixed inset-0 z-40 items-start justify-center bg-[#060913]/70 pt-8 backdrop-blur-sm">
            <div class="rounded-full border border-teal-500/30 bg-[#0A0F1C] px-5 py-2.5 text-sm font-semibold text-teal-400 shadow-[0_0_20px_rgba(45,212,191,0.2)]">
                Syncing live queue…
            </div>
        </div>

        {{-- Content --}}
        <div class="relative z-10 mx-auto max-w-[1440px] space-y-6">

            {{-- Header --}}
            <livewire:business.command-center.header
                :business-state="$businessState"
                :metrics="$metrics"
            />

            {{-- Main Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-10 gap-8 lg:gap-10">
                {{-- Left column — primary queue actions --}}
                <div class="lg:col-span-6 space-y-8">
                    <livewire:business.command-center.next-action
                        :business-state="$businessState"
                        :next-entry="$nextEntry"
                        :selected-service-point-id="$selectedServicePointId"
                        :service-points="$servicePoints"
                    />

                    <livewire:business.command-center.waiting-list
                        :business-state="$businessState"
                        :waiting-entries="$waitingEntries"
                        :hidden-count="$metrics['waiting_hidden_count']"
                    />
                </div>

                {{-- Right column — context panels --}}
                <div class="lg:col-span-4 space-y-5">
                    <livewire:business.command-center.service-points-panel
                        :business-state="$businessState"
                        :service-points="$servicePoints"
                        :selected-service-point-id="$selectedServicePointId"
                    />

                    <livewire:business.command-center.active-tickets
                        :active-entries="$activeEntries"
                    />
                </div>
            </div>


        </div>
    </div>

    {{-- Toast notifications --}}
    <div class="pointer-events-none fixed inset-x-0 top-4 z-50 flex flex-col items-center gap-3 px-4">
        <template x-for="toast in toasts" :key="toast.id">
            <div
                class="pointer-events-auto w-full max-w-md rounded-2xl border px-4 py-3 shadow-[0_24px_60px_-30px_rgba(0,0,0,0.6)] backdrop-blur-xl bg-[#0A0F1C]/95"
                :class="toast.type === 'error'
                    ? 'border-rose-500/30 text-rose-400'
                    : toast.type === 'info'
                        ? 'border-slate-500/30 text-slate-300'
                        : 'border-teal-500/30 text-teal-400'"
            >
                <p class="text-sm font-semibold" x-text="toast.message"></p>
            </div>
        </template>
    </div>

    <style>
        .queue-drag-ghost {
            opacity: 0.45;
            transform: rotate(1.5deg);
        }

        .queue-drag-chosen {
            cursor: grabbing;
            box-shadow: 0 28px 60px -36px rgba(15, 23, 42, 0.55);
        }

        .queue-dropzone--active {
            outline: 2px dashed rgba(20, 159, 124, 0.6);
            outline-offset: 4px;
        }
    </style>

    <div id="print-area">
        @foreach($waitingEntries as $entry)
            <x-queue.thermal-ticket :entry="$entry" :business-id="$businessId" />
        @endforeach
    </div>

    @script
        <script>
            window.commandCenterUi ??= function () {
                return {
                    toasts: [],
                    pushToast(detail) {
                        const toast = {
                            id: `${Date.now()}-${Math.random()}`,
                            type: detail.type ?? 'success',
                            message: detail.message ?? 'Queue updated.',
                        };

                        this.toasts.push(toast);

                        window.setTimeout(() => {
                            this.toasts = this.toasts.filter((item) => item.id !== toast.id);
                        }, 2600);
                    },
                };
            };

            $wire.on('print-ticket', (data) => {
                const entryId = data.entryId;
                const element = document.getElementById('thermal-ticket-' + entryId);
                
                if (element) {
                    element.classList.add('is-printing');
                    setTimeout(() => {
                        window.print();
                        element.classList.remove('is-printing');
                    }, 200);
                }
            });

            window.initializeCommandCenterDnD ??= function () {
                if (!window.Sortable) {
                    return;
                }

                const waitingList = document.querySelector('[data-queue-waiting-list]');
                const dropzones = document.querySelectorAll('[data-queue-dropzone]');

                if (!waitingList) {
                    return;
                }

                if (waitingList._queueSortable) {
                    waitingList._queueSortable.destroy();
                }

                waitingList._queueSortable = window.Sortable.create(waitingList, {
                    animation: 150,
                    sort: false,
                    draggable: '[data-queue-entry]',
                    group: {
                        name: 'queue-assignments',
                        pull: 'clone',
                        put: false,
                    },
                    ghostClass: 'queue-drag-ghost',
                    chosenClass: 'queue-drag-chosen',
                    onStart() {
                        document.querySelectorAll('[data-queue-dropzone][data-accepting="true"]').forEach((zone) => {
                            zone.classList.add('queue-dropzone--active');
                        });
                    },
                    onEnd() {
                        document.querySelectorAll('[data-queue-dropzone]').forEach((zone) => {
                            zone.classList.remove('queue-dropzone--active');
                        });
                    },
                });

                // Also make active tickets draggable for reassignment
                const activeList = document.querySelector('[data-queue-active-list]');
                if (activeList) {
                    if (activeList._queueSortable) {
                        activeList._queueSortable.destroy();
                    }

                    activeList._queueSortable = window.Sortable.create(activeList, {
                        animation: 150,
                        sort: false,
                        draggable: '[data-queue-entry]',
                        group: {
                            name: 'queue-assignments',
                            pull: 'clone',
                            put: false,
                        },
                        ghostClass: 'queue-drag-ghost',
                        chosenClass: 'queue-drag-chosen',
                        onStart() {
                            document.querySelectorAll('[data-queue-dropzone][data-accepting="true"]').forEach((zone) => {
                                zone.classList.add('queue-dropzone--active');
                            });
                        },
                        onEnd() {
                            document.querySelectorAll('[data-queue-dropzone]').forEach((zone) => {
                                zone.classList.remove('queue-dropzone--active');
                            });
                        },
                    });
                }

                dropzones.forEach((zone) => {
                    if (zone._queueSortable) {
                        zone._queueSortable.destroy();
                    }

                    zone._queueSortable = window.Sortable.create(zone, {
                        animation: 150,
                        sort: false,
                        draggable: '[data-queue-entry]',
                        group: {
                            name: 'queue-assignments',
                            pull: false,
                            put: true,
                        },
                        onMove() {
                            return zone.dataset.accepting === 'true';
                        },
                        onAdd(event) {
                            const item = event.item;

                            if (zone.dataset.accepting !== 'true') {
                                item.remove();
                                return;
                            }

                            window.dispatchEvent(new CustomEvent('command-center-drop', {
                                detail: {
                                    entryId: Number(item.dataset.entryId),
                                    targetId: Number(zone.dataset.targetId),
                                    targetType: zone.dataset.targetType,
                                },
                            }));

                            item.remove();
                        },
                    });
                });
            };

            queueMicrotask(() => window.initializeCommandCenterDnD());
        </script>
    @endscript
</div>
