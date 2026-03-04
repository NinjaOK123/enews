<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function gioiThieu(): View
    {
        return view('pages.gioi-thieu');
    }

    public function quyDinh(): View
    {
        return view('pages.quy-dinh');
    }

    public function lienHe(): View
    {
        return view('pages.lien-he');
    }

    public function docVaSuyNgam(): View
    {
        return view('pages.doc-suy-ngam');
    }
}
