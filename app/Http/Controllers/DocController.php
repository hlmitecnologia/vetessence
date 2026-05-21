<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:docs.view');
    }

    public function index()
    {
        $sidebar = $this->buildSidebar();
        $content = $this->renderFile('index');
        return view('docs.index', compact('sidebar', 'content'));
    }

    public function show($section, $page = null)
    {
        $file = $page ? "{$section}/{$page}" : $section;
        $sidebar = $this->buildSidebar();
        $content = $this->renderFile($file);
        return view('docs.index', compact('sidebar', 'content'));
    }

    private function buildSidebar(): array
    {
        return [
            [
                'title' => 'Manual do Usuário',
                'icon'  => 'fa-user',
                'pages' => [
                    ['label' => 'Visão Geral', 'route' => 'docs.show', 'params' => ['section' => 'user-manual']],
                ],
            ],
            [
                'title' => 'Manual Técnico',
                'icon'  => 'fa-cog',
                'pages' => [
                    ['label' => 'Visão Geral', 'route' => 'docs.show', 'params' => ['section' => 'technical-manual']],
                ],
            ],
            [
                'title' => 'Changelog',
                'icon'  => 'fa-history',
                'pages' => [
                    ['label' => 'Versões', 'route' => 'docs.show', 'params' => ['section' => 'changelog']],
                ],
            ],
        ];
    }

    private function renderFile(string $file): string
    {
        $path = storage_path("docs/{$file}.md");

        if (!file_exists($path)) {
            $path = storage_path("docs/{$file}/index.md");
        }

        if (!file_exists($path)) {
            return "<div class='alert alert-warning'>Documento nao encontrado: <code>{$file}.md</code></div>";
        }

        $markdown = file_get_contents($path);
        $clinicName = $this->getClinicName();
        $markdown = str_replace('VetEssence', $clinicName, $markdown);

        $html = Str::markdown($markdown);

        $html = preg_replace_callback(
            '/<h([1-6])>(.*?)<\/h[1-6]>/',
            function ($m) {
                $id = Str::slug($m[2]);
                return "<h{$m[1]} id=\"{$id}\">{$m[2]}</h{$m[1]}>";
            },
            $html
        );

        return $html;
    }

    private function getClinicName(): string
    {
        return Setting::get('branding.clinic_name', 'VetEssence');
    }
}
