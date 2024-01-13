<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `order_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<style>
    #order-logo{
        max-width:100%;
        max-height: 20em;
        object-fit:scale-down;
        object-position:center center;
    }
</style>
<div class="content py-5 px-3 bg-gradient-dark">
	<h2><b><?= isset($code) ? $code : '' ?> Order Details</b></h2>
</div>
<div class="row flex-column mt-lg-n4 mt-md-n4 justify-content-center align-items-center">
	<div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
		<div class="card rounded-0">
            <div class="card-header py-1">
                <div class="card-tools">
                    <?php if(isset($status) && $status < 4): ?>
                        <button class="btn btn-info btn-sm bg-gradient-info rounded-0" type="button" id="update_status">Update Status</button>
                    <?php endif; ?>
                    <button class="btn btn-navy btn-sm bg-gradient-navy rounded-0" type="button" id="print"><i class="fa fa-print"></i> Print</button>
                    <button class="btn btn-danger btn-sm bg-gradient-danger rounded-0" type="button" id="delete_data"><i class="fa fa-trash"></i> Delete</button>
                    <a class="btn btn-light btn-sm bg-gradient-light border rounded-0" href="./?page=orders"><i class="fa fa-angle-left"></i> Back to List</a>
                </div>
            </div>
        </div>
    </div>
	<div class="col-lg-8 col-md-10 col-sm-12 col-xs-12 printout">
		<div class="card rounded-0">
			<div class="card-body">
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
                                            echo '<span class="badge badge-primary bg-gradient-dark px-3 rounded-pill">Packed</span>';
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
                        $order_items = $conn->query("SELECT o.*, p.name as product, p.brand as brand, p.price, cc.name as category, p.image_path, COALESCE((SELECT SUM(quantity) FROM `stock_list` where product_id = p.id  ), 0) as `available` FROM `order_items` o inner join product_list p on o.product_id = p.id inner join category_list cc on p.category_id = cc.id where order_id = '{$id}' ");
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
            </div>
		</div>
	</div>
</div>
<noscript id="print-header">
    <div>
        <div class="d-flex w-100 align-items-center">
            <div class="col-2 text-center">
                <img src="<?= validate_image($_settings->info('logo')) ?>" alt="" class="rounded-circle border" style="width: 5em;height: 5em;object-fit:cover;object-position:center center">
            </div>
            <div class="col-8">
                <div style="line-height:1em">
                    <div class="text-center font-weight-bold"><large><?= $_settings->info('name') ?></large></div>
                    <div class="text-center font-weight-bold"><large>order Details</large></div>
                </div>
            </div>
        </div>
       
        <hr>
    </div>
</noscript>
<script>
    function print_t(){
        var h = $('head').clone()
        var el = ""
        $('.printout').map(function(){
            var p = $(this).clone()
                p.find('.btn').remove()
                p.find('.card').addClass('border')
                p.removeClass('col-lg-8 col-md-10 col-sm-12 col-xs-12')
                p.addClass('col-12')
            el += p[0].outerHTML
        })
        var ph = $($('noscript#print-header').html()).clone()
        h.find('title').text("order Details - Print View")
        var nw = window.open("", "_blank", "width="+($(window).width() * .8)+",left="+($(window).width() * .1)+",height="+($(window).height() * .8)+",top="+($(window).height() * .1))
            nw.document.querySelector('head').innerHTML = h.html()
            nw.document.querySelector('body').innerHTML = ph[0].outerHTML
            nw.document.querySelector('body').innerHTML += el
            nw.document.close()
            start_loader()
            setTimeout(() => {
                nw.print()
                setTimeout(() => {
                    nw.close()
                    end_loader()
                }, 200);
            }, 300);
    }
    $(function(){
        $('#print').click(function(){
            print_t()
        })
        $('#assign_team').click(function(){
            uni_modal("Assign a Team", 'orders/assign_team.php?id=<?= isset($id) ? $id : '' ?>')
        })
		$('#delete_data').click(function(){
			_conf("Are you sure to delete this order permanently?","delete_order", ["<?= isset($id) ? $id :'' ?>"])
		})
        $('#update_status').click(function(){
            uni_modal("Update Status", "orders/update_status.php?id=<?= isset($id) ? $id : '' ?>")
        })
    })
    function delete_order($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_order",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.replace("./?page=orders");
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>
