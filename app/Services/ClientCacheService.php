<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Cache;

class ClientCacheService
{
    const CACHE_KEY = 'clients_for_select';
    const CACHE_DURATION = 60 * 60 * 24; // 24 hours

    /**
     * Get cached clients for select options
     * Returns array of ['id' => 'name'] pairs
     */
    public static function getClientsForSelect(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            return Client::orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
        });
    }

    /**
     * Get cached clients as collection for more complex operations
     */
    public static function getClientsCollection()
    {
        return Cache::remember(self::CACHE_KEY . '_collection', self::CACHE_DURATION, function () {
            return Client::select('id', 'name', 'contact_person')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get cached clients with user relationship for forms that need user data
     */
    public static function getClientsWithUser()
    {
        return Cache::remember(self::CACHE_KEY . '_with_user', self::CACHE_DURATION, function () {
            return Client::with('user:id,name')
                ->select('id', 'name','company_name', 'contact_person', 'user_id')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Clear the client cache
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_KEY . '_collection');
        Cache::forget(self::CACHE_KEY . '_with_user');
    }

    /**
     * Refresh the client cache
     */
    public static function refreshCache(): array
    {
        self::clearCache();
        return self::getClientsForSelect();
    }
}
