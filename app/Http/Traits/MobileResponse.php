<?php

namespace App\Http\Traits;

trait MobileResponse
{
    public function success($data, $message = 'Success', $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function fail($message = 'Error', $code = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => null
        ], $code);
    }
}

/*

<?php
//  MobileResponse.php
//php artisan make:trait App\Http\Trait\MobileResponse
namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;

trait MobileResponse
{
    public function fail($msg)
    {
        $res = [
            "status"=>false,
            "msg"=>$msg,
            "data"=> null
        ];
        return response()->json($res);
    }
    public function success($data)
    {
        $res = [
            "status"=>true,
            "msg"=>"",
            "data"=> $data
        ];
        return response()->json($res);
    }

    public function upload($file,$directory)
    {
        $path = Storage::disk('public')->put($directory,$file);
        return Storage::url($path);
    }
}
*/