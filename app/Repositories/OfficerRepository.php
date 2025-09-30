<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Officer;
use App\Models\OfficerService;
use App\Models\OfficerSubject;
use App\Models\OfficersOfficerSubject;
use App\Models\OfficerPosition;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class OfficerRepository
{
//-----------------Officer--------------------------------------------------------------------
    public function addOfficer($request) {
        $user = User::create([
            'email' => $request->email,
        ]);
        $user->assignRole('officer');

        // Handle image upload
        $imgPath = null;
        if($request->hasFile('img') && $request->file('img')->isValid()) {
            $image = $request->file('img');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $path = $image->storeAs('images/officer', $imageName, 'public');
//            $imagePath = str_replace('storage/', '', $path);
            $imagePath = Storage::url($path);
        }
        // Create officer
        $officer = new Officer();
        $officer->user_id = $user->id;
        $officer->title = $request->title;
        $officer->name_en = $request->nameEn;
        $officer->name_si = $request->nameSi;
        $officer->name_ta = $request->nameTa;
        $officer->tel = $request->tel;
        $officer->officer_services_id = $request->service;
        $officer->officer_grades_id = $request->grade;
        $officer->officer_positions_id = $request->position;
        $officer->image = $imagePath;
        $officer->save();

        // Handle subjects
        $dutyIds = [];
        foreach ($request->input('duty') as $dutyId) {
            $dutyIds[] = $dutyId;
        }
        $officer->officerSubjects()->sync($dutyIds);

        // Return response
        $response = [
            'user' => $user,
            'officer' => $officer,
        ];
        return response($response, 201);
    }


    public function getOfficers()
    {
        $officers = Officer::with([
            'officerService' => function ($query) {
                $query->select('id', 'sname_en');
            },
            'officerGrade' => function ($query) {
                $query->select('id', 'grade_en');
            },
            'officerPosition' => function ($query) {
                $query->select('id', 'position_en');
            },
            'officerSubjects' => function ($query) {
                $query->select('officer_subjects_id', 'subject_en');
            },
            'user' => function ($query) {
                $query->select('id', 'email', 'status');
            }
        ])
            ->select('id', 'title','name_en', 'name_si','name_ta', 'image', 'tel','officer_services_id','officer_grades_id','officer_positions_id', 'user_id')
            ->get();

        $response = [
            "AllOfficers" => $officers,
        ];
        return response($response);
//        return response()->json($response);
    }


    public  function  updateOfficer($id, $request)
    {
       $existOfficer = Officer::findOrFail($id);

       // Delete existing officer image if a new image uploaded
       if ($request->hasFile('img')) {
            // Storage::delete('public/' . $existOfficer->image);
            $imagePath = $existOfficer->image;
            $imagePath = str_replace('/storage/', '', $imagePath);
            Storage::disk('public')->delete($imagePath);

            $image = $request->file('img');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $path = $image->storeAs('images/officer', $imageName, 'public');
            $imagePathNew = Storage::url($path);
        }else{
            $imagePathNew = $existOfficer->image;
        }

        $existOfficer->update([
           'title' => $request['title'],
           'name_en' => $request['nameEn'],
           'name_si' => $request['nameSi'],
           'name_ta' => $request['nameTa'],
           'tel' => $request['tel'],
           'officer_services_id' => $request['service'],
           'officer_grades_id' => $request['grade'],
           'officer_positions_id' => $request['position'],
           'image' => $imagePathNew,
        ]);

        if ($request->has('duty')) {
            $dutyIds = $request->input('duty');
            $existOfficer->officerSubjects()->sync($dutyIds);
        }

        $user = User::findOrFail($existOfficer->user_id);
        $user->update([
            // 'email' => $request['email'],
            'status' => $request['status']
        ]);

        return response(['message' => 'Officer updated successfully.'], 200);

    }



    public function deleteOfficer($id)
    {
        $officer = Officer::find($id);

        if ($officer) {
            $userId = $officer->user_id;
//            $userId = Officer::where('id', $id)->value('user_id');
            try{
                DB::beginTransaction();
                $imagePath = $officer->image;


//                \Log::info('Image path: ' . $imagePath);
                $imagePath = str_replace('/storage/', '', $imagePath);
//                Storage::disk('public')->delete($officer->image);
                Storage::disk('public')->delete($imagePath);

                $officer->officerSubjects()->detach();
                $officer->delete();

                $user = User::find($userId);
                if($user){
                    $user->tokens()->delete();
                    $user->roles()->detach();
                    $user->permissions()->detach();
                    $user->delete();
                }
                DB::commit();
                return true;
            }catch(\Exception $e){
                DB::rollBack();
                return false;
            }
        }
        return false;
    }

    public function getCount()
    {
        return Officer::count();
    }

    //-----------------Position--------------------------------------------------------------------
    public function addPosition($data)
    {
        $position = OfficerPosition::create([
            'position_en' => $data['postEn'],
            'position_si' => $data['postSi'],
            'position_ta' => $data['postTa'],
            'officer_services_id' => $data['service'],
            'officer_levels_id' => $data['level'],
        ]);
        return response([
            'position' => $position
        ], 201);
    }

    public function updatePosition($id, $data)
    {
        $position = OfficerPosition::find($id);
        $position->update([
            'position_en' => $data['postEn'],
            'position_si' => $data['postSi'],
            'position_ta' => $data['postTa'],
            'officer_services_id' => $data['service'],
            'officer_levels_id' => $data['level'],
        ]);
        return response(['message' => 'Position updated successfully.'], 200);
    }
    public function deletePosition($id)
    {
        $position = OfficerPosition::find($id);

        if ($position) {
            $position->delete();
            return response()->noContent(); // Send 204 upon successful delete
        }
        return response()->noContent()->setStatusCode(404); // Send 404 if position not found
    }



    //-----------------Subject--------------------------------------------------------------------
    public function addSubject($data)
    {
        $subject = OfficerSubject::create([
            'subject_en' => $data['dutyEn'],
            'subject_si' => $data['dutySi'],
            'subject_ta' => $data['dutyTa'],
            'officer_levels_id' => $data['level'],
        ]);
        return response([
            'Subject' => $subject
        ], 201);
    }

    public function updateSubject($id, $data)
    {
        $subject = OfficerSubject::find($id);
        $subject->update([
            'subject_en' => $data['dutyEn'],
            'subject_si' => $data['dutySi'],
            'subject_ta' => $data['dutyTa'],
            'officer_levels_id' => $data['level'],
        ]);
        return response(['message' => 'Duty updated successfully.'], 200);
    }
    public function deleteSubject($id)
    {
        $subject = OfficerSubject::find($id);

        if ($subject) {
            $subject->delete();
            return response()->noContent(); // Send 204 upon successful delete
        }
        return response()->noContent()->setStatusCode(404); // Send 404 if subject not found
    }

}


