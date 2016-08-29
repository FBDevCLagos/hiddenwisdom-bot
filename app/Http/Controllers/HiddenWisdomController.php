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

    public function getProverb($lang, $tag, Request $request)
    {
        $path = storage_path() . "/json/proverbs.json";
        $proverbs = json_decode(file_get_contents($path), true)['proverbs'];
        for ($i = 0; $i < count($proverbs); $i++) {
            if ($proverbs[$i]["languange"] == $lang &&
                in_array($tag, $proverbs[$i]["tags"]) && $proverbs[$i]["status"] == "approved") {
                return response()->json($proverbs[$i]);
            }
        }
        return response("proverb not found", 404);
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
