<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function startAction()
    {
        $firstVideoId = $this->params()->fromPost('firstVideoURL');
        
        $secrets = parse_ini_file('config/autoload/localSecrets.ini');
        $apiKey  = $secrets['YouTubePublicData'];
        
        $request   = 'https://www.googleapis.com/youtube/v3/videos?id=' . $firstVideoId . '&key=' . $apiKey . '&part=snippet';
        $response  = $this->makeCurlRequest($request);
        $channelId = $response['items'][0]['snippet']['channelId'];
        
        $request    = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id=' . $channelId . '&key=' . $apiKey;
        $response   = $this->makeCurlRequest($request);
        $playlistId = $response['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
        
        $videosStack   = new \SplStack();
        $currentIndex  = 0;
        $currentVideos = array();
        $nextPageToken = 'first';
        $successful    = True;
        do {
            if (array_key_exists($currentIndex, $currentVideos) === False) {
                if ($nextPageToken == 'last') {
                    $successful = False;
                    break;
                }
                
                $request       = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50' . ($nextPageToken != 'first' ? '&pageToken=' . $nextPageToken : '') . '&playlistId=' . $playlistId . '&key=' . $apiKey;
                $response      = $this->makeCurlRequest($request);
                
                if (array_key_exists('nextPageToken', $response) === False) {
                    $nextPageToken = 'last';
                } else {
                    $nextPageToken = $response['nextPageToken'];
                }
                $currentVideos = $response['items'];
                $currentIndex  = 0;
            }
            
            $currentVideoId = $currentVideos[$currentIndex]['snippet']['resourceId']['videoId'];
            $videosStack[]  = $currentVideoId;
            $currentIndex++;
        } while ($currentVideoId !== $firstVideoId);
        
        if ($successful) {
            var_dump("successful!");
            var_dump($videosStack);
        } else {
            var_dump("NOT successful");
        }
    }
    
    private function makeCurlRequest($request) {
        $handle   = curl_init($request);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, True);
        $response = json_decode(curl_exec($handle), True);
        $errors   = curl_error($handle);
        curl_close($handle);
        
        if (empty($errors)) {
            return $response;
        } else {
            throw new Exception($errors);
        }
    }
}
