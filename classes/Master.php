<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function delete_img(){
		extract($_POST);
		if(is_file($path)){
			if(unlink($path)){
				$resp['status'] = 'success';
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = 'failed to delete '.$path;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = 'Unkown '.$path.' path';
		}
		return json_encode($resp);
	}
	function save_category(){
		$_POST['description'] = addslashes(htmlspecialchars($_POST['description']));
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `category_list` where `name` = '{$name}' and delete_flag = 0 ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Category already exists.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `category_list` set {$data} ";
		}else{
			$sql = "UPDATE `category_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['cid'] = $cid;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New Category successfully saved.";
			else
				$resp['msg'] = " Category successfully updated.";
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_category(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `category_list` set `delete_flag` = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Category successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_product(){
		if(isset($_POST['descrption']))
		$_POST['descrption'] = addslashes(htmlspecialchars($_POST['descrption']));

		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `product_list` where `brand` = '{$brand}' and `name` = '{$name}' and delete_flag = 0 ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Product already exists.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `product_list` set {$data} ";
		}else{
			$sql = "UPDATE `product_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$pid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['pid'] = $pid;
			$resp['status'] = 'success';
			if(empty($id)){
				$resp['msg'] = 'Product has been addedd successfully';
			}else{
				$resp['msg'] = " Product has been updated successfully.";
			}

			if(!empty($_FILES['img']['tmp_name'])){
				$img_path = "uploads/medicines/";
				if(!is_dir(base_app.$img_path)){
					mkdir(base_app.$img_path);
				}
				$accept = array('image/jpeg','image/png');
				if(!in_array($_FILES['img']['type'],$accept)){
					$resp['msg'] += " Image file type is invalid";
				}else{
					if($_FILES['img']['type'] == 'image/jpeg')
						$uploadfile = imagecreatefromjpeg($_FILES['img']['tmp_name']);
					elseif($_FILES['img']['type'] == 'image/png')
						$uploadfile = imagecreatefrompng($_FILES['img']['tmp_name']);
					if(!$uploadfile){
						$resp['msg'] +=  " Image is invalid";
					}else{
						list($width, $height) =getimagesize($_FILES['img']['tmp_name']);
						if($width > 640 || $height > 480){
							if($width > $height){
								$perc = ($width - 640) / $width;
								$width = 640;
								$height = $height - ($height * $perc);
							}else{
								$perc = ($height - 480) / $height;
								$height = 480;
								$width = $width - ($width * $perc);
							}
						}
						$temp = imagescale($uploadfile,$width,$height);
						$spath = $img_path.'/'.$_FILES['img']['name'];
						$i = 1;
						while(true){
							if(is_file(base_app.$spath)){
								$spath = $img_path.'/'.($i++).'_'.$_FILES['img']['name'];
							}else{
								break;
							}
						}
						if($_FILES['img']['type'] == 'image/jpeg')
						$upload = imagejpeg($temp,base_app.$spath,60);
						elseif($_FILES['img']['type'] == 'image/png')
						$upload = imagepng($temp,base_app.$spath,6);
						if($upload){
							$this->conn->query("UPDATE product_list set image_path = CONCAT('{$spath}', '?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$pid}' ");
						}

						imagedestroy($temp);
					}
				}
			}
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] == 'success' && isset($resp['msg']))
		$this->settings->set_flashdata('success', $resp['msg']);
		return json_encode($resp);
	}
	function delete_product(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `product_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Product successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_stock(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `stock_list` where `code` = '{$code}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Code is already taken.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `stock_list` set {$data} ";
		}else{
			$sql = "UPDATE `stock_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['cid'] = $cid;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New Stock successfully saved.";
			else
				$resp['msg'] = " Stock successfully updated.";
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_stock(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `stock_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Stock successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function add_to_card(){
		extract($_POST);
		$check = $this->conn->query("SELECT id FROM `cart_list` where  customer_id = '{$this->settings->userdata('id')}' and product_id = '{$product_id}'")->num_rows;
		if($check > 0 ){
			$resp['status'] = 'failed';
			$resp['msg'] = "Product already exist in your cart.";
		}else{
			$insert = $this->conn->query("INSERT INTO `cart_list` (`customer_id`, `product_id`, `quantity`) VALUES ('{$this->settings->userdata('id')}', '{$product_id}', '1') ");
			if($insert){
				$resp['status'] = 'success';
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
			}
		}


		if($resp['status'] == 'success'){
			$this->settings->set_flashdata('success', 'Product has been added to cart.');
		}
		return json_encode($resp);
	}
	function update_cart(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `cart_list` set quantity = '{$qty}' where id = '{$cart_id}'");
		if($update){
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] == 'success'){
			$this->settings->set_flashdata('success', 'Cart Item has been updated.');
		}
		return json_encode($resp);
	}
	function delete_cart(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `cart_list` where id = '{$id}'");
		if($delete){
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] == 'success'){
			$this->settings->set_flashdata('success', 'Cart Item has been deleted.');
		}
		return json_encode($resp);
	}
	function place_order(){
		extract($_POST);
		$_POST['delivery_address'] = addslashes(htmlspecialchars($_POST['delivery_address']));
		$customer_id = $this->settings->userdata('id');
		$pref = date("Ymd");
		$code = sprintf("%'.05d", 1);
		while(true){
			$check = $this->conn->query("SELECT id FROM `order_list` where `code` = '{$pref}{$code}'")->num_rows;
			if($check > 0){
				$code = sprintf("%'.05d",abs($code) + 1);
			}else{
				$code = $pref.$code;
				break;
			}
		}
		$insert = $this->conn->query("INSERT INTO `order_list` (`code`, `customer_id`, `delivery_address`, `total_amount`) VALUES ('{$code}', '{$customer_id}', '{$delivery_address}', '{$total_amount}') ");
		if($insert){
			$oid = $this->conn->insert_id;
			$data = "";
			$cart = $this->conn->query("SELECT c.*, p.name as product, p.brand as brand, p.price, cc.name as category, p.image_path, COALESCE((SELECT SUM(quantity) FROM `stock_list` where product_id = p.id ), 0) as `available` FROM `cart_list` c inner join product_list p on c.product_id = p.id inner join category_list cc on p.category_id = cc.id where customer_id = '{$customer_id}' ");
			while($row = $cart->fetch_assoc()):
				if(!empty($data)) $data .= ", ";
				$data .= "('{$oid}', '{$row['product_id']}', '{$row['quantity']}', '{$row['price']}')";
			endwhile;
			if(!empty($data)){
				$sql = "INSERT INTO order_items (`order_id`, `product_id`, `quantity`, `price`) VALUES {$data}";
				$save = $this->conn->query($sql);
				if($save){
					$resp['status'] = 'success';
					$this->conn->query("DELETE FROM `cart_list` where customer_id = '{$customer_id}'");
				}else{
					$resp['status'] = 'failed';
					$resp['error'] = $this->conn->error;
					$this->conn->query("DELETE FROM `order_list` where id = '{$oid}'");
				}
			}else{
				$resp['status'] = 'success';
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}

		if($resp['status'] == 'success'){
			$this->settings->set_flashdata('success', 'Order has been placed successfully.');
		}
		return json_encode($resp);
	}
	function update_order_status(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `order_list` set `status` = '{$status}' where id = '{$id}'");
		if($update){
			$resp['status'] = 'success';
		}else{
			$resp['failed'] = 'failed';
			$resp['msg'] = $this->conn->error;
		}
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success', "Order Status has been updated successfully.");

		return json_encode($resp);
	}
	function delete_order(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `order_list` where id = '{$id}'");
		if($delete){
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] == 'success'){
			$this->settings->set_flashdata('success', 'Order has been deleted successfully.');
		}
		return json_encode($resp);
	}
	function save_inquiry(){
		$_POST['message'] = addslashes(htmlspecialchars($_POST['message']));
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `inquiry_list` set {$data} ";
		}else{
			$sql = "UPDATE `inquiry_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$cid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success'," Your Inquiry has been sent successfully. Thank you!");
			else
				$this->settings->set_flashdata('success'," Inquiry successfully updated");
			
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_inquiry(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `inquiry_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Inquiry has been deleted successfully.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'delete_img':
		echo $Master->delete_img();
	break;
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_category':
		echo $Master->delete_category();
	break;
	case 'save_product':
		echo $Master->save_product();
	break;
	case 'delete_product':
		echo $Master->delete_product();
	break;
	case 'save_stock':
		echo $Master->save_stock();
	break;
	case 'delete_stock':
		echo $Master->delete_stock();
	break;
	case 'add_to_card':
		echo $Master->add_to_card();
	break;
	case 'update_cart':
		echo $Master->update_cart();
	break;
	case 'delete_cart':
		echo $Master->delete_cart();
	break;
	case 'place_order':
		echo $Master->place_order();
	break;
	case 'delete_order':
		echo $Master->delete_order();
	break;
	case 'update_order_status':
		echo $Master->update_order_status();
	break;
	case 'save_inquiry':
		echo $Master->save_inquiry();
	break;
	case 'delete_inquiry':
		echo $Master->delete_inquiry();
	break;
	default:
		// echo $sysset->index();
		break;
}