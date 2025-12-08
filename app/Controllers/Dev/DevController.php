<?php

namespace App\Controllers\Dev;

use App\Controllers\BaseController;

class DevController extends BaseController
{
    public function layoutTest()
    {
        $data = [
            'title' => 'Layout Test - Perjadin',
        ];

        return view('test/layout_test', $data);
    }
}
