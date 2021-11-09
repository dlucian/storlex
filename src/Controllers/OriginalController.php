<?php

namespace App\Controllers;

use App\Drivers\DriverManager;
use App\Request;
use App\Response;

class OriginalController extends BaseController
{
    public function upload(Request $request): Response
    {
        DriverManager::imageStorage()->save($request->getFile('file'));
        return $this->successJson('File uploaded successfully');
    }
}
