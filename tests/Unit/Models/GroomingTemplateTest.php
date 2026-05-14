<?php

namespace Tests\Unit\Models;

use App\Models\GroomingTemplate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GroomingTemplateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        GroomingTemplate::create([
            'name' => 'Banho Tosa',
            'price' => 89.90,
            'estimated_minutes' => 60,
        ]);

        $this->assertDatabaseHas('grooming_templates', [
            'name' => 'Banho Tosa',
            'price' => 89.90,
            'estimated_minutes' => 60,
        ]);
    }

    public function test_services_cast()
    {
        $services = ['banho', 'tosa', 'hidratacao'];
        $template = GroomingTemplate::create([
            'name' => 'Banho Tosa',
            'services' => $services,
            'price' => 89.90,
            'estimated_minutes' => 60,
        ]);

        $this->assertIsArray($template->services);
    }
}
