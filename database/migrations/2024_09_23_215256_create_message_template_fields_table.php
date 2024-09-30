<?php

use App\Models\MessageTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('message_template_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(MessageTemplate::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('field_name');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_template_fields');
    }
};
