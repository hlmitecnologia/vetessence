<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DocsPublish extends Command
{
    protected $signature = 'docs:publish';
    protected $description = 'Publish documentation files to storage/docs and public/docs/diagrams';

    public function handle()
    {
        $source = resource_path('docs');
        $dest = storage_path('docs');
        $publicDiagrams = public_path('docs/diagrams');

        if (!File::isDirectory($source)) {
            $this->error('Source directory not found: ' . $source);
            return 1;
        }

        // Publish markdown to storage
        if (File::isDirectory($dest)) {
            File::cleanDirectory($dest);
        }
        File::copyDirectory($source, $dest);

        // Publish diagrams to public for direct web server access
        $diagramsSource = $source . '/diagrams';
        if (File::isDirectory($diagramsSource)) {
            if (File::isDirectory($publicDiagrams)) {
                File::cleanDirectory($publicDiagrams);
            }
            File::copyDirectory($diagramsSource, $publicDiagrams);
        }

        $count = count(File::allFiles($dest));

        $this->info("Documentation published: {$count} files to {$dest}");
        return 0;
    }
}
