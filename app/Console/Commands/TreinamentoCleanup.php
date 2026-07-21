<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Category;
use App\Models\DrugFormulary;
use App\Models\Exam;
use App\Models\Hospitalization;
use App\Models\ImagingExam;
use App\Models\Invoice;
use App\Models\LaboratoryOrder;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StaffNote;
use App\Models\StaffSchedule;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Surgery;
use App\Models\Tutor;
use App\Models\Vaccination;
use Illuminate\Console\Command;

class TreinamentoCleanup extends Command
{
    protected $signature = 'treinamento:cleanup {--module= : Module to clean up data for}';
    protected $description = 'Remove dados criados por roteiros de treinamento';

    public function handle(): void
    {
        $module = $this->option('module');

        match ($module) {
            '01-prontuarios' => $this->cleanupProntuarios(),
            '02-cirurgia-internacao' => $this->cleanupCirurgiaInternacao(),
            '03-vacinas' => $this->cleanupVacinas(),
            '04-estoque-avancado' => $this->cleanupEstoqueAvancado(),
            '05-portal-tutor' => $this->cleanupPortalTutor(),
            '06-exames-laboratorio' => $this->cleanupExamesLaboratorio(),
            '07-farmacia' => $this->cleanupFarmacia(),
            '08-admin-config' => $this->cleanupAdminConfig(),
            '09-financeiro' => $this->cleanupFinanceiro(),
            '10-agendamento' => $this->cleanupAgendamento(),
            '11-tutores-pets' => $this->cleanupTutoresPets(),
            '12-comunicacao' => $this->cleanupComunicacao(),
            '13-agenda-equipe' => $this->cleanupAgendaEquipe(),
            default => $this->warn("Módulo '{$module}' não reconhecido."),
        };

        $this->info('✅ Cleanup concluído.');
    }

    private function cleanupAgendamento(): void
    {
        $this->info('Limpando dados do roteiro 10-agendamento…');
        Appointment::where('date', '2026-07-25')
            ->where('time', '14:00')
            ->whereHas('pet', fn($q) => $q->where('name', 'like', 'Luna'))
            ->delete();
    }

    private function cleanupProntuarios(): void
    {
        $this->info('Limpando dados do roteiro 01-prontuarios…');
        $records = MedicalRecord::where('chief_complaint', 'like', '%Vômito e diarreia%')->get();
        foreach ($records as $r) {
            Prescription::where('medical_record_id', $r->id)->delete();
            $r->delete();
        }
    }

    private function cleanupCirurgiaInternacao(): void
    {
        $this->info('Limpando dados do roteiro 02-cirurgia-internacao…');
        Surgery::where('surgery_type', 'Castração')->get()->each->delete();
        Hospitalization::where('admission_reason', 'like', '%Castração%')->get()->each->delete();
        Appointment::where('type', 'cirurgia')->where('reason', 'like', '%Castração%')->get()->each->delete();
    }

    private function cleanupVacinas(): void
    {
        $this->info('Limpando dados do roteiro 03-vacinas…');
        Vaccination::where('vaccine', 'V10')->get()->each->delete();
    }

    private function cleanupEstoqueAvancado(): void
    {
        $this->info('Limpando dados do roteiro 04-estoque-avancado…');
        PurchaseOrder::whereHas('items', fn($q) => $q->where('product_id', 1))->get()->each->delete();
    }

    private function cleanupPortalTutor(): void
    {
        $this->info('Limpando dados do roteiro 05-portal-tutor (somente visualização, sem dados criados)…');
    }

    private function cleanupExamesLaboratorio(): void
    {
        $this->info('Limpando dados do roteiro 06-exames-laboratorio…');
        Exam::where('type', 'Hemograma Completo')->get()->each->delete();
        LaboratoryOrder::where('lab_name', 'LabVet')->get()->each->delete();
        ImagingExam::where('region', 'Tórax')->get()->each->delete();
    }

    private function cleanupFarmacia(): void
    {
        $this->info('Limpando dados do roteiro 07-farmacia…');
        DrugFormulary::where('drug', 'Dipirona Sódica')->delete();
        $prod = Product::where('name', 'Dipirona 500mg')->first();
        if ($prod) {
            StockMovement::where('product_id', $prod->id)->delete();
            $prod->delete();
        }
        Supplier::where('name', 'FarMed Distribuidora')->delete();
        Category::where('name', 'Medicamentos')->delete();
    }

    private function cleanupFinanceiro(): void
    {
        $this->info('Limpando dados do roteiro 09-financeiro…');
        Invoice::whereHas('items', fn($q) => $q->where('description', 'like', '%Consulta Geral%'))->get()->each->delete();
    }

    private function cleanupAdminConfig(): void
    {
        $this->info('Roteiro 08-admin-config é somente visualização (sem dados criados).');
    }

    private function cleanupTutoresPets(): void
    {
        $this->info('Limpando dados do roteiro 11-tutores-pets…');
        // Force detach tutors before deleting pet
        $pet = Pet::where('name', 'Rex')->first();
        if ($pet) {
            $tutor = $pet->tutors->first();
            $pet->tutors()->detach();
            $pet->delete();
            if ($tutor && $tutor->name === 'Maria das Dores') {
                $tutor->pets()->detach();
                $tutor->delete();
            }
        }
    }

    private function cleanupComunicacao(): void
    {
        $this->info('Limpando dados do roteiro 12-comunicacao…');
        StaffNote::where('title', 'Nota de Treinamento')->delete();
    }

    private function cleanupAgendaEquipe(): void
    {
        $this->info('Limpando dados do roteiro 13-agenda-equipe…');
        StaffSchedule::where('start_time', '08:00')->where('end_time', '18:00')->delete();
    }
}
