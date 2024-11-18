<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Http\Resources\MajorResource;
use App\Http\Traits\MobileResponse;
use App\Models\Major;
use Illuminate\Support\Facades\Validator;
class MajorController extends Controller
{
    use MobileResponse;

    public function update(Request $request,$id)
    {
        $major = Major::find($id);
        if(!$major){
            return $this ->fail("not Found");
        }


        $major->update([
            
            'name' => $request->name,
        ]);
        return $this->success( new MajorResource($disease) );
    }

    public function delete($id)
    {
        $major = Major::find($id);
        if($major){
            $major->delete();
            return $this->success("Deleted successfully");
        } else {
            return $this->fail("Not Found");
        }
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(),[
            
            'name'=>'required|unique:majors,name',
            //'doctor_id'=>'required|exists:doctor,id',
        ]);

        if($validator->fails()){

            return $this->fail($validator->errors()->first());

        }

        $major = Major::create([
            'name'=>$request->name,
            //'doctor_id'=>$request->doctor_id,
            
            
        ]);
        return $this->success( new MajorResource($major) );
    }

    public function all()
    {
        $major = Major::all();
        return $this->success( MajorResource::collection($major) );
    }
}
