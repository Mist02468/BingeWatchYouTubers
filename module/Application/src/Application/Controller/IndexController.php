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
        
        $request  = 'https://www.googleapis.com/youtube/v3/videos?id=' . $firstVideoId . '&key=' . $apiKey . '&part=snippet';
        $handle   = curl_init($request);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, True);
        $response = json_decode(curl_exec($handle), True);
        curl_close($handle);
        
        $channelId = $response['items'][0]['snippet']['channelId'];
    }
}
