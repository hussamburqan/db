<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceResource;
use App\Http\Traits\MobileResponse;
use App\Models\Invoice;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use App\Models\Major;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    use MobileResponse;

    public function update(Request $request,$id)
    {
        $invoice = Invoice::find($id);
        if(!$invoice){
            return $this ->fail("not Found");
        }


        $invoice->update([
            
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_status,
            'time' => $request->time,
        ]);
        return $this->success( new InvoiceResource($disease) );
    }

    public function delete($id)
    {
        $invoice = Invoice::find($id);
        if($invoice){
            $invoice->delete();
            return $this->success("Deleted successfully");
        } else {
            return $this->fail("Not Found");
        }
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'amount'=>'required',
            'payment_method'=>'required',
            'payment_status'=>'required',
            'time'=>'required',
            'doctor_id'=>'required|exists:doctor,id',
            'patient_id'=>'required|exists:patients,id',
            'major_id'=>'required|exists:majors,id',
            'user_id'=>'required|exists:users,id',
        ]);

        if($validator->fails()){

            return $this->fail($validator->errors()->first());

        }

        $invoice = Invoice::create([
            'amount'=>$request->amount,
            'payment_method'=>$request->payment_method,
            'payment_status'=>$request->payment_status,
            'time'=>$request->time,
            'major_id'=>$request->major_id,
            'user_id'=>$request->user_id,
            'doctor_id'=>$request->doctor_id,
            'patient_id'=>$request->patient_id,
            
        ]);
        return $this->success( new InvoiceResource($invoice) );
    }

    public function all()
    {
        $invoice = Invoice::all();
        return $this->success( InvoiceResource::collection($invoice) );
    }
}
