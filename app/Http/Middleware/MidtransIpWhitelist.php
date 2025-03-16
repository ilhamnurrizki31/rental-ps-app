<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransIpWhitelist
{
    // Midtrans IPs (CIDR-supported)
    protected array $sandboxIps = [
        '34.101.68.130',
        '34.101.92.69',
        '34.142.147.133/32',
        '34.142.169.131/32',
        '34.142.231.22/32',
        '35.240.161.215/32',
        '34.142.227.232/32',
        '34.124.184.175/32',
        '35.197.130.2/32',
        '34.142.233.114/32',
    ];
    protected array $productionIps = [
        '103.208.23.0/24',
        "103.208.23.6/32",
        "103.127.16.0/23",
        "103.127.17.6/32",
        "34.87.92.33",
        "34.87.59.67",
        "35.186.147.251",
        "34.87.157.231",
        "13.228.166.126/32",
        "52.220.80.5/32",
        "3.1.123.95/32",
        "108.136.204.114",
        "108.136.34.95",
        "108.137.159.245",
        "108.137.135.225",
        "16.78.53.66",
        "43.218.2.230",
        "16.78.88.149",
        "16.78.85.64",
        "16.78.69.49",
        "16.78.98.130",
        "16.78.9.40",
        "43.218.223.26",
        "13.228.166.126/32",
        "52.220.80.5/32",
        "3.1.123.95/32",
    ];

    public function handle(Request $request, Closure $next)
    {
        //
        $env = config('app.midtrans_env', 'sandbox');
        $allowedIps = $env === 'production' ? $this->productionIps : $this->sandboxIps;

        // Get an array of IPs (client IP + Forwarded IPs)
        $clientIps = $this->getClientIps($request);

        if (!$this->isIpAllowed($clientIps, $allowedIps)) {
            $this->logUnauthorizedAccess($request, $clientIps);
            abort(403, 'Unauthorized: Your IP is not whitelisted.');
        }

        return $next($request);
    }

    private function getClientIps(Request $request): array
    {
        $ips = [$request->ip()];

        // Extract X-Forwarded-For IPs //check x-forward prod
        $forwarded = $request->header('x-forwarded-for');
        if ($forwarded) {
            $ips = array_merge($ips, array_map('trim', explode(',', $forwarded)));
        }

        return array_unique(array_filter($ips));
    }

    private function isIpAllowed(array $ips, array $allowedIps): bool
    {
        return !empty(array_filter($ips, fn($ip) => !empty(array_filter($allowedIps, fn($allowedIp) => $this->ipMatches($ip, $allowedIp)))));
    }

    private function ipMatches(string $ip, string $allowedIp): bool
    {
        return strpos($allowedIp, '/') !== false ? $this->cidrMatch($ip, $allowedIp) : $ip === $allowedIp;
    }

    private function cidrMatch(string $ip, string $cidr): bool
    {
        list($subnet, $bits) = explode('/', $cidr);
        $subnetInt = ip2long($subnet);
        $ipInt = ip2long($ip);
        $mask = -1 << (32 - (int) $bits);

        return ($ipInt & $mask) === ($subnetInt & $mask);
    }

    private function logUnauthorizedAccess(Request $request, array $clientIps): void
    {
        Log::warning('Unauthorized access attempt detected!', [
            'Client IPs' => $clientIps,
            'User-Agent' => $request->header('User-Agent'),
            'Method' => $request->method(),
            'URL' => $request->fullUrl(),
            'Headers' => $request->headers->all(),
            'Query Parameters' => $request->query(),
            'Body' => $request->all(),
            'Server' => $request->server(),
            'Cookies' => $request->cookie(),
            'Files' => $request->allFiles(),
        ]);
    }
}