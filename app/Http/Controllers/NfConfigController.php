<?php

namespace App\Http\Controllers;

use App\Models\NfeConfig;
use App\Models\NfseConfig;

class NfConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:nfe-config.edit');
    }

    public function edit()
    {
        $nfeConfig = NfeConfig::first() ?? new NfeConfig();
        $nfseConfig = NfseConfig::first() ?? new NfseConfig();

        return view('nf.config', compact('nfeConfig', 'nfseConfig'));
    }
}
