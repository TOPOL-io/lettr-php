# Lettr PHP

A PHP client library for the [Lettr](https://app.uselettr.com) email API.

## Requirements

- PHP 8.4 or higher
- Guzzle HTTP client

## Installation

```bash
composer require lettr/lettr-php
```

## Usage

### Basic Usage

```php
use Lettr\Lettr;
use Lettr\Dto\SendEmailData;

// Create client with your API key
$lettr = Lettr::client('your-api-key');

// Send an email using DTO
$email = new SendEmailData(
    from: 'sender@example.com',
    to: ['recipient@example.com'],
    subject: 'Hello from Lettr',
    text: 'Plain text body',
    html: '<p>HTML body</p>',
);

$response = $lettr->emails()->send($email);

echo $response->id; // The email ID
```

### Using Array Syntax

You can also create the DTO from an array:

```php
use Lettr\Lettr;
use Lettr\Dto\SendEmailData;

$lettr = Lettr::client('your-api-key');

$email = SendEmailData::from([
    'from' => 'sender@example.com',
    'to' => ['recipient@example.com'],
    'subject' => 'Hello from Lettr',
    'text' => 'Plain text body',
    'html' => '<p>HTML body</p>',
]);

$response = $lettr->emails()->send($email);
```

### Property Access

You can access the emails service via method or property:

```php
// Via method
$response = $lettr->emails()->send($email);

// Via property
$response = $lettr->emails->send($email);
```

### Custom Base URL

If you need to use a different API endpoint:

```php
$lettr = Lettr::client('your-api-key', 'https://custom-api.example.com');
```

## API Reference

### SendEmailData

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `from` | string | Yes | Sender email address |
| `to` | array | Yes | Array of recipient email addresses |
| `subject` | string | Yes | Email subject |
| `text` | string | No | Plain text body |
| `html` | string | No | HTML body |

### EmailResponse

| Property | Type | Description |
|----------|------|-------------|
| `id` | string | Unique identifier for the sent email |

## Error Handling

The client throws exceptions for API errors:

```php
use Lettr\Lettr;
use Lettr\Exceptions\ApiException;
use Lettr\Exceptions\TransporterException;

try {
    $response = $lettr->emails()->send($email);
} catch (ApiException $e) {
    // API returned an error response
    echo $e->getMessage();
} catch (TransporterException $e) {
    // Network or transport error
    echo $e->getMessage();
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

MIT License. See [LICENSE](LICENSE) for more information.

