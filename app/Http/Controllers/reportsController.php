<?php

namespace App\Http\Controllers;

use App\Models\report;
use Inertia\Inertia;
use Illuminate\Http\Request;

class reportsController extends Controller
{
    public function index()
    {
        $reports=report::all()->map(function ($report) {
            $report->created_date = $report->created_at->format('Y-m-d');
            return $report;
        });
        
        return Inertia::render('ReportsList',compact('reports'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_type'   => 'required|string|max:255',
            'report_issue'  => 'required|string',
            'places'        => 'required|string|max:255',
            'emergency'     => 'required|string|max:50',
        ]);

        $validated["s_name"]="Create by admin";
        $validated["status"]="PENDING";

        $reports=report::create($validated);
        if($reports){
            return redirect()->route("report.index")->with('success', 'Report submitted successfully!');
        }

        return redirect()->back()->with('error', 'Report submission failed!');
    }

    public function destroy(report $report)
    {
        
        $report->delete();
        return redirect()->route("report.index")->with('success', 'Report deleted successfully!');
    }

    public function update(Request $request, report $report)
    {
        $validated = $request->validate([
            'report_type'   => 'required|string|max:255',
            'report_issue'  => 'required|string',
            'places'        => 'required|string|max:255',
            'emergency'     => 'required|string|max:50',
        ]);
        
        $report->update($validated);

    }
}
