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
        if ($file === null) {
            return $this->errorJson(422, 'No file was uploaded');
        }

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
            return $this->errorJson(422, 'No filename was provided');
        }

        if (DriverManager::imageStorage()->exists($file) === false) {
            return $this->error404();
        }

        DriverManager::imageStorage()->remove($file);

        DriverManager::imageCache()->deleteTag($file);

        return $this->successJson(
            sprintf('File \'%s\' deleted successfully', $file)
        );
    }
}
