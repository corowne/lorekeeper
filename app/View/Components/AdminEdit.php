<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Auth;

class AdminEdit extends Component
{
    /**
     * The model object.
     *
     * @var string
     */
    public $object;

    /**
     * The title for the button.
     *
     * @var string
     */
    public $title;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title, $object)
    {
        $this->title = $title;
        $this->object = $object;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        if(Auth::check() && Auth::user()->hasPower($this->object->adminPower)) return view('components.admin-edit');
    }
}
