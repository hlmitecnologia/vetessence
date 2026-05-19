<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Str;

class DocController extends Controller
{
    public function index()
    {
        return $this->render('index');
    }

    public function show($page)
    {
        $page = preg_replace('/[^a-z0-9-]/', '', $page);

        if (!file_exists(storage_path("docs/tutor-manual/{$page}.md"))) {
            $page = 'index';
        }

        return $this->render($page);
    }

    private function render(string $page)
    {
        $path = storage_path("docs/tutor-manual/{$page}.md");

        if (!file_exists($path)) {
            abort(404);
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

        $sidebar = $this->buildSidebar();
        $currentPage = $page;
        $clinicName = $this->getClinicName();

        return view('portal.docs.index', compact('html', 'sidebar', 'currentPage', 'clinicName'));
    }

    private function buildSidebar(): array
    {
        return [
            ['label' => 'Visão Geral', 'page' => 'index'],
            ['label' => 'Login e Primeiro Acesso', 'page' => '01-login'],
            ['label' => 'Dashboard', 'page' => '02-dashboard'],
            ['label' => 'Meus Pets', 'page' => '03-meus-pets'],
            ['label' => 'Agendamento', 'page' => '04-agendamento'],
            ['label' => 'Prontuários', 'page' => '05-prontuarios'],
            ['label' => 'Vacinas', 'page' => '06-vacinas'],
            ['label' => 'Exames', 'page' => '07-exames'],
            ['label' => 'Receitas', 'page' => '08-receitas'],
            ['label' => 'Faturas', 'page' => '09-faturas'],
            ['label' => 'Chat', 'page' => '10-chat'],
            ['label' => 'Notificações', 'page' => '11-notificacoes'],
            ['label' => 'Dúvidas Frequentes', 'page' => '12-duvidas-frequentes'],
        ];
    }

    private function getClinicName(): string
    {
        return Setting::get('branding.clinic_name', 'VetEssence');
    }
}
