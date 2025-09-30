<?php

namespace App\Repositories;

use App\Models\Complain;
use App\Models\ComplainAction;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ComplainRepository
{


    public function addComplain($data)
    {
        // Create the complain record first
        $complain = Complain::create([
            'cname' => $data['cname'],
            'tele' => $data['tele'],
            'complain' => $data['complain'],
        ]);

        // Handle the uploaded images if available
        if ($data->hasFile('imageList')) {
            $images = $data->file('imageList'); // Get the array of uploaded images

            // Store each image and associate it with the complain record
            foreach ($images as $index => $image) {
                $imagePath = $image->store('complains', 'public'); // Store the image in the 'complains' folder under 'storage/app/public'

                // Update the complain record for each image (img1, img2, img3)
                $complain->update([
                    'img' . ($index + 1) => $imagePath,
                ]);
            }
        }

        // Return the response
        $response = [
            'Complain' => $complain,
        ];

        return $response;
    }


    public function getComplain()
    {
        $complain = Complain::select('id', 'created_at', 'cname', 'tele', 'complain', 'img1', 'img2', 'img3')
        ->with('complainAction:id,complain_id,action,created_at')
        ->get();

        $response = [
            "AllComplains" => $complain,
        ];

        return response($response, 200);
    }

    public function addAction($request) {

        $complainAction = new complainAction();
        $complainAction->complain_id = $request->id;
        $complainAction->action = $request->action;
        $complainAction->save();
        // Return response
        $response = [
            'complainAction' => $complainAction,
        ];
        return response($response, 201);
    }

    public function updateAction($id, $request)
    {
        $existAction = ComplainAction::where('complain_id', $id)->firstOrFail();

        $existAction->update([
            'action' => $request['action'],
        ]);

        return response(['message' => 'Complain action updated successfully.'], 200);
    }

    public function deleteComplain($id)
    {

        \Log::info('xxxx: ' . $id);
        \Log::info('All Complains:', Complain::all()->toArray());
        $complain = Complain::find($id);
        \Log::info('xxxx2: ' . $complain);
        if ($complain) {
            try{
                DB::beginTransaction();

                $imagePath1 = $complain->img1;
                if($imagePath1!==null){
                    $imagePath1 = str_replace('/storage/', '', $imagePath1);
                    Storage::disk('public')->delete($imagePath1);
                }

                $imagePath2 = $complain->img2;
                if($imagePath2!==null){
                    $imagePath2 = str_replace('/storage/', '', $imagePath2);
                    Storage::disk('public')->delete($imagePath2);
                }

                $imagePath3 = $complain->img3;
                if($imagePath3!==null){
                    $imagePath3 = str_replace('/storage/', '', $imagePath3);
                    Storage::disk('public')->delete($imagePath3);
                }

                // $complain->complainAction()->detach();
                $complain->complainAction->delete();
                $complain->delete();
                DB::commit();
                return true;
            }catch(\Exception $e){
                DB::rollBack();
                return false;
            }
        }
        return false;
    }

}


