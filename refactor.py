import os
import re

def replace_in_file(filepath, replacements):
    if not os.path.exists(filepath):
        print(f"File not found: {filepath}")
        return
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    new_content = content
    for old, new in replacements:
        new_content = new_content.replace(old, new)
        
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {filepath}")

# 1. Rename files
renames = [
    ('app/Models/Tenant/Counter.php', 'app/Models/Tenant/ServicePoint.php'),
    ('app/Livewire/Business/CounterManagement.php', 'app/Livewire/Business/ServicePointManagement.php'),
    ('resources/views/livewire/business/counter-management.blade.php', 'resources/views/livewire/business/service-point-management.blade.php'),
    ('app/Livewire/Business/CommandCenter/CountersPanel.php', 'app/Livewire/Business/CommandCenter/ServicePointsPanel.php'),
    ('resources/views/livewire/business/command-center/counters-panel.blade.php', 'resources/views/livewire/business/command-center/service-points-panel.blade.php')
]

for old, new in renames:
    if os.path.exists(old):
        os.rename(old, new)
        print(f"Renamed {old} to {new}")
        
if os.path.exists('app/Models/Tenant/ServiceTable.php'):
    os.remove('app/Models/Tenant/ServiceTable.php')
    print("Removed ServiceTable.php")

# 2. Text Replacements
replacements_map = {
    'app/Models/Tenant/ServicePoint.php': [
        ('class Counter extends Model', 'class ServicePoint extends Model'),
        ('Counter', 'ServicePoint')
    ],
    'app/Models/Tenant/Business.php': [
        ('counters()', 'servicePoints()'),
        ('Counter::class', 'ServicePoint::class'),
        ('tables()', 'servicePoints()'),
        ('ServiceTable::class', 'ServicePoint::class')
    ],
    'app/Models/Queue/QueueEntry.php': [
        ('counter()', 'servicePoint()'),
        ('Counter::class', 'ServicePoint::class'),
        ('table()', 'servicePoint()'),
        ('ServiceTable::class', 'ServicePoint::class'),
        ('counter_id', 'service_point_id'),
        ('table_id', 'service_point_id')
    ],
    'app/Services/Queue/QueueService.php': [
        ('counter_id', 'service_point_id'),
        ('table_id', 'service_point_id'),
        ('$entry->table?->name ?? $entry->counter?->name', '$entry->servicePoint?->name'),
        ('$entry->table ? \'table\' : ($entry->counter ? \'counter\' : null)', '$entry->servicePoint ? \'service_point\' : null'),
        ('Counter', 'ServicePoint'),
        ('$entry->counter', '$entry->servicePoint'),
        ('$entry->table', '$entry->servicePoint')
    ],
    'app/Livewire/Business/ServicePointManagement.php': [
        ('CounterManagement', 'ServicePointManagement'),
        ('Counter::', 'ServicePoint::'),
        ('Counter', 'ServicePoint'),
        ('counter', 'servicePoint'),
        ('counters', 'servicePoints'),
        ('business.counter-management', 'business.service-point-management')
    ],
    'app/Livewire/Business/CommandCenter/ServicePointsPanel.php': [
        ('CountersPanel', 'ServicePointsPanel'),
        ('Counter::', 'ServicePoint::'),
        ('Counter', 'ServicePoint'),
        ('counters', 'servicePoints'),
        ('business.command-center.counters-panel', 'business.command-center.service-points-panel')
    ],
    'app/Livewire/Business/CommandCenter/Index.php': [
        ('CountersPanel', 'ServicePointsPanel')
    ],
    'app/Livewire/Business/CommandCenter/NextAction.php': [
        ('counter_id', 'service_point_id'),
        ('table_id', 'service_point_id'),
        ('counters', 'servicePoints'),
        ('tables', 'servicePoints'),
        ('Counter::', 'ServicePoint::'),
        ('ServiceTable::', 'ServicePoint::'),
        ('Counter', 'ServicePoint'),
        ('Table', 'ServicePoint'),
        ('$this->counter', '$this->servicePoint')
    ],
    'app/Livewire/PublicQueue/TvDisplay.php': [
        ('counter_id', 'service_point_id')
    ],
    'app/Livewire/PublicQueue/TicketStatus.php': [
        ('counter_id', 'service_point_id')
    ],
    'resources/views/layouts/app/sidebar.blade.php': [
        ('business.counters', 'business.service-points'),
        ('Counters', 'Service Points')
    ],
    'routes/web.php': [
        ('CounterManagement', 'ServicePointManagement'),
        ('business.counters', 'business.service-points'),
        ('/counters', '/service-points')
    ],
    'resources/views/livewire/business/command-center/index.blade.php': [
        ('command-center.counters-panel', 'command-center.service-points-panel')
    ],
    'resources/views/livewire/business/service-point-management.blade.php': [
        ('Counters', 'Service Points'),
        ('Counter', 'Service Point'),
        ('counter', 'servicePoint'),
        ('counters', 'servicePoints')
    ],
    'resources/views/livewire/business/command-center/service-points-panel.blade.php': [
        ('Counters', 'Service Points'),
        ('Counter', 'Service Point'),
        ('counter', 'servicePoint'),
        ('counters', 'servicePoints')
    ],
    'resources/views/livewire/business/command-center/next-action.blade.php': [
        ('counter_id', 'service_point_id'),
        ('table_id', 'service_point_id'),
        ('Counters', 'Service Points'),
        ('Tables', 'Service Points'),
        ('counter', 'servicePoint'),
        ('table', 'servicePoint')
    ],
    'app/Events/QueueUpdated.php': [
        ('counter_id', 'service_point_id'),
        ('table_id', 'service_point_id'),
        ('counter', 'servicePoint'),
        ('table', 'servicePoint')
    ]
}

for filepath, replacements in replacements_map.items():
    replace_in_file(filepath, replacements)
