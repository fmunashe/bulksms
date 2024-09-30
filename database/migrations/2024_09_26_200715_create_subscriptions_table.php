<?php

use App\Models\Merchant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Merchant::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('total_messages')->nullable();
            $table->decimal('account_balance')->nullable();
            $table->decimal('sms_price')->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Expired'])->nullable()->default('Active');
            $table->dateTime('effective_date',)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
