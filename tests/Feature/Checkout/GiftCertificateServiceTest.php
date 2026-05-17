<?php

namespace Tests\Feature\Checkout;

use App\Livewire\Checkout\CheckoutForm;
use App\Models\GiftCertificate;
use App\Services\GiftCertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class GiftCertificateServiceTest extends TestCase
{
    use RefreshDatabase;

    private GiftCertificateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = app(GiftCertificateService::class);
    }

    // ─── findValid ───────────────────────────────────────────────────────────

    public function test_find_valid_returns_active_unused_cert(): void
    {
        GiftCertificate::create([
            'code'      => 'GIFT100',
            'amount'    => 100.0,
            'is_active' => true,
            'is_used'   => false,
        ]);

        $cert = $this->service->findValid('GIFT100');

        $this->assertNotNull($cert);
        $this->assertEquals('GIFT100', $cert->code);
    }

    public function test_find_valid_returns_null_for_used_cert(): void
    {
        GiftCertificate::create([
            'code'      => 'GIFT200',
            'amount'    => 200.0,
            'is_active' => true,
            'is_used'   => true,
        ]);

        $this->assertNull($this->service->findValid('GIFT200'));
    }

    public function test_find_valid_returns_null_for_inactive_cert(): void
    {
        GiftCertificate::create([
            'code'      => 'GIFT300',
            'amount'    => 300.0,
            'is_active' => false,
            'is_used'   => false,
        ]);

        $this->assertNull($this->service->findValid('GIFT300'));
    }

    public function test_find_valid_returns_null_for_expired_cert(): void
    {
        GiftCertificate::create([
            'code'       => 'GIFTEXP',
            'amount'     => 50.0,
            'is_active'  => true,
            'is_used'    => false,
            'expires_at' => now()->subDay(),
        ]);

        $this->assertNull($this->service->findValid('GIFTEXP'));
    }

    // ─── addCode ─────────────────────────────────────────────────────────────

    public function test_add_code_adds_valid_cert_to_session(): void
    {
        GiftCertificate::create([
            'code'      => 'GIFTADD',
            'amount'    => 150.0,
            'is_active' => true,
            'is_used'   => false,
        ]);

        $result = $this->service->addCode('GIFTADD');

        $this->assertTrue($result);
        $this->assertContains('GIFTADD', session('gift_certificates', []));
    }

    public function test_add_code_returns_false_for_invalid_cert(): void
    {
        $result = $this->service->addCode('NOTEXIST');

        $this->assertFalse($result);
        $this->assertEmpty(session('gift_certificates', []));
    }

    public function test_add_code_returns_false_if_already_in_session(): void
    {
        GiftCertificate::create([
            'code'      => 'GIFTDUP',
            'amount'    => 100.0,
            'is_active' => true,
            'is_used'   => false,
        ]);

        $this->service->addCode('GIFTDUP');
        $result = $this->service->addCode('GIFTDUP');

        $this->assertFalse($result);
        $this->assertCount(1, session('gift_certificates', []));
    }

    public function test_add_code_returns_false_if_reserved_by_another(): void
    {
        GiftCertificate::create([
            'code'      => 'GIFTRES',
            'amount'    => 100.0,
            'is_active' => true,
            'is_used'   => false,
        ]);

        Cache::put('gift:reserve:GIFTRES', true, 3600);

        $result = $this->service->addCode('GIFTRES');

        $this->assertFalse($result);
    }

    // ─── removeCode ──────────────────────────────────────────────────────────

    public function test_remove_code_removes_from_session_and_clears_reserve(): void
    {
        GiftCertificate::create([
            'code'      => 'GIFTRM',
            'amount'    => 100.0,
            'is_active' => true,
            'is_used'   => false,
        ]);

        $this->service->addCode('GIFTRM');
        $this->assertContains('GIFTRM', session('gift_certificates', []));

        $this->service->removeCode('GIFTRM');

        $this->assertNotContains('GIFTRM', session('gift_certificates', []));
        $this->assertFalse(Cache::has('gift:reserve:GIFTRM'));
    }

    // ─── getAppliedCertificates ──────────────────────────────────────────────

    public function test_get_applied_certificates_returns_empty_when_session_empty(): void
    {
        $certs = $this->service->getAppliedCertificates(1000.0);

        $this->assertTrue($certs->isEmpty());
    }

    public function test_get_applied_certificates_returns_applied_certs(): void
    {
        GiftCertificate::create([
            'code'      => 'GIFTGET',
            'amount'    => 250.0,
            'is_active' => true,
            'is_used'   => false,
        ]);

        $this->service->addCode('GIFTGET');

        $certs = $this->service->getAppliedCertificates(1000.0);

        $this->assertCount(1, $certs);
        $this->assertEquals('GIFTGET', $certs->first()['code']);
        $this->assertEquals(250.0, $certs->first()['amount']);
    }

    // ─── CheckoutForm Livewire ───────────────────────────────────────────────

    public function test_apply_gift_code_shows_success_message(): void
    {
        GiftCertificate::create([
            'code'      => 'GIFTLW',
            'amount'    => 100.0,
            'is_active' => true,
            'is_used'   => false,
        ]);

        Livewire::test(CheckoutForm::class)
            ->set('giftCodeInput', 'GIFTLW')
            ->call('applyGiftCode')
            ->assertSet('giftMessage', __t('Сертифікат застосовано'))
            ->assertSet('giftCodeInput', '');
    }

    public function test_apply_gift_code_normalizes_to_uppercase(): void
    {
        GiftCertificate::create([
            'code'      => 'GIFTUP',
            'amount'    => 100.0,
            'is_active' => true,
            'is_used'   => false,
        ]);

        Livewire::test(CheckoutForm::class)
            ->set('giftCodeInput', 'giftup')
            ->call('applyGiftCode')
            ->assertSet('giftMessage', __t('Сертифікат застосовано'));
    }

    public function test_apply_gift_code_shows_error_for_invalid_code(): void
    {
        Livewire::test(CheckoutForm::class)
            ->set('giftCodeInput', 'INVALID')
            ->call('applyGiftCode')
            ->assertSet('giftMessage', __t('Сертифікат не знайдено або вже використаний'));
    }

    public function test_apply_gift_code_does_nothing_for_empty_input(): void
    {
        Livewire::test(CheckoutForm::class)
            ->set('giftCodeInput', '')
            ->call('applyGiftCode')
            ->assertSet('giftMessage', '');
    }

    public function test_remove_gift_code_clears_message(): void
    {
        GiftCertificate::create([
            'code'      => 'GIFTRMLW',
            'amount'    => 100.0,
            'is_active' => true,
            'is_used'   => false,
        ]);

        Livewire::test(CheckoutForm::class)
            ->set('giftCodeInput', 'GIFTRMLW')
            ->call('applyGiftCode')
            ->call('removeGiftCode', 'GIFTRMLW')
            ->assertSet('giftMessage', '');
    }
}
