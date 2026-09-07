<div class="filament-hidden">

![Laravel Mixpanel API](https://raw.githubusercontent.com/jeffersongoncalves/laravel-mixpanel-api/main/art/jeffersongoncalves-laravel-mixpanel-api.png)

</div>

# Laravel Mixpanel API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-mixpanel-api.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-mixpanel-api)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-mixpanel-api/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jeffersongoncalves/laravel-mixpanel-api/actions?query=workflow%3ATests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-mixpanel-api/pint.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/laravel-mixpanel-api/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-mixpanel-api.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-mixpanel-api)
[![License](https://img.shields.io/packagist/l/jeffersongoncalves/laravel-mixpanel-api.svg?style=flat-square)](LICENSE.md)

A Laravel client for [Mixpanel](https://mixpanel.com)'s **HTTP API**: track events, update user profiles, and query insights, funnels, retention and raw event exports through a simple, typed API built on Laravel's `Http` client.

This is a server-side API client only. If you need the Mixpanel JS SDK / Blade view integration instead, see [jeffersongoncalves/laravel-mixpanel](https://github.com/jeffersongoncalves/laravel-mixpanel).

## Features

- Ingestion: `trackEvent`, `setProfile`
- Query API: `queryEvents`, `getFunnel`, `getRetention`
- Export API: `exportEvents` (raw newline-delimited JSON)
- Throws `InvalidArgumentException` when required credentials for a call are missing

## Installation

You can install the package via composer:

```bash
composer require jeffersongoncalves/laravel-mixpanel-api
```

Publish the config file:

```bash
php artisan vendor:publish --tag=mixpanel-api-config
```

Set your Mixpanel credentials in `.env`:

```env
MIXPANEL_TOKEN=your-project-token
MIXPANEL_API_KEY=your-api-key
MIXPANEL_SECRET=your-api-secret
MIXPANEL_PROJECT_ID=your-project-id
```

The project token is used for ingestion (`trackEvent`, `setProfile`). The API key, secret and project ID are used for the query/export endpoints (`queryEvents`, `getFunnel`, `getRetention`, `exportEvents`). Find them under **Project Settings > Access Keys**.

## Configuration

```php
// config/mixpanel-api.php
return [
    'token' => env('MIXPANEL_TOKEN', ''),
    'api_key' => env('MIXPANEL_API_KEY', ''),
    'secret' => env('MIXPANEL_SECRET', ''),
    'project_id' => env('MIXPANEL_PROJECT_ID', ''),
];
```

## Usage

The package is resolved via the `MixpanelApi` facade or by injecting `JeffersonGoncalves\MixpanelApi\MixpanelApi`.

### Track an event

```php
use JeffersonGoncalves\MixpanelApi\Facades\MixpanelApi;

MixpanelApi::trackEvent('signup', $user->id, [
    'plan' => 'pro',
]);
```

### Update a user profile

```php
MixpanelApi::setProfile($user->id, [
    '$email' => $user->email,
    '$name' => $user->name,
]);
```

### Query events (Insights)

```php
$insights = MixpanelApi::queryEvents('purchase', '2026-08-01', '2026-08-31');
```

`fromDate`/`toDate` default to the last 30 days when omitted.

### Get a funnel

```php
$funnel = MixpanelApi::getFunnel('42', '2026-08-01', '2026-08-31');
```

### Get retention data

```php
$retention = MixpanelApi::getRetention('2026-08-01', '2026-08-31', 'signup');
```

### Export raw events

```php
$ndjson = MixpanelApi::exportEvents('2026-08-01', '2026-08-31', 'signup');

foreach (explode(PHP_EOL, trim($ndjson)) as $line) {
    $event = json_decode($line, true);
}
```

The export endpoint returns newline-delimited JSON (one event per line), not a single JSON document, so the raw response body is returned for the caller to parse.

### Error handling

Calling a method without the credentials it requires throws `InvalidArgumentException`:

```php
try {
    MixpanelApi::trackEvent('signup', $user->id);
} catch (InvalidArgumentException $e) {
    logger()->error($e->getMessage());
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome. Please open an issue or pull request on GitHub.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jefferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
