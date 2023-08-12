<?php

namespace LucaF87\PCloudAdapter\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanLocalStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flysystem-pcloud:clean-local-storage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all files in local storage if use another filesystem.';

    /**
     * The parameter that indicates the time in minutes that the files are valid
     *
     * @var int
     */
    protected $keepAlive;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('START CRON');

        try {
            $this->keepAlive = (int)config('flysystem-pcloud.local_files_keep_alive') ?? 60;

            if (env('FILESYSTEM_DISK') != 'pCloud') {
                $this->warn('The Filesystem used is not pCloud. Exit');
                exit;
            }

            $files = Storage::disk('local')->allFiles();
            foreach ($files as $file) {

                $skip = false;
                foreach (config('flysystem-pcloud.clean_excluded') as $excluded) {
                    if(str_contains($file, $excluded)){
                        $skip = true;
                        break;
                    }
                }
                if ($skip){
                    continue;
                }

                $this->info('File: ' . $file);

                $time = Storage::disk('local')->lastModified($file);
                $this->info('Time: ' . $time);
                if (Carbon::parse($time)->addMinutes($this->keepAlive) < Carbon::now()) {
                    $this->info('File too old');
                    Storage::disk('local')->delete($file);
                    Storage::disk('local')->deleteDirectory(dirname($file));
                    $this->info('File deleted');
                }
            }

            $this->info('DONE!');

        }catch (\Exception $e){
            $this->error($e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
}
