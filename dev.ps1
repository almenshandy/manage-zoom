Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "    PILIH PERINTAH LARAVEL / NPM          " -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "1) php artisan serve --port=8030 --host=0.0.0.0"
Write-Host "2) npm run build"
Write-Host "3) npm run dev"
Write-Host "4) php artisan key:generate"
Write-Host "5) php artisan migrate"
Write-Host "6) php artisan migrate:fresh --seed"
Write-Host "7) composer install"
Write-Host "8) npm install"
Write-Host "9) php artisan optimize:clear"
Write-Host "10) Keluar"
Write-Host "==========================================" -ForegroundColor Cyan

$choice = Read-Host "Masukkan pilihan Anda [1-9]"

switch ($choice) {
    "1" { 
        Start-Process "http://127.0.0.1:8030"
        php artisan serve --port=8030 --host=0.0.0.0 
    }
    "2" { npm run build }
    "3" { npm run dev }
    "4" { php artisan key:generate }
    "5" { php artisan migrate }
    "6" { php artisan migrate:fresh --seed }
    "7" { composer install }
    "8" { npm install }
    "9" { php artisan optimize:clear }
    "10" { Write-Host "Batal." -ForegroundColor Yellow; exit }
    default { Write-Host "Pilihan tidak valid!" -ForegroundColor Red }
}
