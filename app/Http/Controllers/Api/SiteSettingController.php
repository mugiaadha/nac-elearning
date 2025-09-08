<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\SiteSetting;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class SiteSettingController extends BaseController
{
    /**
     * Kirim email dari user ke email NAC yang disetting di SiteSettings
     */
    public function sendContactEmail(Request $request)
    {
        return 'test';
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'message' => 'required|string|max:1000',
            'subject' => 'required|string|max:1000',
        ]);

        $setting = SiteSetting::first();
        $toEmail = $setting?->email ?? config('mail.from.address');

        Mail::to($toEmail)->send(new ContactMail(
            $request->name,
            $request->email,
            $request->message,
            $request->subject
        ));

        return $this->sendResponse(null, 'Pesan berhasil dikirim ke NAC Tax Center.');
    }
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

                // Tambahkan base URL pada logo jika ada
                if ($setting->logo) {
                    $setting->logo = url(ltrim($setting->logo, '/'));
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

    protected function clearAllCache()
    {
        try {
            Artisan::call('cache:clear');
            return true;
        } catch (Exception $e) {
            Log::error('Global cache clear failed: ' . $e->getMessage());
            return false;
        }
    }
}
