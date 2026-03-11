import sys

file_path = r'c:\xampp\htdocs\vestalize\routes\api.php'
with open(file_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Laravel line numbers are 1-indexed. lines[178] is line 179.
# We want to remove lines 179 to 269 inclusive.
# That is indices 178 to 268.
new_lines = lines[:178] + lines[269:]

with open(file_path, 'w', encoding='utf-8') as f:
    f.writelines(new_lines)

print("Successfully removed lines 179-269 from api.php")
