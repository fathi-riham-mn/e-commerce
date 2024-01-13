<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT p.*, c.name as `category`,(COALESCE((SELECT SUM(quantity) FROM `stock_list` where product_id = p.id ), 0)  - COALESCE((SELECT SUM(quantity) FROM `order_items` where product_id = p.id), 0)) as `available` from `product_list` p inner join `category_list` c on p.category_id = c.id where p.id = '{$_GET['id']}' and p.delete_flag = 0 ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<style>
	#product-img{
		max-width:100%;
		max-height:35em;
		object-fit:scale-down;
		object-position:center center;
	}
</style>
<div class="content py-5 px-3 bg-gradient-dark">
	<h2><b>Product Details</b></h2>
</div>
<div class="row flex-column mt-lg-n4 mt-md-n4 justify-content-center align-items-center">
	<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
		<div class="card rounded-0">
			<div class="card-header py-1 text-center">
				<div class="card-tools">
					<button class="btn btn-dark btn-sm bg-gradient-dark rounded-0" type="button" id="new_entry"><i class="fa fa-plus-square"></i> New Stock</button>
					<a class="btn btn-light btn-sm bg-gradient-light border rounded-0" href="./?page=inventory"><i class="fa fa-angle-left"></i> Back to List</a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
		<div class="card rounded-0">
			<div class="card-body">
                <div class="container-fluid">
					<div class="row">
						<div class="col-lg-4 col-md-5 col-sm-12 col-xs-12">
							<center>
								<img src="<?= validate_image(isset($image_path) ? $image_path : '') ?>" alt="<?= isset($name) ? $name : '' ?>" class="img-thumbnail p-0 border" id="product-img">
							</center>
						</div>
						<div class="col-lg-8 col-md-7 col-sm-12 col-xs-12">
							<dl>
								<dt class="text-muted">Product</dt>
								<dd class="pl-4">
									<a href="./?page=products/view_product&id=<?= isset($id) ? $id : '' ?>" target="_blank"><?= isset($brand) ? $brand : "" ?> - <?= isset($name) ? $name : "" ?></a>
								</dd>
								<dt class="text-muted">Category</dt>
								<dd class="pl-4"><?= isset($category) ? $category : "" ?></dd>
								<dt class="text-muted">Price</dt>
								<dd class="pl-4"><?= isset($price) ? format_num($price,2) : "" ?></dd>
								<dt class="text-muted h6 mb-0">Available Stock</dt>
								<dd class="pl-4 h5"><?= isset($available) ? format_num($available,0) : "" ?></dd>
							</dl>
						</div>
					</div>
                </div>
            </div>
		</div>
	</div>
	<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
		<div class="card rounded-0">
			<div class="card-header">
				<div class="card-title">Stock History</div>
			</div>
			<div class="card-body">
                <div class="container-fluid">
					<table class="table table-striped table-bordered" id="stock-history">
						<colgroup>
							<col width="20%">
							<col width="35%">
							<col width="35%">
							<col width="10%">
						</colgroup>
						<thead>
							<tr>
								<th class="p-1 text-center">Date Added</th>
								<th class="p-1 text-center">Code</th>
								<th class="p-1 text-center">Qty</th>
								<th class="p-1 text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php if(isset($id)): ?>
							<?php 
							$stocks = $conn->query("SELECT * FROM `stock_list` where product_id = '{$id}' order by abs(unix_timestamp(date_created))");
							while($row = $stocks->fetch_assoc()):
							?>
								<tr class="<?= !empty($row['expiration']) && (strtotime($row['expiration']) <= strtotime(date("Y-m-d"))) ? "text-danger" : "" ?>">
									<td class="p-1 align-middle"><?= date("M d, Y", strtotime($row['date_created'])) ?></td>
									<td class="p-1 align-middle"><?= $row['code'] ?></td>
									<td class="p-1 text-right align-middle"><?= format_num($row['quantity'], 0) ?><?= !empty($row['expiration']) && (strtotime($row['expiration']) <= strtotime(date("Y-m-d"))) ? " (Expired)" : "" ?></td>
									<td class="p-1 text-center align-middle">
										<div class="btn-group">
											<button class="btn btn-flat btn-xs bg-gradient-dark edit_stock" title="Edit Stock" type="button" data-id='<?= $row['id'] ?>'><i class="fa fa-edit text-sm"></i></button>
											<button class="btn btn-flat btn-xs btn-danger bg-gradient-danger delete_stock" title="Delete Stock" type="button" data-code='<?= $row['code'] ?>' data-id='<?= $row['id'] ?>'><i class="fa fa-trash text-sm"></i></button>
										</div>
									</td>
								</tr>
							<?php endwhile; ?>
							<?php endif; ?>
						</tbody>
					</table>
                </div>
            </div>
		</div>
	</div>
</div>
<script>
    $(function(){
		$('#new_entry').click(function(){
			uni_modal('<i class="far fa-plus-square"></i> Add New Stock Entry', 'inventory/manage_stock.php?pid=<?= isset($id) ? $id : '' ?>')
		})
		$('.edit_stock').click(function(){
			uni_modal('<i class="fa fa-edit"></i> Edit Stock Entry Details', 'inventory/manage_stock.php?pid=<?= isset($id) ? $id : '' ?>&id='+$(this).attr('data-id'))
		})
		$('.delete_stock').click(function(){
			_conf("Are you sure to delete this <b>["+$(this).attr('data-code')+"]</b> Stock Entry permanently?","delete_stock", [$(this).attr('data-id')])
		})
		$('#stock-history').dataTable({
			columnDefs: [
					{ orderable: false, targets: [4] }
			],
			order:[0,'asc']
		});
		$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
		
    })
    function delete_stock($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_stock",
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