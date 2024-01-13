<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `product_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<style>
	#cimg{
		max-width:100%;
		max-height:25em;
		object-fit:scale-down;
		object-position:center center;
	}
</style>
<div class="content py-5 px-3 bg-gradient-dark">
	<h2><b><?= isset($id) ? "Update Product Details" : "New Product Entry" ?></b></h2>
</div>
<div class="row mt-lg-n4 mt-md-n4 justify-content-center">
	<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
		<div class="card rounded-0">
			<div class="card-body">

				<div class="container-fluid">
					<form action="" id="product-form">
						<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="brand" class="control-label">Brand</label>
							<input type="text" name="brand" id="brand" class="form-control form-control-sm rounded-0" value="<?php echo isset($brand) ? $brand : ''; ?>"  required/>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="name" class="control-label">Name</label>
							<input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" value="<?php echo isset($name) ? $name : ''; ?>"  required/>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="category_id" class="control-label">Category</label>
							<select name="category_id" id="category_id" class="form-control form-control-sm rounded-0" required="required">
								<option value="" disabled <?= !isset($category_id) ? 'selected' : '' ?>></option>
								<?php 
								$categories = $conn->query("SELECT * FROM category_list where delete_flag = 0 and `status` = 1 ".(isset($category_id) && $category_id > 0 ? " or id = '{$category_id}' " : '')." order by `name` asc");
								while($row = $categories->fetch_assoc()):
								?>
								<option value="<?= $row['id'] ?>" <?= isset($category_id) && $row['id'] == $category_id ? 'selected' : '' ?>><?= $row['name'] ?></option>
								<?php endwhile; ?>
							</select>
						</div>
						<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<label for="description" class="control-label">Description</label>
							<textarea rows="3" name="description" id="description" class="form-control form-control-sm rounded-0" required><?php echo isset($description) ? $description : ''; ?></textarea>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="price" class="control-label">Price</label>
							<input type="number" step="any" name="price" id="price" class="form-control form-control-sm rounded-0 text-right" value="<?php echo isset($price) ? $price : ''; ?>"  required/>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="status" class="control-label">Status</label>
							<select name="status" id="status" class="form-control form-control-sm rounded-0" required="required">
								<option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
								<option value="0" <?= isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
							</select>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="status" class="control-label">Thumbnail</label>
							<div class="custom-file custom-file-sm">
								<input type="file" class="custom-file-input rounded-0" id="customFile1" name="img" onchange="displayImg(this)">
								<label class="custom-file-label" for="customFile1">Choose file</label>
							</div>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<img src="<?php echo validate_image(isset($image_path) ? $image_path : '') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
						</div>
					</form>
				</div>
			</div>
			<div class="card-footer py-1 text-center">
				<button class="btn btn-dark btn-sm bg-gradient-dark btn-flat" form="product-form"><i class="fa fa-save"></i> Save</button>
				<a class="btn btn-light btn-sm bg-gradient-light border btn-flat" href="./?page=products"><i class="fa fa-times"></i> Cancel</a>
			</div>
		</div>
	</div>
</div>
<script>
	function displayImg(input) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        	$(input).siblings('.custom-file-label').html(input.files[0].name)
	        }

	        reader.readAsDataURL(input.files[0]);
	    }else{
			$('#cimg').attr('src', "<?php echo validate_image(isset($image_path) ? $image_path : '') ?>");
			$(input).siblings('.custom-file-label').html('Choose file')
		}
	}
	$(document).ready(function(){
		$('#category_id').select2({
			placeholder:"Please Select Category Here",
			width:'100%',
			containerCssClass:'form-control form-control-sm rounded-0'
		})
		$('#product-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_product",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.replace('./?page=products/view_product&id='+resp.pid)
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-dark err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").scrollTop(0);
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})

	})
</script>