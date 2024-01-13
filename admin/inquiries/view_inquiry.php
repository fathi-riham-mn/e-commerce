<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
    $conn->query("UPDATE `inquiry_list` set `status` = 1 where id = '{$_GET['id']}'");
    $qry = $conn->query("SELECT *  from `inquiry_list` where id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_array() as $k => $v){
            if(!is_numeric($k))
                $$k = $v;
        }
    }
}
?>
<style>
    .course_logo{
        width:100%;
        height:100%;
        object-fit:cover;
        object-position:center center;
    }
</style>
<div class="content bg-gradient-dark py-5 px-4">
    <h3 class="font-weight-bolder">Inquiry Details</h3>
</div>
<div class="row mt-n5 justify-content-center">
    <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
        <div class="card card-outline card-primary rounded-0 shadow">
            <div class="card-body">
                <div class="container-fluid">
                    <fieldset>
                        <div class="row mb-3">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label for="" class="control-label text-muted">Inquirer Fullname</label>
                                <div class="pl-4 font-weight-bolder"><?= isset($fullname) ? $fullname : '' ?></div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label for="" class="control-label text-muted">Email</label>
                                <div class="pl-4 font-weight-bolder"><?= isset($email) && !empty($email) ? $email : 'N/A' ?></div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <label for="" class="control-label text-muted">Contact #</label>
                                <div class="pl-4 font-weight-bolder"><?= isset($contact) ? $contact : '' ?></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label for="" class="control-label text-muted">Message</label>
                                <div class="pl-4"><?= isset($message) ? (str_replace(["\n\r", "\n", "\r"], '<br>', $message)) : '' ?></div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="card-footer py-1 text-center">
                <button id="delete_inquiry" class="btn btn-danger btn-flat bg-gradient-dark btn-sm" type="button"><i class="fa fa-trash"></i> Delete</button>
                <a class="btn btn-light btn-flat bg-gradient-light border btn-sm" href="./?page=inquiries"><i class="fa fa-angle-left"></i> Back to List</a>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#delete_inquiry').click(function(){
			_conf("Are you sure to delete this Inquiry permanently?","delete_inquiry",['<?= isset($id) ? $id : '' ?>'])
		})
    })
    function delete_inquiry($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_inquiry",
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
					location.replace("./?page=inquiries");
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>