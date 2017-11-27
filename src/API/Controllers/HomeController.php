<?php

namespace SlimKit\PlusID\API\Controllers;

class HomeController
{
    public function index()
    {
        return trans('plus-id::messages.success');
    }
}
