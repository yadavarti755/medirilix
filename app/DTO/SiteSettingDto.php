<?php

namespace App\DTO;

class SiteSettingDto
{
    public $site_name;
    public $site_tag_line;
    public $seo_keywords;
    public $seo_description;
    public $header_logo;
    public $footer_logo;
    public $favicon;
    public $admin_panel_logo;
    public $copyright_text;
    public $maintained_by_text;
    public $accessibility_text;
    public $footer_about_us;
    public $created_by;
    public $updated_by;

    public function __construct(
        $site_name,
        $site_tag_line = '',
        $seo_keywords = '',
        $seo_description = '',
        $header_logo = '',
        $footer_logo = '',
        $favicon = '',
        $admin_panel_logo = '',
        $copyright_text = '',
        $maintained_by_text = '',
        $accessibility_text = '',
        $footer_about_us = '',
        $created_by = null,
        $updated_by = null,
        $currency_id = null
    ) {
        $this->site_name = $site_name;
        $this->site_tag_line = $site_tag_line;
        $this->seo_keywords = $seo_keywords;
        $this->seo_description = $seo_description;
        $this->header_logo = $header_logo;
        $this->footer_logo = $footer_logo;
        $this->favicon = $favicon;
        $this->admin_panel_logo = $admin_panel_logo;
        $this->copyright_text = $copyright_text;
        $this->maintained_by_text = $maintained_by_text;
        $this->accessibility_text = $accessibility_text;
        $this->footer_about_us = $footer_about_us;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->currency_id = $currency_id;
    }
    public $currency_id;
}
