import os

filepath = 'tests/Feature/QueueServiceTest.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

replacements = [
    ('Counter::class', 'ServicePoint::class'),
    ('ServiceTable::class', 'ServicePoint::class'),
    ('counter()', 'servicePoint()'),
    ('table()', 'servicePoint()'),
    ('counter_id', 'service_point_id'),
    ('table_id', 'service_point_id'),
    ('Counter', 'ServicePoint'),
    ('ServiceTable', 'ServicePoint'),
    ('counters', 'servicePoints'),
    ('tables', 'servicePoints'),
    ('table', 'servicePoint'),
    ('counter', 'servicePoint')
]

for old, new in replacements:
    content = content.replace(old, new)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated tests")
