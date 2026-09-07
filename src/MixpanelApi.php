<?php

namespace JeffersonGoncalves\MixpanelApi;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

/**
 * Thin wrapper around Laravel's Http client for Mixpanel's HTTP APIs:
 * ingestion (track/engage), query (insights/funnels/retention) and raw
 * event export.
 */
class MixpanelApi
{
    protected const INGESTION_URL = 'https://api.mixpanel.com';

    protected const QUERY_URL = 'https://mixpanel.com/api/2.0';

    protected const EXPORT_URL = 'https://data.mixpanel.com/api/2.0';

    public function __construct(
        protected ?string $token = null,
        protected ?string $apiKey = null,
        protected ?string $secret = null,
        protected ?string $projectId = null,
    ) {}

    /** @param array<string, mixed> $properties */
    public function trackEvent(string $event, string $distinctId, array $properties = []): array
    {
        $this->ensureToken();

        $response = Http::asJson()->post(self::INGESTION_URL.'/track', [[
            'event' => $event,
            'properties' => [...$properties, 'token' => $this->token, 'distinct_id' => $distinctId],
        ]]);

        return (array) ($response->json() ?? []);
    }

    /** @param array<string, mixed> $properties */
    public function setProfile(string $distinctId, array $properties = []): array
    {
        $this->ensureToken();

        $response = Http::asJson()->post(self::INGESTION_URL.'/engage', [[
            '$token' => $this->token,
            '$distinct_id' => $distinctId,
            '$set' => $properties,
        ]]);

        return (array) ($response->json() ?? []);
    }

    public function queryEvents(string $event = 'all', ?string $fromDate = null, ?string $toDate = null): array
    {
        $this->ensureQueryCredentials(requireProjectId: true);

        $response = Http::withBasicAuth($this->apiKey, $this->secret)
            ->post(self::QUERY_URL.'/insights', [
                'project_id' => $this->projectId,
                'events' => [['event' => $event]],
                'time_range' => [
                    'from_date' => $fromDate ?? now()->subDays(30)->toDateString(),
                    'to_date' => $toDate ?? now()->toDateString(),
                ],
            ]);

        return (array) ($response->json() ?? []);
    }

    public function getFunnel(string $funnelId, ?string $fromDate = null, ?string $toDate = null): array
    {
        $this->ensureQueryCredentials();

        $response = Http::withBasicAuth($this->apiKey, $this->secret)
            ->get(self::QUERY_URL.'/funnels', array_filter([
                'funnel_id' => $funnelId,
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ]));

        return (array) ($response->json() ?? []);
    }

    public function getRetention(string $fromDate, string $toDate, ?string $bornEvent = null): array
    {
        $this->ensureQueryCredentials();

        $response = Http::withBasicAuth($this->apiKey, $this->secret)
            ->get(self::QUERY_URL.'/retention', array_filter([
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'retention_type' => 'birth',
                'born_event' => $bornEvent,
            ]));

        return (array) ($response->json() ?? []);
    }

    /**
     * Returns the raw response body: Mixpanel's export endpoint streams
     * newline-delimited JSON, not a single JSON document, so parsing is
     * left to the caller (e.g. line-by-line `json_decode`).
     */
    public function exportEvents(string $fromDate, string $toDate, ?string $event = null): string
    {
        $this->ensureQueryCredentials();

        $query = array_filter([
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'event' => $event !== null ? json_encode([$event]) : null,
        ]);

        return Http::withBasicAuth($this->apiKey, $this->secret)
            ->get(self::EXPORT_URL.'/export', $query)
            ->body();
    }

    protected function ensureToken(): void
    {
        if (blank($this->token)) {
            throw new InvalidArgumentException('Mixpanel token is required for this operation.');
        }
    }

    protected function ensureQueryCredentials(bool $requireProjectId = false): void
    {
        if (blank($this->apiKey) || blank($this->secret)) {
            throw new InvalidArgumentException('Mixpanel API key and secret are required for this operation.');
        }

        if ($requireProjectId && blank($this->projectId)) {
            throw new InvalidArgumentException('Mixpanel project ID is required for this operation.');
        }
    }
}
