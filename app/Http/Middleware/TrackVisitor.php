<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Visitor;
use App\Models\VisitorStat;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the real IP from X-Forwarded-For or fall back to remote IP
        $ip = $this->getRealIp($request);

        $date = Carbon::today()->toDateString();
        $cacheKey = "visited:{$ip}:{$date}";

        if (!Cache::has($cacheKey)) {
            $alreadyVisited = Visitor::where('ip_address', $ip)
                ->where('visit_date', $date)
                ->exists();

            if (!$alreadyVisited) {
                Visitor::create([
                    'ip_address' => $ip,
                    'visit_date' => $date,
                ]);

                if (VisitorStat::count() === 0) {
                    VisitorStat::create(['total_count' => 1]);
                } else {
                    VisitorStat::query()->increment('total_count');
                }

                Cache::put($cacheKey, true, now()->addHours(24));
            }
        }

        return $next($request);
    }

    /**
     * Get the client's real IP address behind a proxy.
     */
    private function getRealIp(Request $request): string
    {
        $forwarded = $request->header('X-Forwarded-For');

        if ($forwarded) {
            $ips = explode(',', $forwarded);
            return trim($ips[0]); // First IP is the real client
        }

        return $request->ip(); // fallback
    }
}
