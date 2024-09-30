<?php

use App\Models\PricePlanType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('price_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(PricePlanType::class)->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('price_plan');
            $table->decimal('price_per_sms', 10, 4)->default(0.0000);
            $table->decimal('total_sms', 10, 0)->default(0.0000);
            $table->decimal('total_price', 10, 2)->default(0.0000);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_plans');
    }
};
