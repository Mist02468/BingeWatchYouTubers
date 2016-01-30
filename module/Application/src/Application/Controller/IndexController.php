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
    //static home page
    public function indexAction()
    {
        return new ViewModel();
    }
    
    //start page, plays the videos after submitting the index/home page's form
    public function startAction()
    {
        //get the video id submitted via a post request from the home page
        $firstVideoId = $this->params()->fromPost('firstVideoURL');
        
        //get the YouTube API key from my local file
        $secrets = parse_ini_file('config/autoload/localSecrets.ini');
        $apiKey  = $secrets['YouTubePublicData'];
        
        //make the initial API request, to get the channel id for the submitted first video
        $request   = 'https://www.googleapis.com/youtube/v3/videos?id=' . $firstVideoId . '&key=' . $apiKey . '&part=snippet';
        $response  = $this->makeCurlRequest($request);
        $channelId = $response['items'][0]['snippet']['channelId'];
        
        //make the other preliminary API request, to get the uploads playlist id for this channel
        $request    = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id=' . $channelId . '&key=' . $apiKey;
        $response   = $this->makeCurlRequest($request);
        $playlistId = $response['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
        
        //API will return videos in an order opposite (most recent first) of what we want, so put them on a stack
        $videosStack   = new \SplStack();
        //keep track of the index of the current video within a response
        $currentIndex  = 0;
        //hold the array of videos within a response
        $currentVideos = array();
        //hold the API next page token from Google, or these special 'first' and 'last' enums
        $nextPageToken = 'first';
        //keep this boolean in case the desired first video is never found
        $successful    = True;
        do {
            if (array_key_exists($currentIndex, $currentVideos) === False) {
                //in case the desired first video is never found, if there are no more pages of videos from the API to look through
                if ($nextPageToken == 'last') {
                    $successful = False;
                    break;
                }
                
                //make an API request to look up another page of videos from this channel's uploads
                $request  = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&maxResults=50' . ($nextPageToken != 'first' ? '&pageToken=' . $nextPageToken : '') . '&playlistId=' . $playlistId . '&key=' . $apiKey;
                $response = $this->makeCurlRequest($request);
                
                //get and store the nextPageToken from this API response
                if (array_key_exists('nextPageToken', $response) === False) {
                    $nextPageToken = 'last';
                } else {
                    $nextPageToken = $response['nextPageToken'];
                }
                //get and store the videos from this API response
                $currentVideos = $response['items'];
                //reset the index, since we're iterating over a new page
                $currentIndex  = 0;
            }
            
            //get the video id
            $currentVideoId = $currentVideos[$currentIndex]['snippet']['resourceId']['videoId'];
            //put it on the stack
            $videosStack[]  = $currentVideoId;
            //prepare to explore the next video, if possible and necessary
            $currentIndex++;
        } while ($currentVideoId !== $firstVideoId); //can stop looping once the desired first video is found
        
        if ($successful) {
            //turn the stack of videos into an ordered string (space as a delimiter), for JavaScript to process
            $videosString = '';
            while ($videosStack->isEmpty() === False) {
                $videosString .= ($videosStack->pop() . ' ');
            }
            $videosString = trim($videosString);
            return new ViewModel(array('videosString' => $videosString)); //pass that string to the view, for JavaScript to process
        } else {
            var_dump("NOT successful"); //let me know there's still debugging for me to do
        }
    }
    
    //handles the curl functions needed to make an API request
    private function makeCurlRequest($request) {
        //initialize curl with the url of the request
        $handle   = curl_init($request);
        //get the response as a string rather than outputting it
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, True);
        //process the response as json, decode it and turn objects into associative arrays
        $response = json_decode(curl_exec($handle), True);
        //check for errors
        $errors   = curl_error($handle);
        //close the curl handle
        curl_close($handle);
        
        //if there are no errors, return the processed response
        if (empty($errors)) {
            return $response;
        } else {
            throw new Exception($errors); //let me know there's still debugging for me to do
        }
    }
}
