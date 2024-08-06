<?php

namespace App\Http\Controllers\Api\Commons;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function backup()
    {
        // \Artisan::call('backup:run --only-db --disable-notifications');
        exec('c:\windows\system32\cmd.exe /c C:\xampp\htdocs\initxampp.bat');
        return response()->json(['message' => 'success'], 200);
        // try {
        // } catch (\Exception $e) {
        //     return response()->json(['message' => 'error', 'errors' => $e->getMessage()], 400);
        // }
    }
}