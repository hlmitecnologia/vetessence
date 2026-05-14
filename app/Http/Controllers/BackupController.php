<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:backup');
    }

    public function index()
    {
        $files = collect(Storage::files('backups'))->map(function ($file) {
            return [
                'name' => basename($file),
                'size' => Storage::size($file),
                'last_modified' => Storage::lastModified($file),
            ];
        })->sortByDesc('last_modified');

        return view('backups.index', compact('files'));
    }

    public function create()
    {
        Artisan::call('backup:database');

        return redirect()->route('backups.index')
            ->with('success', 'Backup criado: ' . Artisan::output());
    }

    public function download($filename)
    {
        $path = 'backups/' . basename($filename);

        if (!Storage::exists($path)) {
            return abort(404);
        }

        return Storage::download($path);
    }

    public function destroy($filename)
    {
        Storage::delete('backups/' . basename($filename));

        return redirect()->route('backups.index')
            ->with('success', 'Backup removido.');
    }
}
