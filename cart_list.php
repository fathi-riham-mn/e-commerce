<?php 
if($_settings->userdata('id') == '' || $_settings->userdata('login_type') != 2){
	echo "<script>alert('You dont have access for this page'); location.replace('./');</script>";
}
?>
<style>
    .product-logo{
        width:7em;
        height:7em;
        object-fit:cover;
        object-position:center center
    }
</style>
<section class="py-3">
    <div class="container">
        <div class="content px-3 py-5 bg-gradient-dark">
            <h3 class=""><b>Cart List</b></h3>
        </div>
        <div class="row mt-n4  justify-content-center align-items-center flex-column">
            <div class="col-lg-10 col-md-11 col-sm-12 col-xs-12">
                <div class="card rounded-0 shadow">
                    <div class="card-body">
                        <div class="container-fluid">
                            <div id="item_list" class="list-group">
                                <?php 
                                $gt = 0;
                                $cart = $conn->query("SELECT c.*, p.name as product, p.brand as brand, p.price, cc.name as category, p.image_path, (COALESCE((SELECT SUM(quantity) FROM `stock_list` where product_id = p.id ), 0) - COALESCE((SELECT SUM(quantity) FROM `order_items` where product_id = p.id), 0)) as `available` FROM `cart_list` c inner join product_list p on c.product_id = p.id inner join category_list cc on p.category_id = cc.id where customer_id = '{$_settings->userdata('id')}' ");
                                while($row = $cart->fetch_assoc()):
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
                                                    <div class="input-group input-group-sm rounded-0" style="width:10em!important">
                                                        <button class="btn btn-xs btn-flat btn-primary bg-gradient-primary minus-qty" type="button"><i class="fa fa-minus"></i></button>
                                                        <input type="text" value="<?= $row['quantity'] ?>" class="form-control form-control-sm rounded-0 qty text-center" readonly="readonly">
                                                        <button class="btn btn-xs btn-flat btn-primary bg-gradient-primary add-qty" type="button"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                    <div class="col-auto">
                                                        <button class="btn btn-sm btn-flat btn-danger del-item" type="button"><i class="fa fa-trash"></i></button>
                                                    </div>
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
                            <?php if($cart->num_rows <= 0): ?>
                                <h5 class="text-center text-muted">Cart has is empty.</h5>
                            <?php endif; ?>
                            <div class="d-flex justify-content-end py-3">
                                <div class="col-auto">
                                    <h3><b>Grand Total: <?= format_num($gt,2) ?></b></h3>
                                </div>
                            </div>
                            <?php if($gt > 0): ?>
                            <div class="py-1 text-center">
                                <a href="./?p=checkout" class="btn btn-lg btn-deafault text-light bg-gradient-dark col-lg-4 col-md-6 col-sm-12 col-xs-12 rounded-pill">Checkout</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function update_item(cart_id = '', qty = 0){
        start_loader()
        $.ajax({
            url:_base_url_+'classes/Master.php?f=update_cart',
            method:'POST',
            data: {cart_id : cart_id, qty :qty},
            dataType:'json',
            error:err=>{
                console.log(err)
                alert_toast("An error occurred.",'error')
                end_loader()
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert_toast("An error occurred.",'error')
                }
                end_loader()
            }
        })
    }
    $(function(){
        $('.add-qty').click(function(){
            var item = $(this).closest('.cart-item')
            var qty = parseFloat(item.find('.qty').val())
            var id = item.attr('data-id')
            var max = item.attr('data-max')
            if(qty == max)
            qty = max;
            else
            qty += 1;
            item.find('.qty').val(qty)
            update_item(id, qty)
        })
        $('.minus-qty').click(function(){
            var item = $(this).closest('.cart-item')
            var qty = parseFloat(item.find('.qty').val())
            var id = item.attr('data-id')
            if(qty == 1)
            qty = 1;
            else
            qty -= 1;
            item.find('.qty').val(qty)
            update_item(id, qty)
        })
        $('.del-item').click(function(){
            var item = $(this).closest('.cart-item')
            var id = item.attr('data-id')
            _conf("Are you sure to remove this item from your cart?", "delete_cart", [id])
        })
    })
    function delete_cart($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_cart",
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
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>