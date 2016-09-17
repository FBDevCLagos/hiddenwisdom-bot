<?php
namespace App\Http\Controllers;

use Log;
use App\Http\Requests\WebhookTokenRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\Client;

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

    public function addMenu(Request $request)
    {
        FBMessageSender::sendPostBackActions();
        return response("",200);
    }

    public function getProverb(Request $request)
    {
        $lang = $request->query('lang');
        $tag = $request->query('tag');
        if(!$lang && !$tag) return response()->json(['body' => 'Proverb not Found']);
        $path = storage_path() . "/json/proverbs.json";
        $proverbs = json_decode(file_get_contents($path), true)['proverbs'];
        for ($i = 0; $i < count($proverbs); $i++) {
            if ($proverbs[$i]["language"] == $lang &&
                in_array(strtolower($tag), $proverbs[$i]["tags"]) && $proverbs[$i]["status"] == "approved") {
                return response()->json(['proverbs' => [$proverbs[$i]]]);
            }
        }
        return response()->json(['proverbs' => [['body' => 'Proverb not Found']]]);
    }

    public function handleMessage(Request $request) {
        $messageEntries = $request->get('entry');
        Log::info(json_encode($request->all()));
        $msgError = 'Message Not Understood';
        if(!$messageEntries) return response($msgError, 400);
        foreach($messageEntries as $entry) {
            $messaging = $entry['messaging'];
            foreach($messaging as $messagingEvent) {
                if(isset($messagingEvent['message']) && !empty($messagingEvent['message'])){
                    return $this->handleMessageCommand($messagingEvent);
                }
                if(isset($messagingEvent['postback']) && !empty($messagingEvent['postback'])){
                    $payload = $messagingEvent['postback']['payload'];
                    $entryMessageSenderId = $messagingEvent['sender']['id'];
                    switch($payload){
                        case 'HELP':
                             FBMessageSender::send($entryMessageSenderId, [
                                'text' => "HELP\n".
                                          "===========\n".
                                          "commands:\n".
                                          "proverbs {language} {tag}\n".
                                          "\t\tproverbs english unity\n".
                                          "proverbs random\n\n"
                                ]);
                        default:
                            FBMessageSender::send($entryMessageSenderId, [ 'text' => 'Not understood' ]);
                    }
                }
                return response("",200);
            }
        }
        return response($msgError, 400);
    }

    private function handleMessageCommand($messagingEvent) {
        $msgError = 'Message Not Understood';
        $entryMessageText = $messagingEvent['message']['text'];
        Log::info($entryMessageText);
        $entryMessageSenderId = $messagingEvent['sender']['id'];
        $client = new Client(['base_uri' => getenv('HW_HOST')]);
        $searchValues = explode(" ", $messagingEvent['message']['text']);
        // Typing On
        FBMessageSender::sendArray($entryMessageSenderId, ['sender_action' => 'typing_on']);
        if ( count($searchValues) < 3) {
            FBMessageSender::send($entryMessageSenderId, ['text' => $msgError]);
            // Typing Off
            FBMessageSender::sendArray($entryMessageSenderId, ['sender_action' => 'typing_off']);
            return response($msgError, 200);
        }
        $response = $client->get('api/v1/proverbs?lang='.$searchValues[1].'&tag='.$searchValues[2]);
        $body = $response->getBody();
        $jsonDecode = json_decode($response->getBody(), true);
        FBMessageSender::send($entryMessageSenderId, ['text' => $jsonDecode['proverbs'][0]['body']]);
        // Typing Off
        FBMessageSender::sendArray($entryMessageSenderId, ['sender_action' => 'typing_off']);
        return response($entryMessageText, 200);
    }
}
