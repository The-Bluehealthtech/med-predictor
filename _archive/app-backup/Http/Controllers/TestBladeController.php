<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestBladeController extends Controller
{
    public function test()
    {
        $player = new \stdClass();
        $player->first_name = "Test";
        $player->last_name = "Blade";
        $player->photo_url = "https://example.com/test.jpg";
        
        return view("test-vue-simple", compact("player"));
    }
}