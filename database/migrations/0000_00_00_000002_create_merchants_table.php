<?php

use App\Models\PricePlan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(PricePlan::class)->nullable()->constrained();
            $table->string('trade_name');
            $table->string('email');
            $table->string('mobile');
            $table->string('contact_person');
            $table->string('contact_person_mobile');
            $table->string('contact_person_email');
            $table->longText('address');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
