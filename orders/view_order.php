<?php 
require_once('./../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT *  from `order_list` where id = '{$_GET['id']}' and customer_id = '{$_settings->userdata('id')}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }else{
		echo "<script>alert('You dont have access for this page'); location.replace('./');</script>";
	}
}else{
	echo "<script>alert('You dont have access for this page'); location.replace('./');</script>";
}
?>
<style>
    #uni_modal .modal-footer{
        display:none !important;
    }
</style>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="mb-3">
                <label for="" class="control-label">Order Reference Code:</label>
                <div class="pl-4"><?= isset($code) ? $code : '' ?></div>
            </div>
            <div class="mb-3">
                <label for="" class="control-label">Delivery Address:</label>
                <div class="pl-4"><?= isset($delivery_address) ? str_replace(["\r\n", "\r", "\n"], "<br>",$delivery_address) : '' ?></div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
        <div class="mb-3">
                <label for="" class="control-label">Status:</label>
                <div class="pl-4">
                <?php 
                $status = isset($status) ? $status : '';
                    switch($status){
                        case 0:
                            echo '<span class="badge badge-secondary bg-gradient-secondary px-3 rounded-pill">Pending</span>';
                            break;
                        case 1:
                            echo '<span class="badge badge-primary bg-gradient-primary px-3 rounded-pill">Packed</span>';
                            break;
                        case 2:
                            echo '<span class="badge badge-warning bg-gradient-warning px-3 rounded-pill">Out for Delivery</span>';
                            break;
                        case 3:
                            echo '<span class="badge badge-teal bg-gradient-teal px-3 rounded-pill">Paid</span>';
                            break;
                        default:
                            echo '<span class="badge badge-light bg-gradient-light border dark px-3 rounded-pill">N/A</span>';
                            break;
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
    <div id="item_list" class="list-group">
        <?php 
        $gt = 0;
        $order_items = $conn->query("SELECT o.*, p.name as product, p.brand as brand, p.price, cc.name as category, p.image_path, COALESCE((SELECT SUM(quantity) FROM `stock_list` where product_id = p.id ), 0) as `available` FROM `order_items` o inner join product_list p on o.product_id = p.id inner join category_list cc on p.category_id = cc.id where order_id = '{$id}' ");
        while($row = $order_items->fetch_assoc()):
            $gt += $row['price'] * $row['quantity'];
        ?>
        <div class="list-group-item cart-item" data-id = '<?= $row['id'] ?>'  data-max = '<?= format_num($row['available'], 0) ?>'>
            <div class="d-flex w-100 align-items-center">
                <div class="col-2 text-center">
                    <img src="<?= validate_image($row['image_path']) ?>" alt="" class="img-thumbnail border p-0 product-logo">
                </div>
                <div class="col-auto flex-shrink-1 flex-grow-1">
                    <div style="line-heigth:1em">
                        <h4 class='mb-0'><?= $row['product'] ?></h4>
                        <div class="text-muted"><?= $row['brand'] ?></div>
                        <div class="text-muted"><?= $row['category'] ?></div>
                        <div class="text-muted d-flex w-100">
                            <?= format_num($row['quantity'],0) ?> x <?= format_num($row['price'],2) ?>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <h4><b><?= format_num($row['price'] * $row['quantity'], 2) ?></b></h4>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php if($order_items->num_rows <= 0): ?>
        <h5 class="text-center text-muted">Order Items is empty.</h5>
    <?php endif; ?>
    <div class="d-flex justify-content-end py-3">
        <div class="col-auto">
            <h3><b>Grand Total: <?= format_num($gt,2) ?></b></h3>
        </div>
    </div>
</div>
<hr class="px-n5">
<div class="py-1 text-right">
    <button class="btn btn-sm btn-light bg-gradient-light border btn-flat" type="button" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
</div>