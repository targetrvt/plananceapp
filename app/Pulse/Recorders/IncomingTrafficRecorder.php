<?php

namespace App\Pulse\Recorders;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Carbon;
use Laravel\Pulse\Concerns\ConfiguresAfterResolving;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns\Ignores;
use Laravel\Pulse\Recorders\Concerns\LivewireRoutes;
use Laravel\Pulse\Recorders\Concerns\Sampling;
use Symfony\Component\HttpFoundation\Response;

/**
 * Aggregates client IP, IP+user pairs, and lightweight request “flags” for Pulse.
 */
class IncomingTrafficRecorder
{
    use ConfiguresAfterResolving;
    use Ignores;
    use LivewireRoutes;
    use Sampling;

    public function __construct(
        protected Pulse $pulse,
    ) {
        //
    }

    public function register(callable $record, Application $app): void
    {
        $this->afterResolving(
            $app,
            Kernel::class,
            fn (Kernel $kernel) => $kernel->whenRequestLifecycleIsLongerThan(-1, $record) // @phpstan-ignore method.notFound
        );
    }

    public function record(Carbon $startedAt, Request $request, Response $response): void
    {
        if (! $request->route() instanceof Route || ! $this->shouldSample()) {
            return;
        }

        [$path] = $this->resolveRoutePath($request);

        if ($this->shouldIgnore($path)) {
            return;
        }

        $ip = (string) ($request->ip() ?? '');

        if ($ip === '') {
            return;
        }

        $this->pulse->record(
            type: 'traffic_ip',
            key: $ip,
            timestamp: $startedAt,
        )->count();

        $userId = $this->pulse->resolveAuthenticatedUserId();
        $this->pulse->record(
            type: 'traffic_ip_user',
            key: json_encode(['ip' => $ip, 'user_id' => $userId ?? 0], JSON_THROW_ON_ERROR),
            timestamp: $startedAt,
        )->count();

        foreach ($this->requestFlags($request) as $flag) {
            $this->pulse->record(
                type: 'traffic_flag',
                key: $flag,
                timestamp: $startedAt,
            )->count();
        }
    }

    /**
     * @return list<string>
     */
    private function requestFlags(Request $request): array
    {
        $flags = [];

        if ($request->secure()) {
            $flags[] = 'https';
        }

        if ($request->ajax()) {
            $flags[] = 'ajax';
        }

        if ($request->expectsJson()) {
            $flags[] = 'expects_json';
        }

        if ($request->prefetch()) {
            $flags[] = 'prefetch';
        }

        if ($request->headers->has('X-Livewire')) {
            $flags[] = 'livewire';
        }

        if ($request->pjax()) {
            $flags[] = 'pjax';
        }

        return $flags;
    }
}
