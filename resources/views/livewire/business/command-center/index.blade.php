<div
    x-data="window.commandCenterUi()"
    x-on:notify.window="pushToast($event.detail)"
    x-on:command-center-drop.window="$wire.handleDrop(Number($event.detail.entryId), $event.detail.targetType, Number($event.detail.targetId))"
    x-on:command-center-hydrate-dnd.window="$nextTick(() => window.initializeCommandCenterDnD())"
    class="space-y-8"
>
    <div wire:loading.flex class="fixed inset-0 z-40 items-start justify-center bg-slate-950/10 pt-6 backdrop-blur-[1px]">
        <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-lg">
            Syncing live queue...
        </div>
    </div>

    <livewire:business.command-center.header
        :business-state="$businessState"
        :metrics="$metrics"
    />

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.08fr)_minmax(340px,0.92fr)]">
        <div class="space-y-6">
            <livewire:business.command-center.next-action
                :business-state="$businessState"
                :next-entry="$nextEntry"
                :service-points="$servicePoints"
                :selected-service-point-id="$selectedServicePointId"
            />

            <livewire:business.command-center.waiting-list
                :business-state="$businessState"
                :waiting-entries="$waitingEntries"
                :hidden-count="$metrics['waiting_hidden_count']"
            />
        </div>

        <div class="space-y-6">
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

    <div class="pointer-events-none fixed inset-x-0 top-4 z-50 flex flex-col items-center gap-3 px-4">
        <template x-for="toast in toasts" :key="toast.id">
            <div
                class="pointer-events-auto w-full max-w-md rounded-2xl border bg-white/95 px-4 py-3 shadow-[0_24px_60px_-30px_rgba(15,23,42,0.35)] backdrop-blur"
                :class="toast.type === 'error'
                    ? 'border-rose-200 text-rose-700'
                    : toast.type === 'info'
                        ? 'border-slate-200 text-slate-700'
                        : 'border-emerald-200 text-emerald-700'"
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
