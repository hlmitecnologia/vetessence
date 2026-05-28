<?php

namespace Tests\Unit\Models;

use App\Models\Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        Setting::create(['key' => 'app_name', 'value' => 'VetEssence']);
        $this->assertDatabaseHas('settings', ['key' => 'app_name', 'value' => 'VetEssence']);
    }

    public function test_get_returns_value()
    {
        Setting::set('app_name', 'VetEssence');
        $this->assertEquals('VetEssence', Setting::get('app_name'));
    }

    public function test_get_returns_default_when_missing()
    {
        $this->assertNull(Setting::get('nonexistent'));
        $this->assertEquals('default', Setting::get('nonexistent', 'default'));
    }

    public function test_set_updates_existing_key()
    {
        Setting::set('app_name', 'Old');
        Setting::set('app_name', 'New');
        $this->assertEquals('New', Setting::get('app_name'));
    }
}
