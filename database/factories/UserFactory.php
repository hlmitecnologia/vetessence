<?php

namespace Database\Factories;

use App\Models\Role as AppRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role as SpatieRole;

class UserFactory extends Factory
{
    protected array $roleSlugMap = [
        'super-admin' => 'Super Administrador',
        'admin' => 'Administrador',
        'branch-admin' => 'Administrador de Unidade',
        'veterinario' => 'Veterinário',
        'recepcionista' => 'Recepcionista',
        'financeiro' => 'Financeiro',
        'super-financial' => 'Super Financeiro',
        'estoque' => 'Estoque',
        'human-resources' => 'Recursos Humanos',
        'tutor' => 'Tutor',
        'auditor' => 'Auditor',
        'tecnico' => 'Técnico',
    ];

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'remember_token' => Str::random(10),
            'is_veterinarian' => false,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    protected function withRole(string $slug): static
    {
        return $this->afterCreating(function ($user) use ($slug) {
            $appRole = AppRole::where('slug', $slug)->first();
            if ($appRole) {
                $user->role_id = $appRole->id;
            }

            $spatieRole = SpatieRole::where('name', $slug)->first();
            if (!$spatieRole) {
                $spatieRole = SpatieRole::create(['name' => $slug, 'guard_name' => 'web']);
                if ($appRole) {
                    $spatieRole->syncPermissions(
                        $appRole->spatiePermissions()->pluck('permissions.id')->toArray()
                    );
                }
            }

            $user->assignRole($spatieRole);
            $user->save();
        });
    }

    public function superAdmin(): static
    {
        return $this->state(fn() => ['is_veterinarian' => false])->withRole('super-admin');
    }

    public function admin(): static
    {
        return $this->state(fn() => ['is_veterinarian' => false])->withRole('admin');
    }

    public function branchAdmin(): static
    {
        return $this->state(fn() => ['is_veterinarian' => false])->withRole('branch-admin');
    }

    public function veterinario(): static
    {
        return $this->state(fn() => ['is_veterinarian' => true])->withRole('veterinario');
    }

    public function recepcionista(): static
    {
        return $this->state(fn() => ['is_veterinarian' => false])->withRole('recepcionista');
    }

    public function financeiro(): static
    {
        return $this->state(fn() => ['is_veterinarian' => false])->withRole('financeiro');
    }

    public function superFinancial(): static
    {
        return $this->state(fn() => ['is_veterinarian' => false])->withRole('super-financial');
    }

    public function estoque(): static
    {
        return $this->state(fn() => ['is_veterinarian' => false])->withRole('estoque');
    }

    public function humanResources(): static
    {
        return $this->state(fn() => ['is_veterinarian' => false])->withRole('human-resources');
    }

    public function tutor(): static
    {
        return $this->state(fn() => ['is_veterinarian' => false])->withRole('tutor');
    }

    public function auditor(): static
    {
        return $this->state(fn() => ['is_veterinarian' => false])->withRole('auditor');
    }

    public function tecnico(): static
    {
        return $this->state(fn() => ['is_veterinarian' => false])->withRole('tecnico');
    }
}
