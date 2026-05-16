<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DocsPublish extends Command
{
    protected $signature = 'docs:publish';
    protected $description = 'Publish documentation markdown files to storage/docs';

    public function handle()
    {
        $source = resource_path('docs');
        $dest = storage_path('docs');

        if (!File::isDirectory($source)) {
            $this->error('Source directory not found: ' . $source);
            return 1;
        }

        File::copyDirectory($source, $dest);

        $count = 0;
        $files = File::allFiles($dest);
        foreach ($files as $file) {
            $count++;
        }

        $this->info("Documentation published: {$count} files to {$dest}");
        return 0;
    }
}
