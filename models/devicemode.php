<?php

class Devicemodel extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }
    //useful
	function fetch_device_detail()
	{
		$sql="select * from device where client_id = 0 ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	function fetch_device_detail_by_id($device_id)
	{
		$sql="select * from device where device_id = '$device_id' ";
		$res = $this->db->query($sql); 
		return $rre = $res->row();
	}

	function fetch_assigned_device()
	{
		$sql="select d.*,u.first_name,u.last_name from device d,users u where d.client_id = u.user_id  ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	function fetch_client_device_by_id($client_id)
	{
		//$sql="SELECT * from device d, vehicle_device vd where d.device_id = vd.d_id and vd.assign_user_id = '$client_id' ";
		$sql="SELECT * from device d, vehicle_device vd where d.device_id = vd.d_id and vd.client_id = '$client_id' ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	function insert_device_detail($data)
	{	
		$this->db->insert('device', $data);
        return $this->db->insert_id();
		// $sql="INSERT INTO device (device_unique_id,device_name,status,device_number,color,driver_name,registration_number) 
		// VALUES($device_unique_id,'$device_name',$status,$device_number,'$color','$driver_name','$reg_number')" ;
		// $res = $this->db->query($sql);
		// return $rre = $res->row();
	}

	function update_device_detail($data)
	{
		//$sql="update device set client_id = '$client_id' and assigned_date = '$assigned_date' where device_id = '$device_id' ";
		$this->db->where('device_id',$data['device_id']);
		$this->db->update('device', $data);
		
	}
	
	function update_user_device_detail($device_id,$client_id)
	{
		$sql="update device set client_id = '$client_id' where device_id = '$device_id' ";
		$res = $this->db->query($sql); 
		return $rre = $res->row();
	}

	function fetch_client_device_detail($user_id)
	{
		$sql="select * from device where client_id= '$user_id' ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	function act_deact_client_device($device_id,$status)
	{
		$sql="update device set status = '$status' where device_id = '$device_id' ";
		$res = $this->db->query($sql);
	}

	//useful
	function fetch_device_by_status($status,$client_id)
	{
		if($status == 'all')
		{
			$sql="SELECT * from device d, vehicle_device vd, users u where u.user_id = d.client_id and d.device_id = vd.d_id and vd.client_id = '$client_id' and vd.is_active = 1 order by d.last_ping desc ";
			//$sql = "SELECT * from device where client_id = $client_id and status=1 ";
		}
		else
		{
			$sql = "select * from device join users on device.client_id = users.user_id 
						where device.status = '$status' group by device.last_ping ";	
		}

		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}


	//useful
	function fetch_device_by_client_id($client_id)
	{
		$sql="SELECT *, TIMESTAMPDIFF(SECOND,d.last_ping,current_timeStamp) as diff 
			 from device d, vehicle_device vd , users u where u.user_id = d.client_id and d.device_id = vd.d_id 
			 and vd.client_id = '$client_id' and vd.is_active = 1 order by d.last_ping desc ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}


	function fetch_client_device_location($user_id)
	{
		$sql="SELECT locations.* FROM locations,device where locations.device_unique_id = device.device_unique_id and device.status=1 and device.client_id = $user_id ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	function single_device_lat_long_locations($client_id,$device_id)
	{	
		$sql="SELECT * from device d, vehicle_device vd, users u 
				where  u.user_id = d.client_id and d.device_id = vd.d_id 
				and vd.client_id = '$client_id' and vd.is_active = 1 
				and d.device_id = '$device_id' order by d.last_ping desc ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}


	//useful
	function fetch_locations()
	{
		$sql="SELECT locations.*,device.device_name FROM locations,device 
				where locations.device_id = device.device_id 
				and device.status=1 ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;

	}
	
	//useful
	function fetch_locations_by_client_id($client_id)
	{
		$sql="SELECT locations.*,device.device_name FROM locations,device 
				where locations.device_id = device.device_id 
				and device.status=1 and device.client_id = $client_id ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;

	}

	function fetch_locations_device_grouped($second,$client_id,$device_id)
	{
		if($client_id == 'all' && $device_id == 'all')
		{	
			//select * from device left join users on device.client_id = users.user_id
			$sql1 = "SELECT * FROM device left join users on device.client_id = users.user_id 
			where device.status=1 ";	//for admin all device
		}

		if($client_id != 'all' && $device_id == 'all')
		{
			$sql1 = "SELECT * FROM device where status=1 and client_id=$client_id ";
			//for client or user all device	
		}

		if($device_id != 'all')
		{
			$sql1 = "SELECT * FROM device where device_id = $device_id ";	
			//for single device
		}
		
		$res1 = $this->db->query($sql1);
		$num = $res1->num_rows();
		
		$location_arr = array();
		
		foreach ($res1->result_array() as $value)
		{
			$device_id = $value['device_id'];
			$sql2 = "SELECT * FROM locations where device_id = $device_id 
						GROUP BY ROUND(UNIX_TIMESTAMP(ping_datetime)/( $second )) ";
			$res2 = $this->db->query($sql2);
			$temp = $res2->result_array();			
			$device_arr = array('device_row'=>$value,'location_table'=>$temp);

			array_push($location_arr, $device_arr);
		}

		$retArr = array('numResults' => $num, 'results' =>  $location_arr );
		return $retArr;
	}

	function fetch_device_for_route_map($device_value,$client_id)
	{
		if($device_value== 'all')
		{
			$sql="select * from locations join device on locations.device_id = device.device_id ";	
		}
		else
		{	
			$sql="select * from locations join device on locations.device_id = device.device_id where status = '$device_value' ";	
		}		
		
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	//usefull
	function single_device_locations($device_id,$date1,$second,$time1,$time2)
	{	

		$sql="SELECT * FROM locations join vehicle_device on locations.device_id = vehicle_device.d_id 
			where locations.ping_datetime BETWEEN '$date1 $time1' and '$date1 $time2' and locations.device_id = '$device_id'
			GROUP BY ROUND(UNIX_TIMESTAMP(ping_datetime)/( $second ))
			ORDER BY ping_datetime DESC ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	function single_device_locations_grouped($device_id,$date1,$second,$time1,$time2)
	{	
		$sql1 = "SELECT * FROM vehicle_device vd , users u ,device d 
					 where vd.client_id = u.user_id and vd.is_active = 1 
					 and vd.d_id = d.device_id and d.device_id='$device_id' ";	
	
		$res1 = $this->db->query($sql1);
		$num = $res1->num_rows();
		
		$location_arr = array();
		
		foreach ($res1->result_array() as $value)
		{
			$device_id = $value['d_id'];
			$sql2 = "SELECT * FROM locations where ping_datetime BETWEEN '$date1 $time1' and '$date1 $time2'
			and device_id = $device_id and lat!='0' and lng != '0'
			GROUP BY ROUND(UNIX_TIMESTAMP(ping_datetime)/( $second ))";
			$res2 = $this->db->query($sql2);
			$temp = $res2->result_array();			
			$device_arr = array('device_row'=>$value,'location_table'=>$temp);

			array_push($location_arr, $device_arr);
		}

		$retArr = array('numResults' => $num, 'results' =>  $location_arr );
		return $retArr;

	}
	
	//usefull
	function fetch_time_arr($client_id,$device_id,$second)
	{	
		if($device_id== 'all')
		{
			$sql="SELECT * FROM locations join device on locations.device_id = device.device_id 
				where device.status = 1
					GROUP BY ROUND(UNIX_TIMESTAMP(ping_datetime)/( $second )) ";	
		}
		else
		{
			$sql="SELECT * FROM locations join device on locations.device_id = device.device_id 
				where locations.device_id = $device_id and device.status = 1
						GROUP BY ROUND(UNIX_TIMESTAMP(ping_datetime)/( $second )) ";
		}	
		
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}
	//usefull
	function fetch_all_locations_device($second,$client_id,$date1,$date2)
	{
		if($client_id=='all')
		{
			$sql="SELECT device.device_name,locations.* FROM locations 
				join device on locations.device_id = device.device_id 
				where (locations.ping_datetime BETWEEN '$date1' and '$date2') and device.status = 1 
				 GROUP BY ROUND(UNIX_TIMESTAMP(ping_datetime)/( $second )) ";
		}
		else
		{
			$sql="SELECT device.device_name,locations.* FROM locations 
				join device on locations.device_id = device.device_id 
				where (locations.ping_datetime BETWEEN '$date1' and '$date2') and device.status = 1 
				and device.client_id = $client_id GROUP BY ROUND(UNIX_TIMESTAMP(ping_datetime)/( $second )) ";
		}
				
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	function device_detail_by_id($device_id)
	{
		$sql="select * from device where device_id = '$device_id' ";
		$res = $this->db->query($sql); 
		return $rre = $res->row();
	}

	function update_vehicle_device_detail($data)
	{
		$this->db->where('device_id',$data['device_id']);
		$this->db->update('device', $data);
		print_r($data);
		return;

	}

	function fetch_device_detail_group($device_id)
	{
		$sql="select * from device where device_id = '$device_id' ";
		$res = $this->db->query($sql); 
		$rre = $res->row()->client_id;

		//$sql2="SELECT * from `group` join group_device where group.client_id = $rre and group_device.dev_id = '$device_id'";
		$sql2="SELECT * from `group`  where client_id = $rre ";
		$res2 = $this->db->query($sql2); 
		$num = $res2->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res2->result());
		return $retArr;
	}

	function fetch_group_by_id($status,$client_id)
	{	
		if($status == 'all')
		{
			$sql="SELECT * from `group` ";
			$res = $this->db->query($sql);
			$num = $res->num_rows();
			$retArr = array('numResults' => $num, 'results' => $res->result());
			return $retArr;
		}
		else
		{
			$sql="SELECT * from `group` where client_id = $client_id ";
			$res = $this->db->query($sql);
			$num = $res->num_rows();
			$retArr = array('numResults' => $num, 'results' => $res->result());
			return $retArr;
		}
		
	}

	function fetch_device_by_group($group_id)
	{
		$sql = "SELECT * from device join users on device.client_id = users.user_id 
				where device.group_id = '$group_id' and device.status = 1 ";

		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	function device_locations_by_group($group_id,$second)
	{
		$sql="SELECT * FROM locations join device on locations.device_id = device.device_id 
				where device.group_id = '$group_id'
				GROUP BY ROUND(UNIX_TIMESTAMP(ping_datetime)/( $second )) ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
		
	}


	

	function device_locations_by_group_grouped($group_id,$second)
	{		
		$sql1 = "SELECT * FROM device where device.status=1 and device.group_id = $group_id ";
		
		$res1 = $this->db->query($sql1);
		$num = $res1->num_rows();
		
		$location_arr = array();
		
		foreach ($res1->result_array() as $value)
		{
			$device_id = $value['device_id'];
			$sql2 = "SELECT * FROM locations where device_id = $device_id 
						GROUP BY ROUND(UNIX_TIMESTAMP(ping_datetime)/( $second )) ";
			$res2 = $this->db->query($sql2);
			$temp = $res2->result_array();			
			$device_arr = array('device_row'=>$value,'location_table'=>$temp);

			array_push($location_arr, $device_arr);
		}

		$retArr = array('numResults' => $num, 'results' =>  $location_arr );
		return $retArr;
	}

	function fetch_address_by_lat_lng($lat,$lng)
	{
		$sql = "SELECT * FROM locations where lat = '".$lat."' and lng ='".$lng."' ";		
		$res = $this->db->query($sql);
		return $res->row();
	}

	function insert_address_db_rev_geo()
	{
		//$sql = "SELECT * FROM locations where address='' group by lat,lng 
		//		order by ping_datetime desc limit 5 ";
		$sql = "SELECT * FROM locations WHERE address = '' ORDER BY ping_datetime DESC LIMIT 10 ";
		$res = $this->db->query($sql);
		//$retArr = array('numResults' => $res->num_rows(), 'results' =>  $res->result() );
		//return $retArr;

		foreach ($res->result_array() as $value)
		{	
			$lat = $value['lat'];
			$lng = $value['lng'];

			/*
			$sqlcheck = "SELECT * FROM locations where lat = '".$lat."' 
							and lng ='".$lng."' and address !='' ";		
			$rescheck = $this->db->query($sqlcheck);			
			if($rescheck->row() )
			{
				//echo 'found';
				//print_r( $res->row() );	
				// $address = $rescheck->row()->address;
				// $locations_id = $rescheck->row()->locations_id;
				// echo $sqlupdate = "UPDATE locations set address = '$address' 
				// 						where locations_id = $locations_id ";	
				//$resupdate = $this->db->query($sqlupdate);

			}
			else
			{
				// $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
				// $json = @file_get_contents($url);
				// $data=json_decode($json);
				// //print_r($data);
				// $status = $data->status;
				// if($status=="OK")
				// echo $data->results[0]->formatted_address;
				// else
				// echo 'Address not recognized';
				// $address = 'from cron';
				// echo $sqlupdate = "UPDATE locations set address = '$address' 
				// 					where lat = '$lat' and lng = '$lng' ";	
				// $resupdate = $this->db->query($sqlupdate);
			}
			*/

			$url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
			$json = @file_get_contents($url);
			$data=json_decode($json);

			$status = $data->status;			
			$city = '';
			$district = '';
			$state = '';

			if($status=="OK")
			{
				$address = $data->results[0]->formatted_address;	
				
				$address2 = $data->results[0]->address_components;
				$city = $address2[1]->short_name ;
				$district = $address2[2]->short_name ;
				$state = $address2[3]->short_name ;
			}
			$address_small = $city.', '.$district.', '.$state;

			// print_r($address);
			// echo '<br><br>';

			// print_r($city);
			// echo '<br><br>';

			// print_r($district);
			// echo '<br><br>';

			// print_r($address_small);
			// return;			
			
			$status = $data->status;
			if($status=="OK")
			{
				$address = $data->results[0]->formatted_address;	
				
				$sqlupdate = "UPDATE locations set address = '$address' 
								where address='' and lat = $lat and lng = $lng ";	
				$resupdate = $this->db->query($sqlupdate);

				$sqlupdate = "UPDATE device set last_location = '$address_small' where lat = $lat and lng = $lng ";	
				$resupdate = $this->db->query($sqlupdate);
			}			
			else
			{
				//echo 'Address not recognized';
			}
			return;

			sleep(1);
						
		}

	}	

	/**
	 * Calculates the great-circle distance between two points, with
	 * the Vincenty formula.
	 * @param float $latitudeFrom Latitude of start point in [deg decimal]
	 * @param float $longitudeFrom Longitude of start point in [deg decimal]
	 * @param float $latitudeTo Latitude of target point in [deg decimal]
	 * @param float $longitudeTo Longitude of target point in [deg decimal]
	 * @param float $earthRadius Mean earth radius in [m]
	 * @return float Distance between points in [m] (same as earthRadius)
	 */
	public static function vincentyGreatCircleDistance_new(
  	$latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
	{
		if($latitudeFrom == 0 || $longitudeFrom == 0 || $latitudeTo == 0 || $longitudeTo == 0)
		{
			return 0;
		}
	  // convert from degrees to radians
	  $latFrom = deg2rad($latitudeFrom);
	  $lonFrom = deg2rad($longitudeFrom);
	  $latTo = deg2rad($latitudeTo);
	  $lonTo = deg2rad($longitudeTo);

	  $lonDelta = $lonTo - $lonFrom;
	  $a = pow(cos($latTo) * sin($lonDelta), 2) +
	    pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
	  $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

	  $angle = atan2(sqrt($a), $b);
	  //return ($angle * $earthRadius)/1000;
	  $distance = ($angle * $earthRadius)/1000;
	  if($distance > 1000)
	  {
	  		return 0;
	  }else
	  {
	  	return $distance;
	  }

	}

	function insert_distance()
	{
		/*
		UPDATE `gpstracker`.`locations` SET `dist_calculated` = '0' ;
		UPDATE `gpstracker`.`locations` SET `travel_dist` = '0';
		UPDATE `gpstracker`.`device` SET `total_travel` = '0';
		UPDATE `gpstracker`.`locations` SET `dist_calculated` = '0' ;
		*/
		ini_set('max_execution_time', 0); //0=NOLIMIT
		$sql = "SELECT * FROM locations where dist_calculated=0 
		order by ping_datetime asc  ";
		$res = $this->db->query($sql);

		foreach ($res->result_array() as $value)
		{
			// print_r($value);
			$lat = $value['lat'];
			$lng = $value['lng'];
			$locations_id = $value['locations_id'];			
			
			$device_id = $value['device_id'];
			$ping_datetime = $value['ping_datetime'];
			$sqlprevious = "SELECT * FROM locations where device_id = $device_id 
				and ping_datetime < '$ping_datetime' order by ping_datetime DESC limit 1 ";
			$resprevious = $this->db->query($sqlprevious);

			if($resprevious->row() )
			{
				$latprevious = $resprevious->row()->lat;
				$lngprevious = $resprevious->row()->lng;
				
				$distance = $this->vincentyGreatCircleDistance_new($lat,$lng,$latprevious,$lngprevious);

				if($this->check_if_dgroup($device_id) )
				{
					//update locations set travel_dist = 1.2*travel_dist
					$distance *= 1.2;					
				}
				
				$sqlupdate = "UPDATE locations set travel_dist = $distance ,
									dist_calculated=1 where locations_id = $locations_id ";	
				$resupdate = $this->db->query($sqlupdate);

				$sql_total_update = "UPDATE device set total_travel = total_travel + $distance 
										where device_id = $device_id";
				$res_total = $this->db->query($sql_total_update);


			}//if old row
			//else
			//{
				// $sqlupdate = "UPDATE locations set dist_calculated=0
				// where locations_id = $locations_id ";	
				// $resupdate = $this->db->query($sqlupdate);
			//}

		}//main for loop 

	}//insert distance function end

	function update_device_group($data)
	{
		$this->db->insert('group_device', $data);
		//print_r($data);
		//return;
	}

	function delete_device_group($device_id)
	{
		$this->db->where('dev_id', $device_id);
		$this->db->delete('group_device');
	}
	
	function insert_device_vehicle($data)
	{
		$this->db->insert('vehicle_device', $data);
    	return $this->db->insert_id();
	}

	function fetch_vehicle_device_detail($vd_id,$mode,$client_id)
	{
		if($mode == 1)
		{
			$sql = "SELECT vehicle_device.*, group_concat(group_device.grp_id) as grpid 
					from vehicle_device, group_device where vehicle_device.vd_id = '$vd_id' 
					and group_device.dev_id = vehicle_device.vd_id ";
			
			$res = $this->db->query($sql);
			return $res->row();
		}
		else
		{
			$sql = "SELECT * from vehicle_device where d_id = 0 and client_id = '$client_id' ";
			$res = $this->db->query($sql);
			$num = $res->num_rows();
			$retArr = array('numResults' => $num, 'results' => $res->result());
			return $retArr;
		}
		

	}

	function update_device_vehicle($data)
	{	
		$this->db->where('vd_id',$data['vd_id']);
		$this->db->update('vehicle_device', $data);
	}

	function fetch_vehicle_device_detail_all($client_id)
	{
		$sql = "SELECT *,vd.client_id as client_id from vehicle_device vd left join device d on vd.d_id = d.device_id where vd.client_id = '$client_id' ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;

	}

	function fetch_vehicle_unassigned_device($client_id)
	{
		$sql = "SELECT * FROM `device` WHERE client_id='$client_id' 
				and device_id not in (select d_id from vehicle_device
					where client_id = '$client_id' )";
   				
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}
	
	function fetch_user_vehicle_device($assign_user_id)
	{
		//$sql = "SELECT * FROM vehicle_device vd, device d where vd.assign_user_id = '$assign_user_id' ";
		$sql = "SELECT vd.*,d.device_name,d.device_number from vehicle_device vd,device d 
				where find_in_set('$assign_user_id', vd.assign_user_id  ) and vd.d_id = d.device_id ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	function fetch_device_by_group_id($group_id)
	{
		$sql="SELECT * from device d ,group_device gd 
				where gd.dev_id = d.device_id and gd.grp_id = $group_id";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	//useful
	function fetch_device_by_assigned_user($client_id)
	{
		$sql="SELECT * from device d join vehicle_device vd, users u 
				where u.user_id = '$client_id' and find_in_set('$client_id', vd.assign_user_id  ) and d.device_id = vd.d_id and d.status = vd.is_active order by d.last_ping desc ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	function user_single_device_locations($client_id,$device_id)
	{	
		$sql="SELECT * from device d, vehicle_device vd, users u 
				where find_in_set('$client_id', vd.assign_user_id  ) and  u.user_id = d.client_id 
				and d.device_id = vd.d_id and vd.is_active = 1 and d.device_id = '$device_id' ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	function fetch_user_device_by_group_id($group_id,$client_id)
	{
		$sql="SELECT * from device d , vehicle_device vd 
					where find_in_set('$client_id', vd.assign_user_id  ) 
				 and vd.group_id = $group_id and d.device_id = vd.d_id ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	function single_device_fuel_detail($device_id,$date1,$time)
	{
		$sql = "SELECT * FROM locations where date(ping_datetime) ='$date1' 
		and device_id = $device_id 
		GROUP BY ROUND(UNIX_TIMESTAMP(ping_datetime)/( $time ))  ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;

	}

	function fetch_vehicle_details($vehicle_id)
	{
		$sql = "SELECT * from vehicle_device vd ,users u 
				where vd.vd_id = $vehicle_id 
				and (vd.client_id=u.user_id or find_in_set(u.user_id, vd.assign_user_id  ) ) ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	function check_if_dgroup($device_id)
	{
		$sql ="SELECT `d_group` from users,device where device.client_id = users.user_id and device.device_id=$device_id";
		$res = $this->db->query($sql);
		if($res->row())
		{
			return $res->row()->d_group;
		}
		else
		{
			return 0;
		}

	}

	function device_detail_for_marker($device_id,$status,$client_id)
	{
		$curr_time = date('Y-m-d H:i:s');
		$old_time = date('Y-m-d H:i:s',strtotime("-5 minutes",strtotime($curr_time)));

		if($status =='all')
		{
	        $sql = "SELECT d_id, ifnull(sum(travel_dist),0) as travel_sum ,count(*) as ping_count 
			        FROM vehicle_device left join locations on locations.device_id = vehicle_device.d_id 
			        and ping_datetime > '".$old_time."' where vehicle_device.client_id = $client_id
			        order by locations.ping_datetime ";
			$res = $this->db->query($sql);
			$num = $res->num_rows();
			$retArr = array('numResults' => $num, 'results' => $res->result());
			return $retArr;

		}
		else 
		{  
			$sql = "SELECT d_id, ifnull(sum(travel_dist),0) as travel_sum ,count(*) as ping_count 
			        FROM vehicle_device left join locations on locations.device_id = vehicle_device.d_id 
			        and ping_datetime > '".$old_time."' WHERE vehicle_device.d_id = $device_id locations.ping_datetime ";
			$res = $this->db->query($sql);
			$num = $res->num_rows();
			$retArr = array('numResults' => $num, 'results' => $res->result());
			return $retArr;
		}
		

	}

	function device_lat_long_trackonmap($client_id,$device_id)
	{	
		$sql="SELECT * from device d, vehicle_device vd, users u 
				where  u.user_id = d.client_id and d.device_id = vd.d_id 
				and vd.client_id = '$client_id' and vd.is_active = 1 
				and d.device_id in($device_id) order by d.last_ping desc ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

	function device_marker_trackonmap($device_id,$client_id)
	{
		$curr_time = date('Y-m-d H:i:s');
		$old_time = date('Y-m-d H:i:s',strtotime("-5 minutes",strtotime($curr_time)));

		$sql = "SELECT d_id, ifnull(sum(travel_dist),0) as travel_sum ,count(*) as ping_count 
		        FROM vehicle_device left join locations on locations.device_id = vehicle_device.d_id 
		        and ping_datetime > '".$old_time."' WHERE vehicle_device.d_id in($device_id) group by d_id ";
		$res = $this->db->query($sql);
		$num = $res->num_rows();
		$retArr = array('numResults' => $num, 'results' => $res->result());
		return $retArr;
	}

}
