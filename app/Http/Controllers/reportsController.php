<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Models\report;
use App\Models\report_progress;
use App\Models\technical_person;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class reportsController extends Controller
{

    public function dashboard()
    {
        $reports=report::all();
        $history=report::where('status','CLOSED')->orderBy('created_at','desc')->get();
        $history=$history->map(function ($report) {
            $report->created_date = $report->updated_at->format('Y-m-d');
            $report->technician_name = $report->report_progress->last()->person->name;
            return $report;
        });
    
        return Inertia::render('Dashboard',compact('reports','history'));
    }
    public function index()
    {
        $request = request();

        // Collect filters from query params
        $filters = [
            'search' => $request->query('search'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
            'status' => $request->query('status'),
            'hostel' => $request->query('hostel'),
        ];

        // Use the model scope to apply filters. Use pagination (10 per page).
        $reportsQuery = report::query()->filter($filters)->orderBy('created_at', 'desc');
        $reports = $reportsQuery->paginate(10)->through(function ($report) {
            $report->created_date = $report->created_at->format('Y-m-d');
            return $report;
        });
        // dd($reports);

        // Retrieve hostels defensively. If the connection or table doesn't exist, fall back to empty collection.
        $hostels = collect();
        try {
            $DB = DB::connection('student_registration');
            if ($DB->getSchemaBuilder()->hasTable('hostel')) {
                $hostels = $DB->table('hostel')->where('h_status', 'ACTIVE')->get();
            }
        } catch (\Exception $e) {
            // Log exception for visibility but don't break the page
            // You can uncomment the next line if you want to log to storage/logs
            // \Log::error('Hostel retrieval failed: '.$e->getMessage());
            $hostels = collect();
        }

        return Inertia::render('ReportsList', compact('reports', 'hostels'));
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
        
        // recommended replacement for $SELECTION_PLACEHOLDER$
        if ($request->hasFile('attachment')) {
            $files = $request->file('attachment');
            if (!is_array($files)) {
                $files = [$files];
            }

            $paths = [];
            foreach ($files as $file) {
                // store on the public disk (storage/app/public/attachments)
                $paths[] = $file->store('attachments', 'public');
            }

            // either save JSON or save array and use $casts = ['attachment' => 'array'] on the model
            $validated['attachment'] = json_encode($paths);
        }

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
        
        if ($request->hasFile('attachment')) {
            $files = $request->file('attachment');
            if (!is_array($files)) {
                $files = [$files];
            }

            $paths = [];
            foreach ($files as $file) {
                // store on the public disk (storage/app/public/attachments)
                $paths[] = $file->store('attachments', 'public');
            }

            // either save JSON or save array and use $casts = ['attachment' => 'array'] on the model
            $validated['attachment'] = json_encode($paths);
        }

        $report->update($validated);
        return redirect()->route("report.index")->with('success', 'Report updated successfully!');

    }
    public function closeReport($id)
    {
        $report=report::find($id);
        $report->status="CLOSED";
        $report->save();
        return redirect()->back()->with('success', 'Report closed successfully!');
    }

    public function reportDetail($id)
    {
        $reportDetail=report::find($id);
        // dd($reportDetail->report_progress);
        // $reportDetail["created_date"]=$report->created_at->format('Y-m-d');
        // $reportDetail=new ReportResource($report);
        
        $reportDetail["report_aaa"]=$reportDetail->report_progress->map(function ($report_progress) {
            $report_progress["created_date"]=$report_progress->created_at->format('Y-m-d');
            $report_progress["person_name"]=$report_progress->person;
            return $report_progress;
            
        });
        $techbicalPerson=technical_person::all();
        
        return Inertia::render('ReportDetails',compact('reportDetail','techbicalPerson'));
    }

    public function assignTechnician(Request $request,$id)
    {
        $validated = $request->validate([
            "solution"=>"required|string",
            "p_id"=>"required|integer",
        ]);

        $validated["report_id"]= $id;
        $validated["status"]="IN PROGRESS";

        $report_detail=report_progress::create($validated);
        if($report_detail){
            $report=report::find($id);
            $report->status="IN PROGRESS";
            $report->save();

            return redirect()->back()->with('success', 'Technician assigned successfully!');
        }
        return redirect()->back()->with('error', 'Technician assignment failed!');

    }

    public function updateAssignTechnician(Request $request,$id)
    {
        $validated = $request->validate([
            "solution"=>"required|string",
            "p_id"=>"required|integer",
        ]);
        $report_detail=report_progress::find($id);
        $report_detail->update($validated);
        if($report_detail){
            return redirect()->back()->with('success', 'Technician updated successfully!');
        }
        return redirect()->back()->with('error', 'Technician update failed!');

    }

    public function destroyAssignTechnician($id)
    {
        $reportTechnical=report_progress::find($id);
        $reportTechnical->delete();
        if($reportTechnical){
            return redirect()->back()->with('success', 'Technician removed successfully!');
        }
        return redirect()->back()->with('error', 'Technician removal failed!');
    }

}
