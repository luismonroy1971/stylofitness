# Script para limpiar espacios en blanco excesivos
$inputFile = "c:\trabajos\stylofitness\app\Views\home\index.php"
$outputFile = "c:\trabajos\stylofitness\app\Views\home\index_clean.php"

# Leer todas las líneas
$lines = Get-Content $inputFile

# Procesar líneas para eliminar múltiples líneas vacías consecutivas
$cleanedLines = @()
$emptyLineCount = 0

foreach ($line in $lines) {
    if ($line -match '^\s*$') {
        $emptyLineCount++
        # Solo permitir máximo 1 línea vacía consecutiva
        if ($emptyLineCount -le 1) {
            $cleanedLines += $line
        }
    } else {
        $emptyLineCount = 0
        $cleanedLines += $line
    }
}

# Guardar el archivo limpio
$cleanedLines | Set-Content $outputFile -Encoding UTF8

Write-Host "Archivo limpiado guardado como: $outputFile"
Write-Host "Líneas originales: $($lines.Count)"
Write-Host "Líneas después de limpiar: $($cleanedLines.Count)"
Write-Host "Líneas eliminadas: $($lines.Count - $cleanedLines.Count)"