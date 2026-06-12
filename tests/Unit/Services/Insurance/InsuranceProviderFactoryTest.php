<?php

namespace Tests\Unit\Services\Insurance;

use App\Services\Insurance\InsuranceProvider;
use App\Services\Insurance\InsuranceProviderFactory;
use App\Services\Insurance\PortoSeguroProvider;
use InvalidArgumentException;
use Tests\ModuleTestCase;

class InsuranceProviderFactoryTest extends ModuleTestCase
{
    public function test_make_returns_porto_seguro_provider(): void
    {
        $provider = InsuranceProviderFactory::make('porto-seguro');

        $this->assertInstanceOf(PortoSeguroProvider::class, $provider);
        $this->assertInstanceOf(InsuranceProvider::class, $provider);
    }

    public function test_make_throws_exception_for_unsupported_provider(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown insurance provider: unreal');

        InsuranceProviderFactory::make('unreal');
    }

    public function test_make_throws_exception_for_empty_string(): void
    {
        $this->expectException(InvalidArgumentException::class);

        InsuranceProviderFactory::make('');
    }

    public function test_register_and_make_custom_provider(): void
    {
        InsuranceProviderFactory::register('custom', FakeInsuranceProvider::class);

        $provider = InsuranceProviderFactory::make('custom');

        $this->assertInstanceOf(FakeInsuranceProvider::class, $provider);
    }

    public function test_make_with_porto_seguro_returns_same_class_as_registered(): void
    {
        $first = InsuranceProviderFactory::make('porto-seguro');
        $second = InsuranceProviderFactory::make('porto-seguro');

        $this->assertInstanceOf(PortoSeguroProvider::class, $first);
        $this->assertInstanceOf(PortoSeguroProvider::class, $second);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}

class FakeInsuranceProvider implements InsuranceProvider
{
    public function submitClaim(\App\Models\ConvenioClaim $claim): \App\Services\Insurance\InsuranceClaimResult
    {
        return \App\Services\Insurance\InsuranceClaimResult::success('fake', 'ext-1');
    }

    public function checkStatus(string $claimId): \App\Services\Insurance\InsuranceClaimResult
    {
        return \App\Services\Insurance\InsuranceClaimResult::success('fake', $claimId);
    }

    public function getName(): string
    {
        return 'fake';
    }
}
