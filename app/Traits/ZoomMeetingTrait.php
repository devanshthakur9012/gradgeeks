<?php 
namespace App\Traits;

use GuzzleHttp\Client;
use App\Models\Utility;
use Log;

/**
 * trait ZoomMeetingTrait
 */
trait ZoomMeetingTrait
{
    public $client;
    public $jwt;
    public $headers;
    public $url;
    public function __construct()
    {
        $this->client = new Client();
        $this->url = "https://api.zoom.us/v2/";
    }
    // public function generateZoomToken()
    // {
    //     $settings  = Utility::settings(\Auth::user()->id);
    //     $payload = [
    //         'iss' => $key,
    //         'exp' => strtotime('+1 minute'),
    //     ];

    //     return \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');
    // }
    
    private function retrieveZoomUrl()
    {
        return $this->url;
    }

    public function toZoomTimeFormat(string $dateTime)
    {
        try {
            $date = new \DateTime($dateTime);

            return $date->format('Y-m-d\TH:i:s');
        } catch (\Exception $e) {
            Log::error('ZoomJWT->toZoomTimeFormat : '.$e->getMessage());

            return '';
        }
    }

    public function createmitting($data)
    {
        $path = 'users/me/meetings';
        $url = $this->retrieveZoomUrl();
         
        $body = [
            'headers' => $this->getHeader($data['workspace']),
            'body'    => json_encode([
                'topic'      => $data['topic'],
                'type'       => self::MEETING_TYPE_SCHEDULE,
                'start_time' => $this->toZoomTimeFormat($data['start_time']),
                'duration'   => $data['duration'],
                'password' => $data['password'],
                'agenda'     => (! empty($data['agenda'])) ? $data['agenda'] : null,
                'timezone'     => 'Asia/Kolkata',
                'settings'   => [
                    'host_video'        => ($data['host_video'] == "1") ? true : false,
                    'participant_video' => ($data['participant_video'] == "1") ? true : false,
                    'waiting_room'      => true,
                ],
            ]),
        ];

        $response =  $this->client->post($url.$path, $body);
    
        return [
            'success' => $response->getStatusCode() === 201,
            'data'    => json_decode($response->getBody(), true),
        ];
        
    }

    public function meetingUpdate($id, $data)
    {
        $path = 'meetings/'.$id;
        $url = $this->retrieveZoomUrl();

        $body = [
            'headers' => $this->getHeader(),
            'body'    => json_encode([
                'topic'      => $data['topic'],
                'type'       => self::MEETING_TYPE_SCHEDULE,
                'start_time' => $this->toZoomTimeFormat($data['start_time']),
                'duration'   => $data['duration'],
                'agenda'     => (! empty($data['agenda'])) ? $data['agenda'] : null,
                'timezone'     => config('app.timezone'),
                'settings'   => [
                    'host_video'        => ($data['host_video'] == "1") ? true : false,
                    'participant_video' => ($data['participant_video'] == "1") ? true : false,
                    'waiting_room'      => true,
                ],
            ]),
        ];
        $response =  $this->client->patch($url.$path, $body);

        return [
            'success' => $response->getStatusCode() === 204,
            'data'    => json_decode($response->getBody(), true),
        ];
    }

    public function get($id,$slug)
    {
        $path = 'meetings/'.$id;
        $url = $this->retrieveZoomUrl();

        $body = [
            'headers' => $this->getHeader($slug),
            'body'    => json_encode([]),
        ];
       
            $response =  $this->client->get($url.$path, $body);
            return [
                'success' => $response->getStatusCode() === 204,
                'data'    => json_decode($response->getBody(), true),
            ];
       
        
    }

    /**
     * @param string $id
     * 
     * @return bool[]
     */
    public function delete($id)
    {
        $path = 'meetings/'.$id;
        $url = $this->retrieveZoomUrl();
        $body = [
            'headers' => $this->headers,
            'body'    => json_encode([]),
        ];

        $response =  $this->client->delete($url.$path, $body);

        return [
            'success' => $response->getStatusCode() === 204,
        ];
    }
  
    public function getHeader($slug)
    {
        return [
            'Authorization' => 'Bearer '.$this->getToken($slug),
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
    }
    public function getToken($slug){

        $workspace  = Utility::getWorkspaceBySlug($slug);
        $settings = Utility::getPaymentSetting($workspace->id);
        if (
            isset($settings['zoom_account_id']) && !empty($settings['zoom_account_id']) &&
            isset($settings['zoom_client_id']) && !empty($settings['zoom_client_id']) &&
            isset($settings['zoom_client_secret']) && !empty($settings['zoom_client_secret'])
        ) {
            // Construct the basic authentication header
            $basicAuthHeader = base64_encode($settings['zoom_client_id'] . ':' . $settings['zoom_client_secret']);

            // Prepare the request to obtain the access token
            $response = $this->client->request('POST', 'https://zoom.us/oauth/token', [
                'headers' => [
                    'Authorization' => 'Basic ' . $basicAuthHeader,
                ],
                'form_params' => [
                    'grant_type' => 'account_credentials',
                    "account_id" =>  $settings['zoom_account_id']
                ],
            ]);

            // Decode the response and retrieve the access token
            $token = json_decode($response->getBody(), true);

            if (isset($token['access_token'])) {
                return $token['access_token'];
            }
        }

        return false;
    }

    // public function getToken($slug){
       
    //     $settings  = Utility::getWorkspaceBySlug($slug);
    //     if((isset($settings['zoom_api_key']) && !empty($settings['zoom_api_key'])) && (isset($settings['zoom_api_secret']) && !empty($settings['zoom_api_secret']))){
    //         $key = $settings['zoom_api_key'];
    //         $secret = $settings['zoom_api_secret'];
    //         $payload = [
    //             'iss' => $key,
    //             'exp' => strtotime('+1 minute'),
    //         ];
            
    
    //         return \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');
    //     }else{
    //         return false;
    //     }
       
    // }


}

 ?>