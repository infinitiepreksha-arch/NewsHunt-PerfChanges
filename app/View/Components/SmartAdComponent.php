<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\SmartAd;

class SmartAdComponent extends Component
{
    /**
     * The alert type.
     *
     * @var string
     */
    public $slug;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $smartAd = SmartAd::where('slug', $this->slug)->first();
        return view('front_end.components.smart-ad-component', compact('smartAd'));
    }
}
