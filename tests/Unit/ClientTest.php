<?php

declare(strict_types=1);

use Lettr\Client;
use Lettr\Contracts\TransporterContract;

test('can create Client instance', function (): void {
    $client = new Client('test-api-key');

    expect($client)->toBeInstanceOf(Client::class);
});

test('can create Client instance with custom base url', function (): void {
    $client = new Client('test-api-key', 'https://custom.api.com');

    expect($client)->toBeInstanceOf(Client::class);
});

test('implements TransporterContract', function (): void {
    $client = new Client('test-api-key');

    expect($client)->toBeInstanceOf(TransporterContract::class);
});
