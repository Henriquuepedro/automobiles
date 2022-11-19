<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fipe\ControlAuto;

class ReportController extends Controller
{
    private ControlAuto $controlAutos;

    public function __construct(ControlAuto $controlAutos)
    {
        $this->controlAutos = $controlAutos;
    }

    public function fipeVariation()
    {
        $autoFipe = new \StdClass();
        $autoFipe->controlAutos = $this->controlAutos->getAllControlsActive();

        return view('admin.report.fipeVariation', compact('autoFipe'));
    }
}
