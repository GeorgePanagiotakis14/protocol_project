<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    /**
     * POST /admin/backup/run
     * Κάνει backup της βάσης σε .sql και το κατεβάζει.
     */
    public function run(Request $request)
    {
        return $this->perform();
    }

    /**
     * Δημιουργεί SQL dump (schema + data) για MySQL.
     */
    public function perform()
    {
        $connection = config('database.default'); // π.χ. mysql
        $cfg = config("database.connections.$connection");

        if (($cfg['driver'] ?? null) !== 'mysql') {
            abort(500, 'BackupController supports only MySQL connections.');
        }

        $databaseName = (string) ($cfg['database'] ?? '');
        if ($databaseName === '') {
            abort(500, 'Database name is missing in config.');
        }

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $fileName = 'backup_' . $databaseName . '_' . now()->format('Y_m_d_H_i_s') . '.sql';
        $path = $backupDir . DIRECTORY_SEPARATOR . $fileName;

        // Παίρνουμε όλα τα tables (base tables)
        $tables = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
        if (empty($tables)) {
            abort(500, 'No tables found or insufficient permissions.');
        }

        // Το πρώτο πεδίο στο αποτέλεσμα είναι το "Tables_in_<db>"
        $firstRow = (array) $tables[0];
        $tableColumn = array_key_first($firstRow);

        // Ξεκινάμε να γράφουμε κατευθείαν σε αρχείο (όχι σε ένα τεράστιο string)
        $fh = fopen($path, 'wb');
        if (!$fh) {
            abort(500, 'Cannot write backup file.');
        }

        $this->writeLine($fh, "-- Database Backup");
        $this->writeLine($fh, "-- Database: {$databaseName}");
        $this->writeLine($fh, "-- Generated: " . now()->toDateTimeString());
        $this->writeLine($fh, "");
        $this->writeLine($fh, "SET NAMES utf8mb4;");
        $this->writeLine($fh, "SET FOREIGN_KEY_CHECKS=0;");
        $this->writeLine($fh, "");

        foreach ($tables as $t) {
            $tableName = (string) ($t->$tableColumn ?? '');
            if ($tableName === '') {
                continue;
            }

            $safeTable = $this->escapeIdentifier($tableName);

            $this->writeLine($fh, "-- ----------------------------");
            $this->writeLine($fh, "-- Table structure for {$safeTable}");
            $this->writeLine($fh, "-- ----------------------------");

            // DROP + CREATE
            $this->writeLine($fh, "DROP TABLE IF EXISTS {$safeTable};");

            $createRow = DB::select("SHOW CREATE TABLE {$safeTable}");
            $createSql = $createRow[0]->{'Create Table'} ?? null;

            if (!$createSql) {
                fclose($fh);
                abort(500, "Could not read CREATE TABLE for {$tableName}");
            }

            $this->writeLine($fh, $createSql . ";");
            $this->writeLine($fh, "");

            // DATA
            $this->writeLine($fh, "-- ----------------------------");
            $this->writeLine($fh, "-- Records of {$safeTable}");
            $this->writeLine($fh, "-- ----------------------------");

            // Chunk για να μη φορτώνεις ολόκληρο table στη μνήμη
            $chunkSize = 500;
            DB::table($tableName)->orderByRaw('1')->chunk($chunkSize, function ($rows) use ($fh, $safeTable) {
                foreach ($rows as $row) {
                    $values = array_map(function ($value) {
                        return $this->sqlValue($value);
                    }, (array) $row);

                    $this->writeLine(
                        $fh,
                        "INSERT INTO {$safeTable} VALUES (" . implode(',', $values) . ");"
                    );
                }
            });

            $this->writeLine($fh, "");
            $this->writeLine($fh, "");
        }

        $this->writeLine($fh, "SET FOREIGN_KEY_CHECKS=1;");
        fclose($fh);

        // Download + delete after send
        return response()->download($path, $fileName, [
            'Content-Type' => 'application/sql',
        ])->deleteFileAfterSend(true);
    }

    private function writeLine($fh, string $line): void
    {
        fwrite($fh, $line . "\n");
    }

    /**
     * Escapes table/column identifiers with backticks.
     */
    private function escapeIdentifier(string $name): string
    {
        // escape any backticks inside identifier
        $name = str_replace('`', '``', $name);
        return "`{$name}`";
    }

    /**
     * Converts a PHP value to safe SQL literal.
     */
    private function sqlValue($value): string
    {
        if (is_null($value)) {
            return 'NULL';
        }

        // booleans as 0/1
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        // numbers unquoted
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        // Fallback: quote strings
        $str = (string) $value;

        // Escape backslashes + quotes + newlines (basic MySQL string escaping)
        $str = str_replace(
            ["\\", "\0", "\n", "\r", "\t", "'", "\"", "\x1a"],
            ["\\\\", "\\0", "\\n", "\\r", "\\t", "\\'", "\\\"", "\\Z"],
            $str
        );

        return "'" . $str . "'";
    }
}



