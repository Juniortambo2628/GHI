# Start Laravel and Vite dev servers
# Usage: .\dev.ps1 [artisan-port] [vite-port]

param(
    [int]$ArtisanPort = 8000,
    [int]$VitePort = 3000
)

$projectDir = $PSScriptRoot

Write-Host "Starting dev servers..." -ForegroundColor Cyan
Write-Host "  Laravel: http://localhost:$ArtisanPort" -ForegroundColor Green
Write-Host "  Vite:    http://localhost:$VitePort" -ForegroundColor Green
Write-Host "  Press Ctrl+C to stop" -ForegroundColor DarkGray
Write-Host ""

# Start Laravel server in background
$laravelProcess = Start-Process -FilePath "php" -ArgumentList "artisan", "serve", "--port=$ArtisanPort" -PassThru -WindowStyle Minimized -WorkingDirectory $projectDir

# Start Vite dev server
try {
    npm run dev -- --port $VitePort
} finally {
    # Kill Laravel server when Vite stops
    if (!$laravelProcess.HasExited) {
        Stop-Process -Id $laravelProcess.Id -Force -ErrorAction SilentlyContinue
        Write-Host "Laravel server stopped" -ForegroundColor DarkGray
    }
}
