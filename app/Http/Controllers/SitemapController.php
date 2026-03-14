<?php

namespace App\Http\Controllers;

use App\Services\MenuService;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    protected $menuService;

    public function __construct()
    {
        $this->menuService = new MenuService();
    }

    public function showSitemap()
    {
        $pageTitle = "Sitemap";
        $parentPageTitle = "Sitemap";
        $menus = $this->menuService->findByLocation('header');
        // dd($menus);
        return view('website.sitemap', compact('pageTitle', 'parentPageTitle', 'menus'));
    }
}
