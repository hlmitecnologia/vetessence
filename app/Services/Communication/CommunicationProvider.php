<?php

namespace App\Services\Communication;

interface CommunicationProvider
{
    public function send(string $destination, string $message): bool;
    public function getName(): string;
}
