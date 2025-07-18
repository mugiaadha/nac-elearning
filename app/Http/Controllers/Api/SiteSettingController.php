<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Exception;

class SiteSettingController extends BaseController
{
    /**
     * Get site settings data
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cacheKey = $this->generateCacheKey('site_settings_all');

        return $this->cacheOrExecute(
            $cacheKey,
            function () {
                $setting = SiteSetting::select([
                    'logo',
                    'phone',
                    'email',
                    'address',
                    'facebook',
                    'twitter',
                    'copyright'
                ])->first();

                if (!$setting) {
                    throw new Exception('Data pengaturan situs tidak ditemukan');
                }

                return $setting;
            },
            1440, // Cache for 24 hours
            'Data pengaturan situs berhasil diambil',
            'Getting site settings data'
        );
    }

    /**
     * Clear site settings cache (for admin use)
     *
     * @return \Illuminate\Http\Response
     */
    public function clearSiteSettingsCache()
    {
        try {
            // Clear by tags if supported
            $cleared = $this->clearCacheByTags(['site_settings']);

            // Clear specific key as fallback
            $cacheKey = $this->generateCacheKey('site_settings_all');
            $keyCleared = $this->clearCache($cacheKey);

            return $this->sendResponse([
                'tags_cleared' => $cleared,
                'key_cleared' => $keyCleared
            ], 'Cache berhasil dibersihkan');
        } catch (Exception $e) {
            return $this->handleException($e, 'Clearing site settings cache');
        }
    }
}
