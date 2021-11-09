<?php

namespace App\Controllers;

use App\Drivers\DriverManager;
use App\Request;
use App\Response;

/**
 * Images Controller
 */
class ImagesController extends BaseController
{
    /**
     * Retrieve image
     *
     * @param Request $request
     * @return Response
     */
    public function retrieve(string $image, Request $request): Response
    {
        $attributes = $this->expandAttributes($image);
        if ($attributes === null) {
            return new Response(400, 'Bad request');
        }
        $image = DriverManager::imageStorage()->get((string)$attributes['name']);
        if ($image === null) {
            return new Response(404, 'Not found');
        }
        return new Response(200, $image, ['Content-Type' => 'image/png']);
    }

    /**
     * Convert an IMG url such as
     * 'Screenshot 2021-11-03 at 16.50.20.png-300x200.jpg'
     * to parameters for the retrieve method, such as name,
     * width, height, filetype.
     *
     * @param string $image
     * @return ?array<string,string|int> The parameters if matched, or null.
     */
    public function expandAttributes(string $image): ?array
    {
        $matches = [];
        preg_match('/(.+)-(\d*)x(\d*)\.([a-zA-Z]{3,4})/', $image, $matches);
        if (empty($matches) || count($matches) < 5) {
            return null;
        }
        return [
            'name' => (string)$matches[1],
            'width' => (int)$matches[2],
            'height' => (int)$matches[3],
            'extension' => (string)$matches[4],
        ];
    }
}
