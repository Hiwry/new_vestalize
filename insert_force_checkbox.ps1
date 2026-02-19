$file = "c:\xampp\htdocs\vestalize.10 (1)\vestalize.10\resources\views\stock-requests\index.blade.php"
$lines = [System.IO.File]::ReadAllLines($file)
$newLines = New-Object System.Collections.Generic.List[string]
$newLines.AddRange($lines)

# Content to insert
$forceHtml = @"
            <div class="mb-4">
                <div class="flex items-center gap-2 mb-1">
                    <input type="checkbox" id="approve-force" name="force" value="1" class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                    <label for="approve-force" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Forçar aprovação (permitir estoque negativo)
                    </label>
                </div>
            </div>
"@

$forceGroupHtml = @"
            <div class="mb-4">
                <div class="flex items-center gap-2 mb-1">
                    <input type="checkbox" id="approve-group-force" name="force" value="1" class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                    <label for="approve-group-force" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Forçar aprovação (permitir estoque negativo)
                    </label>
                </div>
            </div>
"@

# Insert from bottom up to preserve indices
# Line 1904 (index 1903)
# Note: Check if file length is sufficient
if ($newLines.Count -gt 1903) {
    $newLines.Insert(1903, $forceGroupHtml)
} else {
    Write-Host "Error: File too short for index 1903"
}

# Line 1485 (index 1484)
# Note: If inserted above, indices below shift? No, inserted > 1484, so 1484 is safe.
if ($newLines.Count -gt 1484) {
    $newLines.Insert(1484, $forceHtml)
} else {
    Write-Host "Error: File too short for index 1484"
}

[System.IO.File]::WriteAllLines($file, $newLines, [System.Text.Encoding]::UTF8)
Write-Host "Lines inserted successfully using .NET"
