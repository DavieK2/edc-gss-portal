<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('IsRepeated')->nullable();
            $table->string('ProductGroupCode')->nullable();
            $table->string('PaymentLogId')->nullable();
            $table->string('CustReference')->nullable();
            $table->string('AlternateCustReference')->nullable();
            $table->string('Amount')->nullable();
            $table->string('PaymentStatus')->nullable();
            $table->string('PaymentMethod')->nullable();
            $table->string('PaymentReference')->nullable();
            $table->string('TerminalId')->nullable();
            $table->string('ChannelName')->nullable();
            $table->string('Location')->nullable();
            $table->string('IsReversal')->nullable();
            $table->string('PaymentDate')->nullable();
            $table->string('SettlementDate')->nullable();
            $table->string('InstitutionId')->nullable();
            $table->string('InstitutionName')->nullable();
            $table->string('BranchName')->nullable();
            $table->string('BankName')->nullable();
            $table->string('FeeName')->nullable();
            $table->string('CustomerName')->nullable();
            $table->string('OtherCustomerInfo')->nullable();
            $table->string('ReceiptNo')->nullable();
            $table->string('CollectionsAccount')->nullable();
            $table->string('ThirdPartyCode')->nullable();
            $table->string('PaymentItems')->nullable();
            $table->string('BankCode')->nullable();
            $table->string('CustomerAddress')->nullable();
            $table->string('CustomerPhoneNumber')->nullable();
            $table->string('DepositorName')->nullable();
            $table->string('DepositSlipNumber')->nullable();
            $table->string('PaymentCurrency')->nullable();
            $table->string('OriginalPaymentLogId')->nullable();
            $table->string('OriginalPaymentReference')->nullable();
            $table->string('Teller')->nullable();
            $table->integer('registration_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_notifications');
    }
};
