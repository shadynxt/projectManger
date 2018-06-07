<?php

namespace App\Http\Controllers;

use InfyOm\Generator\Utils\ResponseUtil;
use Response;

/**
 * @SWG\Swagger(
 *   basePath="/api/v1",
 *   @SWG\Info(
 *     title="Laravel Generator APIs",
 *     version="1.0.0",
 *   )
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
class AppBaseController extends Controller
{
   public $default_paginate_number = 20;
    public function sendResponse($result, $message = 'oki')
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }
    public function sendError($error, $code = 404)
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }
    public function userNotFound()
    {
        return Response::json(ResponseUtil::makeError('user_not_found'), 404);
    }
    public function itemNotFound()
    {
        return Response::json(ResponseUtil::makeError('item_not_found'), 404);
    }
}
