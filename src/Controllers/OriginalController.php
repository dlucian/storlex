<?php

namespace App\Controllers;

use App\Drivers\DriverManager;
use App\Request;
use App\Response;
use App\Traits\ValidatesAdminRequests;

/**
 * Original Images Controller
 *
 * Handles uplaoding of original images,
 * which are to be resized and optimized.
 */
class OriginalController extends BaseController
{
    use ValidatesAdminRequests;

    /**
     * Upload image
     *
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request): Response
    {
        if ($adminRequest = $this->validateAdminRequest($request)) {
            return $adminRequest;
        }

        $file = $request->getFile('file');
        DriverManager::imageStorage()->save($file);
        return $this->successJson(
            sprintf('File \'%s\' uploaded successfully', $file['name'])
        );
    }

    /**
     * Delete an original image from storage
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        if ($adminRequest = $this->validateAdminRequest($request)) {
            return $adminRequest;
        }

        $file = $request->input('filename');
        if ($file === null) {
            return new Response(422, ['message' => 'Missing filename']);
        }

        if (DriverManager::imageStorage()->exists($file) === false) {
            return new Response(404, ['message' => 'File not found']);
        }

        DriverManager::imageStorage()->remove($file);
            return $this->successJson(
                sprintf('File \'%s\' deleted successfully', $file)
            );
    }
}
