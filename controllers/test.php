<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Device extends REST_Controller
{
    //useful
 	function fetch_device_detail_get()
    {
        $a = $this->devicemodel->fetch_device_detail();
        $this->response($a);
    }

    function fetch_device_detail_post()
    {
        $a = $this->devicemodel->fetch_device_detail();
        $this->response($a);
    }

    function fetch_device_detail_by_id_get()
    {
        $a = $this->devicemodel->fetch_device_detail_by_id( $this->get('device_id'));
        $this->response($a);
    }

    function fetch_device_detail_by_id_post()
    {
        $a = $this->devicemodel->fetch_device_detail_by_id( $this->post('device_id'));
        $this->response($a);
    }


}	
