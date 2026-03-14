<?php

namespace App\Services;

use App\Repositories\SiteSettingRepository;
use App\DTO\SiteSettingDto;
use App\Traits\FileUploadTraits;
use Illuminate\Support\Facades\Config;

class SiteSettingService
{
    use FileUploadTraits;
    private $siteSettingRepository;

    public function __construct()
    {
        $this->siteSettingRepository = new SiteSettingRepository();
    }

    public function findFirst()
    {
        return $this->siteSettingRepository->findFirst();
    }

    public function findById($id)
    {
        return $this->siteSettingRepository->findById($id);
    }

    public function update(SiteSettingDto $siteSettingDto, $id)
    {
        // Upload header logo
        if ($siteSettingDto->header_logo) {
            $headerFile = $this->uploadFile($siteSettingDto->header_logo, Config::get('file_paths')['SITE_HEADER_LOGO_PATH']);
            $siteSettingDto->header_logo = $headerFile['file_name'];
        }

        // Upload footer logo
        if ($siteSettingDto->footer_logo) {
            $footerFile = $this->uploadFile($siteSettingDto->footer_logo, Config::get('file_paths')['SITE_FOOTER_LOGO_PATH']);
            $siteSettingDto->footer_logo = $footerFile['file_name'];
        }

        // Upload favicon logo
        if ($siteSettingDto->favicon) {
            $faviconFile = $this->uploadFile($siteSettingDto->favicon, Config::get('file_paths')['SITE_FAVICON_PATH']);
            $siteSettingDto->favicon = $faviconFile['file_name'];
        }

        // Upload admin panel logo
        if ($siteSettingDto->admin_panel_logo) {
            $adminLogoFile = $this->uploadFile($siteSettingDto->admin_panel_logo, Config::get('file_paths')['SITE_ADMIN_PANEL_LOGO_PATH']);
            $siteSettingDto->admin_panel_logo = $adminLogoFile['file_name'];
        }

        $updateData = [
            'site_name' => $siteSettingDto->site_name,
            'site_tag_line' => $siteSettingDto->site_tag_line,
            'seo_keywords' => $siteSettingDto->seo_keywords,
            'seo_description' => $siteSettingDto->seo_description,
            'copyright_text' => $siteSettingDto->copyright_text,
            'maintained_by_text' => $siteSettingDto->maintained_by_text,
            'accessibility_text' => $siteSettingDto->accessibility_text,
            'footer_about_us' => $siteSettingDto->footer_about_us,
            'created_by' => $siteSettingDto->created_by,
            'updated_by' => $siteSettingDto->updated_by,
            'currency_id' => $siteSettingDto->currency_id,
        ];

        if ($siteSettingDto->header_logo) {
            $updateData['header_logo'] = $siteSettingDto->header_logo;
        }
        if ($siteSettingDto->footer_logo) {
            $updateData['footer_logo'] = $siteSettingDto->footer_logo;
        }
        if ($siteSettingDto->favicon) {
            $updateData['favicon'] = $siteSettingDto->favicon;
        }
        if ($siteSettingDto->admin_panel_logo) {
            $updateData['admin_panel_logo'] = $siteSettingDto->admin_panel_logo;
        }

        $siteSetting = $this->siteSettingRepository->update($updateData, $id);

        if (!$siteSetting) {
            return false;
        }

        return $siteSetting;
    }
}
