<?php
namespace App\Http\Controllers;

use Log;
use App\Http\Requests\WebhookTokenRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use FBMessageSender;


class HiddenWisdomController extends Controller
{
    public function verify(Request $request)
    {
        if (WebhookTokenRequest::isValidRequest($request)) {
            return response($request->input('hub_challenge'));
        }
        return response("",403);
    }

    public function handleMessage(Request $request) {
        $messageEntries = $request->get('entry');
        Log::info(json_encode($request->all()));
        if(!$messageEntries) return response('Message Not Understood', 400);
        foreach($messageEntries as $entry) {
            $messaging = $entry['messaging'];
            foreach($messaging as $messagingEvent) {
                if(isset($messagingEvent['message']) && !empty($messagingEvent['message'])){
                    $entryMessageText = $messagingEvent['message']['text'];
                    $entryMessageSenderId = $messagingEvent['sender']['id'];
                    FBMessageSender::send($entryMessageSenderId, [ 'text' => $entryMessageText ]);
                    return response($entryMessageText, 200);
                }
            }
        }
        return response('Message Not Understood', 400);
    }
}
