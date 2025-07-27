<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
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
        //if route is like this format .com/groups/group_id, then set show_info to true, else set it to false
        return view('layouts.app', compact('show_info','group'));
    }
}
