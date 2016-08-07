<?php
namespace App\Http\Requests;
use Illuminate\Support\Facades\Validator;

class WebhookTokenRequest
{
  private static function rules()
  {
    return [
        'hub_verify_token' => 'required|in:'.getenv('VALIDATION_TOKEN'),
        'hub_mode' => 'required|in:subscribe'
    ];
  }

  public static function isValidRequest($request)
  {
    return Validator::make($request->all(), self::rules())->passes();
  }
}
