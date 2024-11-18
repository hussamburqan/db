namespace App\Http\Controllers;

use App\Http\Resources\DoctorResource;
use App\Http\Traits\MobileResponse;
use App\Models\Doctor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    use MobileResponse;

    public function one2($id)
    {
        $doctor = Doctor::find($id);
        if (!$doctor) {
            return $this->fail("Doctor not found");
        }
        return $this->success(new DoctorResource($doctor));
    }

    public function one(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:doctors,id'
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first());
        }

        $doctor = Doctor::find($request->id);
        return $this->success(new DoctorResource($doctor));
    }

    public function update(Request $request, $id)
    {
        $doctor = Doctor::find($id);
        if (!$doctor) {
            return $this->fail("Doctor not found");
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string',
            'age' => 'sometimes|required|integer',
            'email' => 'sometimes|required|email|unique:doctors,email,'.$id,
            'gender' => 'sometimes|required|in:male,female',
            'phone' => 'sometimes|required|unique:doctors,phone,'.$id,
            'specialization' => 'sometimes|required|string',
            'experience' => 'sometimes|required|integer',
            'education' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first());
        }

        $doctor->update($request->only([
            'name', 'age', 'email', 'gender', 
            'phone', 'specialization', 'experience', 'education'
        ]));

        return $this->success(new DoctorResource($doctor));
    }

    public function delete($id)
    {
        $doctor = Doctor::find($id);
        if ($doctor) {
            $doctor->delete();
            return $this->success("Deleted successfully");
        }
        return $this->fail("Doctor not found");
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'age' => 'required|integer',
            'email' => 'required|email|unique:doctors,email',
            'gender' => 'required|in:male,female',
            'phone' => 'required|unique:doctors,phone',
            'specialization' => 'required|string',
            'experience' => 'required|integer',
            'education' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'nclinic_id' => 'required|exists:nclinics,id',
            'major_id' => 'required|exists:majors,id',
        ]);

        if ($validator->fails()) {
            return $this->fail($validator->errors()->first());
        }

        $doctor = Doctor::create($request->only([
            'name', 'age', 'email', 'gender', 'phone',
            'specialization', 'experience', 'education',
            'user_id', 'nclinic_id', 'major_id'
        ]));

        return $this->success(new DoctorResource($doctor));
    }

    public function all()
    {
        $doctors = Doctor::all();
        return $this->success(DoctorResource::collection($doctors));
    }
}