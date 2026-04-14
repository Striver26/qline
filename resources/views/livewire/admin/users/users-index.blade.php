<div class="space-y-6">
    <div class="page-header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center w-full gap-4">
            <div>
                <span class="page-kicker">User Management</span>
                <h1 class="page-title mt-4">All Registered Users</h1>
            </div>
            
            <flux:modal.trigger name="invite-staff">
                <flux:button variant="primary" icon="plus">Invite Staff</flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    @if(session('status'))
        <div class="p-4 mb-4 text-sm text-brand-800 rounded-xl bg-brand-50 border border-brand-200">
            {{ session('status') }}
        </div>
    @endif

    <div class="toolbar-card flex flex-col sm:flex-row gap-4">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search users by name or email..." class="flex-1 max-w-sm" />
        <flux:select wire:model.live="filterRole" class="w-full sm:w-48">
            <option value="">All Roles</option>
            <option value="user">Standard User</option>
            <option value="business_staff">Business Staff</option>
            <option value="business_owner">Business Owner</option>
            <option value="platform_staff">Platform Staff</option>
            <option value="superadmin">Super Admin</option>
        </flux:select>
    </div>

    <div class="glass-card !p-0 overflow-hidden">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column>Role</flux:table.column>
                <flux:table.column>Joined</flux:table.column>
                <flux:table.column align="end">Actions</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse($users as $user)
                    <flux:table.row>
                        <flux:table.cell class="font-semibold text-slate-800 dark:text-slate-100">{{$user->name}}</flux:table.cell>
                        <flux:table.cell>{{$user->email}}</flux:table.cell>
                        <flux:table.cell>
                            <span class="badge-pill badge-pill--brand">{{$user->role}}</span>
                        </flux:table.cell>
                        <flux:table.cell>{{$user->created_at->format('M d, Y')}}</flux:table.cell>
                        <flux:table.cell>
                            <flux:dropdown align="end" position="bottom">
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="bottom top" />
                                <flux:menu>
                                    <flux:menu.item wire:click="editUser({{$user->id}})" icon="pencil">Edit Role</flux:menu.item>
                                    <flux:menu.item wire:click="confirmDelete({{$user->id}})" variant="danger" icon="trash">Delete User</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row><flux:table.cell colspan="4" class="text-center py-8 text-slate-500">No users found.</flux:table.cell></flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
        <div class="border-t border-slate-200/70 dark:border-white/10 px-6 py-4">{{$users->links()}}</div>
    </div>

    <flux:modal name="invite-staff" class="min-w-[22rem]">
        <form wire:submit="inviteStaff">
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">Invite Platform Staff</h2>
            <p class="text-sm text-slate-500 mb-4">This creates a platform staff account instantly and yields a password.</p>
            
            <div class="mb-4">
                <flux:input label="Email Address" wire:model="inviteEmail" required />
            </div>
            
            <div class="mb-4">
                <flux:select label="Role" wire:model="inviteRole" required>
                    <option value="platform_staff">Platform Staff</option>
                    <option value="superadmin">Super Admin</option>
                </flux:select>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Create Staff</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="edit-user" class="min-w-[22rem]">
        <form wire:submit="updateUser">
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">Edit Role</h2>
            
            <div class="mb-4">
                <flux:select label="System Role" wire:model="editRole" required>
                    <option value="user">Standard User</option>
                    <option value="business_staff">Business Staff</option>
                    <option value="business_owner">Business Owner</option>
                    <option value="platform_staff">Platform Staff</option>
                    <option value="superadmin">Super Admin</option>
                </flux:select>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Save Changes</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="delete-user" class="min-w-[22rem]">
        <form wire:submit="deleteUser">
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-2">Delete User?</h2>
            <p class="text-sm text-slate-500 mb-6">This action is permanent and destroys their access completely.</p>
            
            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger">Yes, Delete</flux:button>
            </div>
        </form>
    </flux:modal>
</div>