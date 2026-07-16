<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Criar convenio_subscriptions (plano contratado pelo tutor)
        Schema::create('convenio_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('tutors')->cascadeOnDelete();
            $table->foreignId('convenio_id')->constrained('convenios')->cascadeOnDelete();
            $table->string('policy_number')->nullable();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('external_policy_id')->nullable();
            $table->timestamp('eligibility_last_checked_at')->nullable();
            $table->timestamps();

            $table->unique(['tutor_id', 'convenio_id', 'policy_number']);
        });

        // 2. Criar convenio_covered_pets (quais pets do tutor estão cobertos)
        Schema::create('convenio_covered_pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('convenio_subscriptions')->cascadeOnDelete();
            $table->foreignId('pet_id')->constrained('pets')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['subscription_id', 'pet_id']);
        });

        // 3. Criar convenio_coverage_rules (o que cada convênio cobre)
        Schema::create('convenio_coverage_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('convenio_id')->constrained('convenios')->cascadeOnDelete();
            $table->string('item_type'); // service, product, procedure
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->decimal('coverage_percent', 5, 2)->default(100);
            $table->decimal('max_value', 10, 2)->nullable();
            $table->boolean('requires_pre_authorization')->default(false);
            $table->integer('annual_limit')->nullable();
            $table->timestamps();
        });

        // 4. Migrar dados de convenio_pet → convenio_subscriptions + convenio_covered_pets
        $rows = DB::table('convenio_pet')->get();
        foreach ($rows as $petRow) {
            $pet = DB::table('pets')->find($petRow->pet_id);
            if (!$pet) continue;

            $tutorRow = DB::table('pet_tutor')
                ->where('pet_id', $petRow->pet_id)
                ->orderBy('is_primary', 'desc')
                ->first();

            if (!$tutorRow) continue;

            $convenio = DB::table('convenios')->find($petRow->convenio_id);
            $discount = $convenio?->discount_percent ?? 0;

            $subscriptionId = DB::table('convenio_subscriptions')->insertGetId([
                'tutor_id' => $tutorRow->tutor_id,
                'convenio_id' => $petRow->convenio_id,
                'policy_number' => $petRow->policy_number,
                'discount_percent' => $discount,
                'start_date' => $petRow->start_date,
                'end_date' => $petRow->end_date,
                'is_active' => true,
                'external_policy_id' => $petRow->external_policy_id ?? null,
                'eligibility_last_checked_at' => $petRow->eligibility_last_checked_at ?? null,
                'created_at' => $petRow->created_at ?? now(),
                'updated_at' => $petRow->updated_at ?? now(),
            ]);

            DB::table('convenio_covered_pets')->insert([
                'subscription_id' => $subscriptionId,
                'pet_id' => $petRow->pet_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. Adicionar subscription_id em convenio_claims (nullable)
        Schema::table('convenio_claims', function (Blueprint $table) {
            $table->foreignId('subscription_id')->nullable()->after('convenio_pet_id')
                ->constrained('convenio_subscriptions')->cascadeOnDelete();
        });

        // 6. Preencher subscription_id em convenio_claims baseado no convenio_pet_id
        DB::statement('
            UPDATE convenio_claims cc
            JOIN convenio_pet cp ON cc.convenio_pet_id = cp.id
            JOIN convenio_covered_pets ccp ON ccp.pet_id = cp.pet_id
            JOIN convenio_subscriptions cs ON cs.id = ccp.subscription_id AND cs.convenio_id = cp.convenio_id
            SET cc.subscription_id = cs.id
        ');
    }

    public function down(): void
    {
        Schema::table('convenio_claims', function (Blueprint $table) {
            $table->dropForeign(['subscription_id']);
            $table->dropColumn('subscription_id');
        });
        Schema::dropIfExists('convenio_coverage_rules');
        Schema::dropIfExists('convenio_covered_pets');
        Schema::dropIfExists('convenio_subscriptions');
    }
};
