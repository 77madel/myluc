@echo off
REM ============================================
REM Laravel Scheduler - MyLMS
REM Exécute les tâches planifiées
REM ============================================

cd C:\Users\madou\OneDrive\Desktop\ProjetLaravel\myluc
php artisan schedule:run >> storage\logs\scheduler.log 2>&1

REM Pour debug, décommentez la ligne suivante :
REM echo [%date% %time%] Scheduler executed >> storage\logs\scheduler-debug.log

