<?php
namespace App\Http\Controllers;

use App\Http\Requests\WebhookTokenRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class HiddenWisdomController extends Controller
{
    public function verify(Request $request)
    {
        if (WebhookTokenRequest::isValidRequest($request)) {
            return response($request->input('hub_challenge'));
        }
        return response("",403);
    }
}
