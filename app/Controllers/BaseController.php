<?php

namespace App\Controllers;

use App\Models\CodeModel;
use App\Models\Feature;
use App\Models\onoff;
use App\Models\Server;
use App\Models\UserModel;
use App\Models\_ftext;
use CodeIgniter\Controller;
use Config\Services;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */

class BaseController extends Controller
{
	/**
	 * Instance of the main Request object.
	 *
	 * @var IncomingRequest|CLIRequest
	 */
	protected $request;

	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = ['nata', 'form', 'url', 'text', 'html', 'filesystem'];
  
	/**
	 * Constructor.
	 *
	 * @param RequestInterface  $request
	 * @param ResponseInterface $response
	 * @param LoggerInterface   $logger
	 */
	function getUserIP()
	{
        $clientIp  = @$_SERVER['HTTP_CLIENT_IP'];
        $forwardIp = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remoteIp  = $_SERVER['REMOTE_ADDR'];
        if(filter_var($clientIp, FILTER_VALIDATE_IP))
        {
            $ipaddress = $clientIp;
        }
        elseif(filter_var($forwardIp, FILTER_VALIDATE_IP))
        {
            $ipaddress = $forwardIp;
        }
        else
        {
            $ipaddress = $remoteIp;
        }
        return $ipaddress;
    }
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		$userid = session()->userid;
		if ($userid) {
			$userModel = new UserModel();
			$user = $userModel->getUser($userid);

			if ($user && (((int) $user->level === 1) || ((int) $user->level === 2))) {
				$renderer = Services::renderer();
				$serverModel = new Server();
				$onoffModel = new onoff();
				$ftextModel = new _ftext();
				$featureModel = new Feature();

				$renderer->setData([
					'row' => $serverModel->find(1),
					'onoff' => $onoffModel->find(1),
					'ftext' => $ftextModel->find(1),
					'feature' => $featureModel->find(1),
				]);
			}
		}

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.: $this->session = \Config\Services::session();
	}
}
