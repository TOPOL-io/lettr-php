<?php

declare(strict_types=1);

use Lettr\Dto\Email\SendEmailResponse;
use Lettr\ValueObjects\RequestId;

test('can create SendEmailResponse', function (): void {
    $response = SendEmailResponse::from([
        'request_id' => 'req_123',
        'accepted' => 5,
        'rejected' => 1,
    ]);

    expect($response->requestId)->toBeInstanceOf(RequestId::class)
        ->and((string) $response->requestId)->toBe('req_123')
        ->and($response->accepted)->toBe(5)
        ->and($response->rejected)->toBe(1);
});

test('allAccepted returns true when no rejections', function (): void {
    $response = SendEmailResponse::from([
        'request_id' => 'req_123',
        'accepted' => 5,
        'rejected' => 0,
    ]);

    expect($response->allAccepted())->toBeTrue();
});

test('allAccepted returns false when has rejections', function (): void {
    $response = SendEmailResponse::from([
        'request_id' => 'req_123',
        'accepted' => 4,
        'rejected' => 1,
    ]);

    expect($response->allAccepted())->toBeFalse();
});

test('hasRejections returns correct value', function (): void {
    $responseWithRejections = SendEmailResponse::from([
        'request_id' => 'req_123',
        'accepted' => 4,
        'rejected' => 1,
    ]);

    $responseWithoutRejections = SendEmailResponse::from([
        'request_id' => 'req_456',
        'accepted' => 5,
        'rejected' => 0,
    ]);

    expect($responseWithRejections->hasRejections())->toBeTrue()
        ->and($responseWithoutRejections->hasRejections())->toBeFalse();
});

test('total returns sum of accepted and rejected', function (): void {
    $response = SendEmailResponse::from([
        'request_id' => 'req_123',
        'accepted' => 4,
        'rejected' => 1,
    ]);

    expect($response->total())->toBe(5);
});
