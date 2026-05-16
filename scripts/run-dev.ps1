<#
.SYNOPSIS
  Web-2 Project - Auto Start with Health Check
  
.DESCRIPTION
  Checks project setup (PHP, MySQL, files) and starts PHP dev server.
  
.EXAMPLE
  .\scripts\run-dev.ps1
  .\scripts\run-dev.ps1 -Port 3000
  .\scripts\run-dev.ps1 -NoOpenBrowser
#>
param(
    [int]$Port = 8000,
    [string]$Host = 'localhost',
    [switch]$NoOpenBrowser,
    [switch]$SkipHealthCheck
)

$ErrorActionPreference = 'Stop'
$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Definition
$repoRoot = Resolve-Path (Join-Path $scriptDir '..') | Select-Object -ExpandProperty Path
$frontendDir = Join-Path $repoRoot 'frontend'

Write-Host "====== Web-2 Project Auto Start ======" -ForegroundColor Cyan
Write-Host ""

# Find PHP executable
Write-Host "[1/3] Checking for PHP..." -ForegroundColor Cyan
$phpExe = $null
try {
    $phpExe = (Get-Command php -ErrorAction SilentlyContinue).Source
} catch {}

if (-not $phpExe) {
    $candidates = @(
        'C:\xampp\php\php.exe',
        'C:\Program Files\php\php.exe',
        'C:\Program Files (x86)\php\php.exe'
    )
    foreach ($c in $candidates) {
        if (Test-Path $c) { $phpExe = $c; break }
    }
}

if (-not $phpExe) {
    Write-Host "ERROR: PHP not found in PATH or common locations" -ForegroundColor Red
    Write-Host "Please install PHP and add it to PATH, or install XAMPP" -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit 1
}
Write-Host "  Found: $phpExe" -ForegroundColor Green

# Run health check
if (-not $SkipHealthCheck) {
    Write-Host "[2/3] Running health check..." -ForegroundColor Cyan
    $healthScript = Join-Path $repoRoot 'scripts\health-check.php'
    & $phpExe -f $healthScript
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Health check failed. Please fix the issues above." -ForegroundColor Red
        Read-Host "Press Enter to exit"
        exit 1
    }
} else {
    Write-Host "[2/3] Skipping health check" -ForegroundColor Yellow
}

# Start server
Write-Host "[3/3] Starting PHP server on http://$Host`:$Port" -ForegroundColor Cyan
Write-Host "  Press Ctrl+C to stop the server" -ForegroundColor Gray
Write-Host ""

if (-not $NoOpenBrowser) {
    Write-Host "Opening browser..." -ForegroundColor Green
    Start-Process "http://$Host`:$Port/pages/index.php"
    Start-Sleep -Seconds 1
}

& $phpExe -S "$Host`:$Port" -t $frontendDir
