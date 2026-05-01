<?php

namespace App\Livewire\Pulse;

use App\Models\User;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

#[Lazy]
class TrafficOverview extends Card
{
    public function render()
    {
        $sampleRate = (float) config(
            'pulse.recorders.'.\App\Pulse\Recorders\IncomingTrafficRecorder::class.'.sample_rate',
            1
        );

        $byIp = $this->aggregate('traffic_ip', ['count'], 'count', 'desc', 40);
        $byIpUser = $this->aggregate('traffic_ip_user', ['count'], 'count', 'desc', 40);
        $flags = $this->aggregate('traffic_flag', ['count'], 'count', 'desc', 20);

        $userIds = $byIpUser
            ->map(function ($row) {
                try {
                    $decoded = json_decode($row->key, true, 512, JSON_THROW_ON_ERROR);

                    return isset($decoded['user_id']) && (int) $decoded['user_id'] > 0
                        ? (int) $decoded['user_id']
                        : null;
                } catch (\JsonException) {
                    return null;
                }
            })
            ->filter()
            ->unique()
            ->values();

        $emailsById = User::query()
            ->whereIn('id', $userIds)
            ->pluck('email', 'id');

        $byIpUser = $byIpUser->map(function ($row) use ($emailsById) {
            try {
                $decoded = json_decode($row->key, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                return (object) [
                    'ip' => $row->key,
                    'user_label' => '—',
                    'count' => $row->count,
                ];
            }

            $ip = (string) ($decoded['ip'] ?? '');
            $uid = (int) ($decoded['user_id'] ?? 0);
            $label = $uid > 0
                ? ($emailsById->get($uid) ?? '#'.$uid)
                : __('admin.pulse.guest_label');

            return (object) [
                'ip' => $ip,
                'user_label' => $label,
                'count' => $row->count,
            ];
        });

        return view('livewire.pulse.traffic-overview', [
            'byIp' => $byIp,
            'byIpUser' => $byIpUser,
            'flags' => $flags,
            'trafficSampleRate' => $sampleRate,
        ]);
    }
}
