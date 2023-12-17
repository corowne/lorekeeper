<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class AdminEdit extends Component {
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
     * @param mixed $title
     * @param mixed $object
     */
    public function __construct($title, $object) {
        $this->title = $title;
        $this->object = $object;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Closure|\Illuminate\Contracts\View\View|string
     */
    public function render() {
        if (Auth::check() && Auth::user()->hasPower($this->object->adminPower)) {
            return view('components.admin-edit');
        }
    }
}
