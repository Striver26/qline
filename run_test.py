import subprocess
with open("test_results.txt", "w", encoding="utf-8") as f:
    result = subprocess.run(["php", "artisan", "test", "--filter=QueueServiceTest"], capture_output=True, text=True)
    f.write(result.stdout)
    f.write(result.stderr)
