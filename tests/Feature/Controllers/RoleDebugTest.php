<?php
// Simple test to check what happens
namespace Tests\Feature\Controllers;

use App\Models\Role;
use Tests\ModuleTestCase;

class RoleDebugTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_debug_update()
    {
        $role = Role::first();
        
        $response = $this->put(route('roles.update', $role), [
            'name' => 'Admin Atualizado',
            'slug' => $role->slug,
            'description' => 'Descrição atualizada',
        ]);
        
        echo "Status: " . $response->getStatusCode() . "\n";
        echo "Location: " . ($response->headers->get('Location') ?? 'none') . "\n";
        echo "Session has errors: " . (session()->has('errors') ? 'yes' : 'no') . "\n";
        if (session()->has('errors')) {
            echo "Errors: " . json_encode(session('errors')->toArray()) . "\n";
        }
    }
}
