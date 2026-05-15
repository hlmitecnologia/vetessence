<?php
namespace Tests\Feature\Controllers;

use App\Models\Role;
use Tests\ModuleTestCase;

class RoleDebug2Test extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_debug()
    {
        $role = Role::first();
        
        // First, check what the route resolves to
        $url = route('roles.update', $role);
        echo "URL: " . $url . PHP_EOL;
        
        // Try a GET first to see if we get 200
        $getResponse = $this->get(route('roles.edit', $role));
        echo "GET status: " . $getResponse->getStatusCode() . PHP_EOL;
        
        // Now try PUT
        $response = $this->put($url, [
            'name' => 'Admin Atualizado',
            'slug' => $role->slug,
            'description' => 'Descrição atualizada',
        ]);
        
        echo "PUT Status: " . $response->getStatusCode() . PHP_EOL;
        echo "PUT Location: " . ($response->headers->get('Location') ?? 'none') . PHP_EOL;
        echo "PUT isRedirect: " . ($response->isRedirect() ? 'yes' : 'no') . PHP_EOL;
        
        // If we followed redirects, check content
        $this->assertTrue(true); // prevent failure
    }
}
