<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Lang;
use JWTAuth;
/**
 * Class AuthController.
 */
class AuthApiController extends AppBaseController
{
    public function refresh(Request $request)
    {
        try {
            $newToken = JWTAuth::setRequest($request)->parseToken()->refresh();
        } catch (TokenExpiredException $e) {
            return $this->sendError(Lang::get('auth.token_expired'), $e->getStatusCode());
        } catch (JWTException $e) {
            return $this->sendError(Lang::get('auth.token_invalid'), $e->getStatusCode());
        }
        return $this->sendResponse(compact('newToken'), 'New Token retrieved successfully');
    }
}