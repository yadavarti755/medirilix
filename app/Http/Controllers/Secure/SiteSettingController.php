<?php

namespace App\Http\Controllers\Secure;

use App\DTO\SiteSettingDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSiteSettingRequest;
use App\Models\SiteSetting;
use App\Services\CurrencyService;
use App\Services\SiteSettingService;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    protected $currencyService;
    protected $siteSettingService;

    public function __construct()
    {
        $this->currencyService = new CurrencyService();
        $this->siteSettingService = new SiteSettingService();
    }

    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageTitle = 'Site Settings';
        $settings = $this->siteSettingService->findFirst();
        $currencies = $this->currencyService->findAll();
        return view('secure.site_settings.index', compact('pageTitle', 'settings', 'currencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(SiteSetting $siteSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SiteSetting $siteSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSiteSettingRequest $request, SiteSetting $siteSetting)
    {
        try {
            $siteSettingDto = new SiteSettingDto(
                strip_tags($request->input('site_name')),
                strip_tags($request->input('site_tag_line')),
                strip_tags($request->input('seo_keywords')),
                strip_tags($request->input('seo_description')),
                $request->hasFile('header_logo') ? $request->file('header_logo') : null,
                $request->hasFile('footer_logo') ? $request->file('footer_logo') : null,
                $request->hasFile('favicon') ? $request->file('favicon') : null,
                $request->hasFile('admin_panel_logo') ? $request->file('admin_panel_logo') : null,
                strip_tags($request->input('copyright_text')),
                strip_tags($request->input('maintained_by_text')),
                strip_tags($request->input('accessibility_text')),
                strip_tags($request->input('footer_about_us')),
                $siteSetting->created_by,
                auth()->user()->id,
                $request->input('currency_id')
            );

            $siteSetting = $this->siteSettingService->update($siteSettingDto, $siteSetting->id);

            if (!$siteSetting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error while updating site setting.',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Site setting updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SiteSetting $siteSetting)
    {
        //
    }
}
