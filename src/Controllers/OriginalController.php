<?php

namespace App\Controllers;

use App\Drivers\DriverManager;
use App\Request;
use App\Response;

/**
 * Original Images Controller
 *
 * Handles uplaoding of original images,
 * which are to be resized and optimized.
 */
class OriginalController extends BaseController
{
    /**
     * Upload image
     *
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request): Response
    {
        $file = $request->getFile('file');
        DriverManager::imageStorage()->save($file);
        return $this->successJson(
            sprintf('File \'%s\' uploaded successfully', $file['name'])
        );
    }
}
