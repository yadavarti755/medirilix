<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PageHeader extends Component
{
    public $title;
    public $button;
    public $backButton;

    /**
     * Create a new component instance.
     */
    public function __construct($title = '', $button = '', $backButton = false)
    {
        $this->title = $title;
        $this->button = $button;
        $this->backButton = $backButton;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.page-header');
    }
}
