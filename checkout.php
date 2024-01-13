<?php 
if($_settings->userdata('id') == '' || $_settings->userdata('login_type') != 2){
	echo "<script>alert('You dont have access for this page'); location.replace('./');</script>";
}
?>
<script src="https://www.paypalobjects.com/api/checkout.js"></script>
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
                        <?php 
                            $cart_total = $conn->query("SELECT SUM(c.quantity * p.price) FROM `cart_list` c inner join product_list p on c.product_id = p.id inner join category_list cc on p.category_id = cc.id where customer_id = '{$_settings->userdata('id')}' ")->fetch_array()[0];
                            $cart_total = $cart_total > 0 ? $cart_total :0;
                        ?>
                            <form action="" id="order-form">
                                <input type="hidden" name="total_amount" value="<?= $cart_total ?>">
                                <h3><b>Total: <?= format_num($cart_total,2) ?></b></h3>
                                <div class="form-group">
                                    <label for="delivery_address" class="control-label">Delivery Address</label>
                                    <textarea name="delivery_address" id="delivery_address" cols="30" rows="4" class="form-control form-control-sm rounded-0" required></textarea>
                                </div>
                                <div class="col my-3">
                                    <h4 class="text-muted">Payment Method</h4>
                                <div class="d-flex w-50 justify-content-between ">
                                    <button class="btn btn-lg btn-deafault text-dark bg-gradient-light rounded-pill border-dark">Cash on Delivery</button>
                                    <span class="btn btn-lg btn-deafault text-dark bg-gradient-light rounded-pill border-dark" id="paypal-button"></span>
                                </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    paypal.Button.render({
    env: 'sandbox', // change for production if app is live,
 
        //app's client id's
	client: {
        sandbox:    'AdDNu0ZwC3bqzdjiiQlmQ4BRJsOarwyMVD_L4YQPrQm4ASuBg4bV5ZoH-uveg8K_l9JLCmipuiKt4fxn',
        //production: 'AaBHKJFEej4V6yaArjzSx9cuf-UYesQYKqynQVCdBlKuZKawDDzFyuQdidPOBSGEhWaNQnnvfzuFB9SM'
    },
 
    commit: true, // Show a 'Pay Now' button
 
    style: {
    	color: 'blue',
    	size: 'small'
    },
 
    payment: function(data, actions) {
        return actions.payment.create({
            payment: {
                transactions: [
                    {
                    	//total purchase
                        amount: { 
                        	total: '<?php echo $cart_total; ?>', 
                        	currency: 'PHP' 
                        }
                    }
                ]
            }
        });
    },
 
    onAuthorize: function(data, actions) {
        return actions.payment.execute().then(function(payment) {
    		// //sweetalert for successful transaction
    		// swal('Thank you!', 'Paypal purchase successful.', 'success');
            payment_online()
        });
    },
 
}, '#paypal-button');

function payment_online(){
    $('[name="payment_method"]').val("Online Payment")
    $('[name="paid"]').val(1)
    $('#place_order').submit()
}
    $(function(){
        $('#order-form').submit(function(e){
            e.preventDefault()
            start_loader()
            $.ajax({
                url:_base_url_+'classes/Master.php?f=place_order',
                method:'POST',
                data: $(this).serialize(),
                dataType:'json',
                error:err=>{
                    console.log(err)
                    alert_toast("An error occurred.",'error')
                    end_loader()
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.replace('./')
                    }else{
                        alert_toast("An error occurred.",'error')
                    }
                    end_loader()
                }
            })
        })
    })
</script>
