<?php

namespace App\Repositories;

use App\Models\DownloadCommitteeReport;
use App\Models\DownloadActs;
use App\Models\DownloadApplication;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
class DownloadRepository
{

    //-----------------Acts--------------------------------------------------------------------
    public function addActs($request)
    {
        $filePathEn = null;
        if ($request->hasFile('actFileEn')) {
            $file = $request->file('actFileEn');
            $fileName = time() . '_en.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('acts', $fileName, 'public');
            $filePathEn = str_replace('storage/', '', $path);
        }

        $filePathSi = null;
        if ($request->hasFile('actFileSi')) {
            $file = $request->file('actFileSi');
            $fileName = time() . '_si.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('acts', $fileName, 'public');
            $filePathSi = str_replace('storage/', '', $path);
        }

        $filePathTa = null;
        if ($request->hasFile('actFileTa')) {
            $file = $request->file('actFileTa');
            $fileName = time() . '_ta.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('acts', $fileName, 'public');
            $filePathTa = str_replace('storage/', '', $path);
        }

        $acts = DownloadActs::create([
            'number' => $request['actNumber'],
            'issue_date' => $request['actDate'],
            'name_en' => $request['nameEn'],
            'name_si' => $request['nameSi'],
            'name_ta' => $request['nameTa'],
            'file_path_en' => $filePathEn,
            'file_path_si' => $filePathSi,
            'file_path_ta' => $filePathTa,
            'created_at' => now(),
        ]);
        return response([
            'acts' => $acts
        ], 200);

    }


    public function updateActs($id, $request)
    {
        // Retrieve the existing DownloadActs model
        $existActs = DownloadActs::findOrFail($id);

        // Delete existing files if new files are uploaded
        if ($request->hasFile('actFileEn')) {
            Storage::delete('public/' . $existActs->file_path_en);
        }

        if ($request->hasFile('actFileSi')) {
            Storage::delete('public/' . $existActs->file_path_si);
        }

        if ($request->hasFile('actFileTa')) {
            Storage::delete('public/' . $existActs->file_path_ta);
        }

        // Process English file if uploaded
        if ($request->hasFile('actFileEn')) {
            $englishFileName = 'acts/' . time() . '_en.' . $request->file('actFileEn')->getClientOriginalExtension();
            $request->file('actFileEn')->storeAs('public', $englishFileName);
            $request->merge(['actFileEn' => $englishFileName]);
        } else {
            // Use the existing file path if no new file is uploaded
            $request->merge(['actFileEn' => $existActs->file_path_en]);
        }

        // Process Sinhala file if uploaded
        if ($request->hasFile('actFileSi')) {
            $sinhalaFileName = 'acts/' . time() . '_si.' . $request->file('actFileSi')->getClientOriginalExtension();
            $request->file('actFileSi')->storeAs('public', $sinhalaFileName);
            $request->merge(['actFileSi' => $sinhalaFileName]);
        } else {
            // Use the existing file path if no new file is uploaded
            $request->merge(['actFileSi' => $existActs->file_path_si]);
        }

        // Process Tamil file if uploaded
        if ($request->hasFile('actFileTa')) {
            $tamilFileName = 'acts/' . time() . '_ta.' . $request->file('actFileTa')->getClientOriginalExtension();
            $request->file('actFileTa')->storeAs('public', $tamilFileName);
            $request->merge(['actFileTa' => $tamilFileName]);
        } else {
            // Use the existing file path if no new file is uploaded
            $request->merge(['actFileTa' => $existActs->file_path_ta]);
        }

        // Update other fields along with the file paths
        $existActs->update([
            'number' => $request->input('actNumber'),
            'issue_date' => $request->input('actDate'),
            'name_en' => $request->input('nameEn'),
            'name_si' => $request->input('nameSi'),
            'name_ta' => $request->input('nameTa'),
            'file_path_en' => $request->input('actFileEn'),
            'file_path_si' => $request->input('actFileSi'),
            'file_path_ta' => $request->input('actFileTa'),
            'updated_at' => now(),
        ]);

        return response(['message' => 'Acts updated successfully.'], 200);
    }



    public function deleteActs($id)
    {
        $acts = DownloadActs::find($id);

        if ($acts) {
            if ($acts->file_path_en) {
                Storage::disk('public')->delete($acts->file_path_en);
            }
            if ($acts->file_path_si) {
                Storage::disk('public')->delete($acts->file_path_si);
            }
            if ($acts->file_path_ta) {
                Storage::disk('public')->delete($acts->file_path_ta);
            }

            $acts->delete();

            return response()->noContent(); // Send 204 upon successful delete
        }

        return response()->noContent()->setStatusCode(404); // Send 404 if act not found

    }


    //-----------------Report--------------------------------------------------------------------

    public function addReport($request)
    {
        $filePathEn = null;
        if ($request->hasFile('reportFileEn')) {
            $file = $request->file('reportFileEn');
            $fileName = time() . '_en.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('report', $fileName, 'public');
            $filePathEn = str_replace('storage/', '', $path);
        }

        $filePathSi = null;
        if ($request->hasFile('reportFileSi')) {
            $file = $request->file('reportFileSi');
            $fileName = time() . '_si.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('report', $fileName, 'public');
            $filePathSi = str_replace('storage/', '', $path);
        }

        $filePathTa = null;
        if ($request->hasFile('reportFileTa')) {
            $file = $request->file('reportFileTa');
            $fileName = time() . '_ta.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('report', $fileName, 'public');
            $filePathTa = str_replace('storage/', '', $path);
        }

        $report = DownloadCommitteeReport::create([
            'report_year' => $request['reportYear'],
            'report_month' => $request['reportMonth'],
            'name_en' => $request['nameEn'],
            'name_si' => $request['nameSi'],
            'name_ta' => $request['nameTa'],
            'file_path_en' => $filePathEn,
            'file_path_si' => $filePathSi,
            'file_path_ta' => $filePathTa,
        ]);
        return response([
            'report' => $report
        ], 200);

    }


    public function updateReport($id, $request)
    {
        // Retrieve the existing DownloadCommitteeReport model
        $existReport = DownloadCommitteeReport::findOrFail($id);

        // Delete existing files if new files are uploaded
        if ($request->hasFile('reportFileEn')) {
            Storage::delete('public/' . $existReport->file_path_en);
        }

        if ($request->hasFile('reportFileSi')) {
            Storage::delete('public/' . $existReport->file_path_si);
        }

        if ($request->hasFile('reportFileTa')) {
            Storage::delete('public/' . $existReport->file_path_ta);
        }

        // Process English file if uploaded
        if ($request->hasFile('reportFileEn')) {
            $englishFileName = 'report/' . time() . '_en.' . $request->file('reportFileEn')->getClientOriginalExtension();
            $request->file('reportFileEn')->storeAs('public', $englishFileName);
            $request->merge(['reportFileEn' => $englishFileName]);
        } else {
            // Use the existing file path if no new file is uploaded
            $request->merge(['reportFileEn' => $existReport->file_path_en]);
        }

        // Process Sinhala file if uploaded
        if ($request->hasFile('reportFileSi')) {
            $sinhalaFileName = 'report/' . time() . '_si.' . $request->file('reportFileSi')->getClientOriginalExtension();
            $request->file('reportFileSi')->storeAs('public', $sinhalaFileName);
            $request->merge(['reportFileSi' => $sinhalaFileName]);
        } else {
            // Use the existing file path if no new file is uploaded
            $request->merge(['reportFileSi' => $existReport->file_path_si]);
        }

        // Process Tamil file if uploaded
        if ($request->hasFile('reportFileTa')) {
            $tamilFileName = 'report/' . time() . '_ta.' . $request->file('reportFileTa')->getClientOriginalExtension();
            $request->file('reportFileTa')->storeAs('public', $tamilFileName);
            $request->merge(['reportFileTa' => $tamilFileName]);
        } else {
            // Use the existing file path if no new file is uploaded
            $request->merge(['reportFileTa' => $existReport->file_path_ta]);
        }

        // Update other fields along with the file paths
        $existReport->update([
            'report_year' => $request->input('reportYear'),
            'report_month' => $request->input('reportMonth'),
            'name_en' => $request->input('nameEn'),
            'name_si' => $request->input('nameSi'),
            'name_ta' => $request->input('nameTa'),
            'file_path_en' => $request->input('reportFileEn'),
            'file_path_si' => $request->input('reportFileSi'),
            'file_path_ta' => $request->input('reportFileTa'),
        ]);

        return response(['message' => 'Report updated successfully.'], 200);
    }



    public function deleteReport($id)
    {
        $report = DownloadCommitteeReport::find($id);

        if ($report) {
            if ($report->file_path_en) {
                Storage::disk('public')->delete($report->file_path_en);
            }
            if ($report->file_path_si) {
                Storage::disk('public')->delete($report->file_path_si);
            }
            if ($report->file_path_ta) {
                Storage::disk('public')->delete($report->file_path_ta);
            }

            $report->delete();

            return response()->noContent(); // Send 204 upon successful delete
        }

        return response()->noContent()->setStatusCode(404); // Send 404 if act not found

    }

    //----------------------------------------------------------

    public function getCount()
    {
        $countAct = DownloadActs::count();
        $countReport = DownloadCommitteeReport::count();
        return [$countAct, $countReport];
    }

    // ----------------- Applications -----------------
    public function addApplication($request)
    {
        $filePathEn = null;
        if ($request->hasFile('applicationFileEn')) {
            $file = $request->file('applicationFileEn');
            $fileName = time() . '_en.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('applications', $fileName, 'public');
            $filePathEn = str_replace('storage/', '', $path);
        }

        $filePathSi = null;
        if ($request->hasFile('applicationFileSi')) {
            $file = $request->file('applicationFileSi');
            $fileName = time() . '_si.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('applications', $fileName, 'public');
            $filePathSi = str_replace('storage/', '', $path);
        }

        $filePathTa = null;
        if ($request->hasFile('applicationFileTa')) {
            $file = $request->file('applicationFileTa');
            $fileName = time() . '_ta.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('applications', $fileName, 'public');
            $filePathTa = str_replace('storage/', '', $path);
        }

        $application = DownloadApplication::create([
            'application_year' => $request['applicationYear'],
            'application_month' => $request['applicationMonth'],
            'name_en' => $request['nameEn'],
            'name_si' => $request['nameSi'],
            'name_ta' => $request['nameTa'],
            'file_path_en' => $filePathEn,
            'file_path_si' => $filePathSi,
            'file_path_ta' => $filePathTa,
        ]);

        return response(['application' => $application], 201);
    }

    public function updateApplication($id, $request)
    {
        $existing = DownloadApplication::findOrFail($id);

        if ($request->hasFile('applicationFileEn')) {
            Storage::delete('public/' . $existing->file_path_en);
        }
        if ($request->hasFile('applicationFileSi')) {
            Storage::delete('public/' . $existing->file_path_si);
        }
        if ($request->hasFile('applicationFileTa')) {
            Storage::delete('public/' . $existing->file_path_ta);
        }

        if ($request->hasFile('applicationFileEn')) {
            $enName = 'applications/' . time() . '_en.' . $request->file('applicationFileEn')->getClientOriginalExtension();
            $request->file('applicationFileEn')->storeAs('public', $enName);
            $request->merge(['applicationFileEn' => $enName]);
        } else {
            $request->merge(['applicationFileEn' => $existing->file_path_en]);
        }

        if ($request->hasFile('applicationFileSi')) {
            $siName = 'applications/' . time() . '_si.' . $request->file('applicationFileSi')->getClientOriginalExtension();
            $request->file('applicationFileSi')->storeAs('public', $siName);
            $request->merge(['applicationFileSi' => $siName]);
        } else {
            $request->merge(['applicationFileSi' => $existing->file_path_si]);
        }

        if ($request->hasFile('applicationFileTa')) {
            $taName = 'applications/' . time() . '_ta.' . $request->file('applicationFileTa')->getClientOriginalExtension();
            $request->file('applicationFileTa')->storeAs('public', $taName);
            $request->merge(['applicationFileTa' => $taName]);
        } else {
            $request->merge(['applicationFileTa' => $existing->file_path_ta]);
        }

        $existing->update([
            'application_year' => $request->input('applicationYear'),
            'application_month' => $request->input('applicationMonth'),
            'name_en' => $request->input('nameEn'),
            'name_si' => $request->input('nameSi'),
            'name_ta' => $request->input('nameTa'),
            'file_path_en' => $request->input('applicationFileEn'),
            'file_path_si' => $request->input('applicationFileSi'),
            'file_path_ta' => $request->input('applicationFileTa'),
        ]);

        return response(['message' => 'Application updated successfully.'], 200);
    }

    public function deleteApplication($id)
    {
        $application = DownloadApplication::find($id);
        if ($application) {
            if ($application->file_path_en) {
                Storage::disk('public')->delete($application->file_path_en);
            }
            if ($application->file_path_si) {
                Storage::disk('public')->delete($application->file_path_si);
            }
            if ($application->file_path_ta) {
                Storage::disk('public')->delete($application->file_path_ta);
            }
            $application->delete();
            return response()->noContent();
        }
        return response()->noContent()->setStatusCode(404);
    }

}


