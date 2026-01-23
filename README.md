# Lettr PHP SDK

[![CI](https://github.com/TOPOL-io/lettr-php/actions/workflows/ci.yml/badge.svg)](https://github.com/TOPOL-io/lettr-php/actions/workflows/ci.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/lettr/lettr-php.svg)](https://packagist.org/packages/lettr/lettr-php)
[![Total Downloads](https://img.shields.io/packagist/dt/lettr/lettr-php.svg)](https://packagist.org/packages/lettr/lettr-php)
[![PHP Version](https://img.shields.io/packagist/php-v/lettr/lettr-php.svg)](https://packagist.org/packages/lettr/lettr-php)
[![License](https://img.shields.io/packagist/l/lettr/lettr-php.svg)](https://packagist.org/packages/lettr/lettr-php)

Official PHP SDK for the [Lettr](https://uselettr.com) email API.

## Requirements

- PHP 8.4+
- Guzzle HTTP client 7.5+

## Installation

```bash
composer require lettr/lettr-php
```

## Quick Start

```php
use Lettr\Lettr;

$lettr = Lettr::client('your-api-key');

// Send an email
$response = $lettr->emails()->send(
    $lettr->emails()->create()
        ->from('sender@example.com', 'Sender Name')
        ->to(['recipient@example.com'])
        ->subject('Hello from Lettr')
        ->html('<h1>Hello!</h1><p>This is a test email.</p>')
);

echo $response->requestId; // Request ID for tracking
echo $response->accepted;  // Number of accepted recipients
```

## Sending Emails

### Using the Email Builder (Recommended)

The fluent builder provides a clean API for constructing emails:

```php
$response = $lettr->emails()->send(
    $lettr->emails()->create()
        ->from('sender@example.com', 'Sender Name')
        ->to(['recipient@example.com'])
        ->cc(['cc@example.com'])
        ->bcc(['bcc@example.com'])
        ->replyTo('reply@example.com')
        ->subject('Welcome!')
        ->html('<h1>Welcome</h1>')
        ->text('Welcome (plain text fallback)')
        ->transactional()
        ->withClickTracking(true)
        ->withOpenTracking(true)
        ->metadata(['user_id' => '123', 'campaign' => 'welcome'])
        ->substitutionData(['name' => 'John', 'company' => 'Acme'])
        ->campaignId('welcome-series')
);
```

### Using SendEmailData DTO

For programmatic email construction:

```php
use Lettr\Dto\Email\SendEmailData;
use Lettr\Dto\Email\EmailOptions;
use Lettr\ValueObjects\EmailAddress;
use Lettr\ValueObjects\Subject;
use Lettr\Collections\EmailAddressCollection;

$email = new SendEmailData(
    from: new EmailAddress('sender@example.com', 'Sender'),
    to: EmailAddressCollection::from(['recipient@example.com']),
    subject: new Subject('Hello'),
    html: '<p>Email content</p>',
);

$response = $lettr->emails()->send($email);
```

### Quick Send Methods

For simple use cases:

```php
// HTML email
$response = $lettr->emails()->sendHtml(
    from: 'sender@example.com',
    to: 'recipient@example.com',
    subject: 'Hello',
    html: '<p>HTML content</p>',
);

// Plain text email
$response = $lettr->emails()->sendText(
    from: ['email' => 'sender@example.com', 'name' => 'Sender'],
    to: ['recipient1@example.com', 'recipient2@example.com'],
    subject: 'Hello',
    text: 'Plain text content',
);

// Template email
$response = $lettr->emails()->sendTemplate(
    from: 'sender@example.com',
    to: 'recipient@example.com',
    subject: 'Welcome!',
    templateSlug: 'welcome-email',
    templateVersion: 2,
    projectId: 123,
    substitutionData: ['name' => 'John'],
);
```

### Attachments

```php
use Lettr\Dto\Email\Attachment;

$email = $lettr->emails()->create()
    ->from('sender@example.com')
    ->to(['recipient@example.com'])
    ->subject('Document attached')
    ->html('<p>Please find the document attached.</p>')
    // From file path
    ->attachFile('/path/to/document.pdf')
    // With custom name and mime type
    ->attachFile('/path/to/file', 'custom-name.pdf', 'application/pdf')
    // From binary data
    ->attachData($binaryContent, 'report.csv', 'text/csv')
    // Using Attachment DTO
    ->attach(Attachment::fromFile('/path/to/image.png'));

$response = $lettr->emails()->send($email);
```

### Templates with Substitution Data

```php
$response = $lettr->emails()->send(
    $lettr->emails()->create()
        ->from('sender@example.com')
        ->to(['recipient@example.com'])
        ->subject('Your Order #{{order_id}}')
        ->useTemplate('order-confirmation', version: 1, projectId: 123)
        ->substitutionData([
            'order_id' => '12345',
            'customer_name' => 'John Doe',
            'items' => [
                ['name' => 'Product A', 'price' => 29.99],
                ['name' => 'Product B', 'price' => 49.99],
            ],
            'total' => 79.98,
        ])
);
```

### Email Options

```php
$email = $lettr->emails()->create()
    ->from('sender@example.com')
    ->to(['recipient@example.com'])
    ->subject('Newsletter')
    ->html($htmlContent)
    // Tracking
    ->withClickTracking(true)
    ->withOpenTracking(true)
    // Mark as transactional (bypasses unsubscribe lists)
    ->transactional(false)
    // CSS inlining
    ->withInlineCss(true)
    // Template variable substitution
    ->withSubstitutions(true);
```

## Retrieving Emails

### Get Email Events by Request ID

```php
use Lettr\ValueObjects\RequestId;

// After sending
$response = $lettr->emails()->send($email);
$requestId = $response->requestId;

// Later, retrieve events
$result = $lettr->emails()->get($requestId);
// or
$result = $lettr->emails()->get('req_abc123');

foreach ($result->events as $event) {
    echo $event->type->value;      // 'delivery', 'open', 'click', etc.
    echo $event->recipient;        // Recipient email
    echo $event->timestamp;        // When the event occurred
    echo $event->messageId;        // Unique message ID

    // Event-specific data
    if ($event->type === EventType::Click) {
        echo $event->clickUrl;     // Clicked URL
    }
    if ($event->type === EventType::Bounce) {
        echo $event->bounceClass;  // Bounce classification
        echo $event->reason;       // Bounce reason
    }
}
```

### List Email Events with Filtering

```php
use Lettr\Dto\Email\ListEmailsFilter;

// List all events
$result = $lettr->emails()->list();

// With filters
$filter = ListEmailsFilter::create()
    ->perPage(50)
    ->forRecipient('user@example.com')
    ->fromDate('2024-01-01')
    ->toDate('2024-12-31');

$result = $lettr->emails()->list($filter);

echo $result->totalCount;
echo $result->pagination->hasNextPage();

// Paginate through results
while ($result->hasMore()) {
    foreach ($result->events as $event) {
        // Process event
    }

    $filter = $filter->cursor($result->pagination->nextCursor);
    $result = $lettr->emails()->list($filter);
}
```

## Domain Management

### List Domains

```php
$domains = $lettr->domains()->list();

foreach ($domains as $domain) {
    echo $domain->domain;           // example.com
    echo $domain->status->value;    // 'pending', 'approved'
    echo $domain->canSend;          // true/false
    echo $domain->dkimStatus;       // DnsStatus enum
    echo $domain->returnPathStatus; // DnsStatus enum
}
```

### Add a Domain

```php
use Lettr\ValueObjects\DomainName;

$result = $lettr->domains()->create('example.com');
// or
$result = $lettr->domains()->create(new DomainName('example.com'));

echo $result->domain;
echo $result->status;

// DNS records to configure
echo $result->dns->returnPathHost;   // Return path CNAME host
echo $result->dns->returnPathValue;  // Return path CNAME value

if ($result->dns->dkim !== null) {
    echo $result->dns->dkim->selector;   // DKIM selector
    echo $result->dns->dkim->publicKey;  // DKIM public key
}
```

### Get Domain Details

```php
$domain = $lettr->domains()->get('example.com');

echo $domain->domain;
echo $domain->status;
echo $domain->canSend;
echo $domain->trackingDomain;
echo $domain->createdAt;
echo $domain->verifiedAt;

// DNS configuration
echo $domain->dns->returnPathHost;
echo $domain->dns->returnPathValue;
```

### Verify Domain DNS

```php
$verification = $lettr->domains()->verify('example.com');

if ($verification->isFullyVerified()) {
    echo "Domain is ready to send!";
} else {
    // Check individual records
    if (!$verification->dkim->isValid()) {
        echo "DKIM error: " . $verification->dkim->error;
        echo "Expected: " . $verification->dkim->expected;
        echo "Found: " . $verification->dkim->found;
    }

    if (!$verification->returnPath->isValid()) {
        echo "Return path error: " . $verification->returnPath->error;
    }
}
```

### Delete a Domain

```php
$lettr->domains()->delete('example.com');
```

## Webhooks

### List Webhooks

```php
$webhooks = $lettr->webhooks()->list();

foreach ($webhooks as $webhook) {
    echo $webhook->id;
    echo $webhook->name;
    echo $webhook->url;
    echo $webhook->enabled;
    echo $webhook->authType->value;  // 'none', 'basic', 'bearer'

    // Event types this webhook listens to
    foreach ($webhook->eventTypes as $eventType) {
        echo $eventType->value;  // 'delivery', 'bounce', 'open', etc.
    }

    // Health check
    if ($webhook->isFailing()) {
        echo "Last error: " . $webhook->lastError;
    }
}
```

### Get Webhook Details

```php
$webhook = $lettr->webhooks()->get('webhook-id');

echo $webhook->name;
echo $webhook->url;
echo $webhook->lastStatus?->value;
echo $webhook->lastTriggeredAt;

// Check if webhook listens to specific events
if ($webhook->listensTo(EventType::Bounce)) {
    echo "Webhook receives bounce notifications";
}
```

## Event Types

The SDK provides an `EventType` enum with helper methods:

```php
use Lettr\Enums\EventType;

$type = EventType::Delivery;

$type->label();        // "Delivery"
$type->isSuccess();    // true (injection, delivery)
$type->isFailure();    // false (bounce, policy_rejection, etc.)
$type->isEngagement(); // false (open, initial_open, click)
$type->isUnsubscribe(); // false (list_unsubscribe, link_unsubscribe)
```

Available event types:
- `injection` - Email accepted for delivery
- `delivery` - Email delivered to recipient
- `bounce` - Email bounced
- `delay` - Delivery delayed
- `policy_rejection` - Rejected by policy
- `out_of_band` - Out of band bounce
- `open` - Email opened
- `initial_open` - First open
- `click` - Link clicked
- `generation_failure` - Template generation failed
- `generation_rejection` - Template generation rejected
- `spam_complaint` - Marked as spam
- `list_unsubscribe` - Unsubscribed via list header
- `link_unsubscribe` - Unsubscribed via link

## Value Objects

The SDK uses value objects for type safety and validation:

```php
use Lettr\ValueObjects\EmailAddress;
use Lettr\ValueObjects\DomainName;
use Lettr\ValueObjects\RequestId;
use Lettr\ValueObjects\Timestamp;

// Email addresses with optional name
$email = new EmailAddress('user@example.com', 'User Name');
echo $email->address;  // user@example.com
echo $email->name;     // User Name

// Domain names (validated)
$domain = new DomainName('example.com');

// Request IDs
$requestId = new RequestId('req_abc123');

// Timestamps
$timestamp = Timestamp::fromString('2024-01-15T10:30:00Z');
echo $timestamp->toIso8601();
echo $timestamp->toDateTime();
```

## Error Handling

```php
use Lettr\Exceptions\ApiException;
use Lettr\Exceptions\TransporterException;
use Lettr\Exceptions\ValidationException;
use Lettr\Exceptions\NotFoundException;
use Lettr\Exceptions\UnauthorizedException;
use Lettr\Exceptions\ConflictException;
use Lettr\Exceptions\InvalidValueException;

try {
    $response = $lettr->emails()->send($email);
} catch (ValidationException $e) {
    // Invalid request data (422)
    echo "Validation failed: " . $e->getMessage();
} catch (UnauthorizedException $e) {
    // Invalid API key (401)
    echo "Authentication failed: " . $e->getMessage();
} catch (NotFoundException $e) {
    // Resource not found (404)
    echo "Not found: " . $e->getMessage();
} catch (ConflictException $e) {
    // Resource conflict (409)
    echo "Conflict: " . $e->getMessage();
} catch (ApiException $e) {
    // Other API errors
    echo "API error ({$e->getCode()}): " . $e->getMessage();
} catch (TransporterException $e) {
    // Network/transport errors
    echo "Network error: " . $e->getMessage();
} catch (InvalidValueException $e) {
    // Invalid value object (e.g., invalid email format)
    echo "Invalid value: " . $e->getMessage();
}
```

## Development

### Install Dependencies

```bash
composer install
```

### Code Style

This project uses Laravel Pint for code style:

```bash
composer lint
```

### Static Analysis

This project uses PHPStan at level 8:

```bash
composer analyse
```

### Testing

This project uses Pest for testing:

```bash
composer test
```

## License

MIT License. See [LICENSE](LICENSE) for details.
