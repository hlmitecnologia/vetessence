<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DocsPublish extends Command
{
    protected $signature = 'docs:publish';
    protected $description = 'Publish documentation files to storage/docs';

    public function handle()
    {
        $source = resource_path('docs');
        $dest = storage_path('docs');

        if (!File::isDirectory($source)) {
            $this->error('Source directory not found: ' . $source);
            return 1;
        }

        // Publish markdown + diagrams to storage (Laravel serves via routes)
        if (File::isDirectory($dest)) {
            File::cleanDirectory($dest);
        }
        File::copyDirectory($source, $dest);

        // Remove public/docs/ so nginx passes all /docs* requests to Laravel
        $publicDocs = public_path('docs');
        if (File::isDirectory($publicDocs)) {
            File::deleteDirectory($publicDocs);
        }

        $count = count(File::allFiles($dest));

        $this->info("Documentation published: {$count} files to {$dest}");
        return 0;
    }
}
