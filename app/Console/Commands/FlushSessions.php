<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FlushSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush all user sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $driver = config('session.driver');
        $method_name = 'clean' . ucfirst($driver);
        if (method_exists($this, $method_name)) {
            try {
                $this->$method_name();
                $this->info('Session data cleaned.');
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        } else {
            $this->error(
                "Impossible clean the sessions of the driver '{$driver}'."
            );
        }
    }

    protected function cleanFile(): void
    {
        $directory = config('session.files');
        $ignoreFiles = ['.gitignore', '.', '..'];

        $files = scandir($directory);
        foreach ($files as $file) {
            if(!in_array($file, $ignoreFiles)) {
                unlink($directory . '/' . $file);
            }
        }
    }

    protected function cleanDatabase(): void
    {
        $table = config('session.table');
        DB::table($table)->truncate();
    }
}
