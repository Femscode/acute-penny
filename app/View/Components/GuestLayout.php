<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        $route = Route::currentRouteName();//get the current route
       
        if($route == 'groups.show') {
            $show_info = true;
            $group = Route::current()->parameter('group');
           
        } else {
            $show_info = false;
            $group = null;
        }
        return view('layouts.guest', compact('show_info','group'));
    }
}
