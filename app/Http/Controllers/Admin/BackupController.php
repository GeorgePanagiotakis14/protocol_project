<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    /**
     * POST admin/backup/run
     */
    public function run(Request $request)
    {
        // για να μη σε κόψει το web timeout
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');

        Artisan::call('backup:run', [
            '--disable-notifications' => true,
        ]);

        $output = Artisan::output();

        $zip = $this->findLatestBackupZipPath();

        if (!$zip) {
            return back()->with('error', "Δεν βρέθηκε backup zip. Output:\n" . $output);
        }

        return back()->with('success', '✅ Το backup δημιουργήθηκε: ' . basename($zip));
    }

    /**
     * GET admin/backup/download-latest
     */
    public function downloadLatest(): BinaryFileResponse
    {
        $zip = $this->findLatestBackupZipPath();

        if (!$zip) {
            abort(404, 'Δεν βρέθηκε backup zip. Έλεγξε config/backup.php και storage/logs/laravel.log');
        }

        return response()->download($zip, basename($zip));
    }

    /**
     * Βρίσκει το πιο πρόσφατο .zip του Spatie ΣΤΟ ΣΩΣΤΟ DISK.
     *
     * Το Spatie αποθηκεύει στο disk(s) που έχεις στο:
     * config('backup.backup.destination.disks') => π.χ. ['local']
     *
     * Και μέσα σε φάκελο:
     * <backup.name>/backup_....zip
     */
    private function findLatestBackupZipPath(): ?string
    {
        $backupName = config('backup.backup.name') ?: config('app.name', 'Laravel');

        $disks = config('backup.backup.destination.disks') ?: ['local'];
        if (!is_array($disks) || empty($disks)) {
            $disks = ['local'];
        }

        $allZipFiles = collect();

        foreach ($disks as $diskName) {
            try {
                $disk = Storage::disk($diskName);
                $root = $disk->path($backupName); // ✅ π.χ. storage/app/private/Laravel

                if (!is_dir($root)) {
                    continue;
                }

                $files = collect(File::allFiles($root))
                    ->filter(fn ($f) => Str::endsWith(Str::lower($f->getFilename()), '.zip'))
                    ->map(function ($f) {
                        return [
                            'path' => $f->getPathname(),
                            'mtime' => $f->getMTime(),
                        ];
                    });

                $allZipFiles = $allZipFiles->merge($files);
            } catch (\Throwable $e) {
                // αν κάποιο disk δεν είναι σωστό/ρυθμισμένο, το αγνοούμε
                continue;
            }
        }

        return $allZipFiles
            ->sortByDesc('mtime')
            ->first()['path'] ?? null;
    }
}



