<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Repositories\PageRepository;
use App\Services\MenuService;

class PageController extends Controller
{

    protected $menuService;
    protected $pageRepository;

    // Construct
    public function __construct()
    {
        $this->menuService = new MenuService();
        $this->pageRepository = new PageRepository();
    }

    // Page
    public function page($page)
    {
        $url = '/' . $page;
        $rowUrl = $page;
        $pageTitle = '';
        $parentPageTitle = '';
        $pageDetails = '';
        $submenus = '';

        // Search in menu
        $menu = $this->menuService->findByUrlWithParents($url);
        if (!$menu) {
            $page = $this->pageRepository->findBySlug($rowUrl);
            if ($page) {
                $pageTitle = $page->title;
                $pageDetails = $page;
            }
        } else {
            $parentId = $menu->id;
            $pageTitle = $menu->title;
            $pageDetails = $menu->page;
            if (count($menu->parents) > 0) {
                $parentPageTitle = $this->menuService->fetchMainParentName($menu->parents);
                $mainParent = $this->menuService->fetchMainParent($menu->parents);
                $parentId = $mainParent->id ?? $menu->id;
            }
            $submenus = $this->menuService->findAllById($parentId);
        }
        if (!empty($pageDetails) && $pageDetails->is_published == 1) {
            return view('website.page', compact('pageTitle', 'parentPageTitle', 'submenus', 'pageDetails'));
        }
        return abort(404);
    }
}
