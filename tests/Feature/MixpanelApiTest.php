<?php

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\MixpanelApi\Facades\MixpanelApi;
use JeffersonGoncalves\MixpanelApi\MixpanelApi as MixpanelApiManager;

it('tracks an event', function () {
    Http::fake(['api.mixpanel.com/*' => Http::response(['status' => 1])]);

    $result = MixpanelApi::trackEvent('signup', '123', ['plan' => 'pro']);

    expect($result['status'])->toBe(1);
    Http::assertSent(function ($request) {
        $body = $request->data()[0];

        return str_contains($request->url(), 'api.mixpanel.com/track')
            && $body['event'] === 'signup'
            && $body['properties']['plan'] === 'pro'
            && $body['properties']['token'] === 'test-token'
            && $body['properties']['distinct_id'] === '123';
    });
});

it('throws when tracking an event without a token', function () {
    app('config')->set('mixpanel-api.token', null);
    app()->forgetInstance(MixpanelApiManager::class);

    expect(fn () => MixpanelApi::trackEvent('signup', '123'))
        ->toThrow(InvalidArgumentException::class, 'Mixpanel token is required for this operation.');
});

it('sets a user profile', function () {
    Http::fake(['api.mixpanel.com/*' => Http::response(['status' => 1])]);

    $result = MixpanelApi::setProfile('123', ['plan' => 'pro']);

    expect($result['status'])->toBe(1);
    Http::assertSent(function ($request) {
        $body = $request->data()[0];

        return str_contains($request->url(), 'api.mixpanel.com/engage')
            && $body['$distinct_id'] === '123'
            && $body['$set']['plan'] === 'pro'
            && $body['$token'] === 'test-token';
    });
});

it('throws when setting a profile without a token', function () {
    app('config')->set('mixpanel-api.token', null);
    app()->forgetInstance(MixpanelApiManager::class);

    expect(fn () => MixpanelApi::setProfile('123'))
        ->toThrow(InvalidArgumentException::class, 'Mixpanel token is required for this operation.');
});

it('queries events', function () {
    Http::fake(['mixpanel.com/api/2.0/insights*' => Http::response(['series' => []])]);

    $result = MixpanelApi::queryEvents('purchase', '2026-08-01', '2026-08-31');

    expect($result)->toHaveKey('series');
    Http::assertSent(fn ($request) => str_contains($request->url(), 'mixpanel.com/api/2.0/insights')
        && $request['project_id'] === '12345'
        && $request['events'][0]['event'] === 'purchase'
        && $request['time_range']['from_date'] === '2026-08-01'
        && $request['time_range']['to_date'] === '2026-08-31');
});

it('throws when querying events without a project id', function () {
    app('config')->set('mixpanel-api.project_id', null);
    app()->forgetInstance(MixpanelApiManager::class);

    expect(fn () => MixpanelApi::queryEvents())
        ->toThrow(InvalidArgumentException::class, 'Mixpanel project ID is required for this operation.');
});

it('gets a funnel', function () {
    Http::fake(['mixpanel.com/api/2.0/funnels*' => Http::response(['meta' => []])]);

    $result = MixpanelApi::getFunnel('42', '2026-08-01', '2026-08-31');

    expect($result)->toHaveKey('meta');
    Http::assertSent(fn ($request) => str_contains($request->url(), 'funnel_id=42')
        && str_contains($request->url(), 'from_date=2026-08-01')
        && str_contains($request->url(), 'to_date=2026-08-31'));
});

it('throws when getting a funnel without credentials', function () {
    app('config')->set('mixpanel-api.api_key', null);
    app()->forgetInstance(MixpanelApiManager::class);

    expect(fn () => MixpanelApi::getFunnel('42'))
        ->toThrow(InvalidArgumentException::class, 'Mixpanel API key and secret are required for this operation.');
});

it('gets retention data', function () {
    Http::fake(['mixpanel.com/api/2.0/retention*' => Http::response(['data' => []])]);

    $result = MixpanelApi::getRetention('2026-08-01', '2026-08-31', 'signup');

    expect($result)->toHaveKey('data');
    Http::assertSent(fn ($request) => str_contains($request->url(), 'retention_type=birth')
        && str_contains($request->url(), 'born_event=signup'));
});

it('throws when getting retention data without credentials', function () {
    app('config')->set('mixpanel-api.secret', null);
    app()->forgetInstance(MixpanelApiManager::class);

    expect(fn () => MixpanelApi::getRetention('2026-08-01', '2026-08-31'))
        ->toThrow(InvalidArgumentException::class, 'Mixpanel API key and secret are required for this operation.');
});

it('exports raw events as a newline-delimited JSON string', function () {
    $ndjson = json_encode(['event' => 'signup']).PHP_EOL.json_encode(['event' => 'purchase']);
    Http::fake(['data.mixpanel.com/api/2.0/export*' => Http::response($ndjson)]);

    $result = MixpanelApi::exportEvents('2026-08-01', '2026-08-31', 'signup');

    expect($result)->toBe($ndjson);
    Http::assertSent(fn ($request) => str_contains($request->url(), 'data.mixpanel.com/api/2.0/export')
        && str_contains($request->url(), 'event=%5B%22signup%22%5D'));
});

it('throws when exporting events without credentials', function () {
    app('config')->set('mixpanel-api.api_key', null);
    app('config')->set('mixpanel-api.secret', null);
    app()->forgetInstance(MixpanelApiManager::class);

    expect(fn () => MixpanelApi::exportEvents('2026-08-01', '2026-08-31'))
        ->toThrow(InvalidArgumentException::class, 'Mixpanel API key and secret are required for this operation.');
});
