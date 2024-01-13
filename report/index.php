<section class="py-3">
    <div class="container">
        <div class="content py-5 px-3 bg-gradient-dark">
            <h2>Fire Reporting</h2>
        </div>
        <div class="row justify-content-center mt-n3">
            <div class="col-lg-8 col-md-10 col-sm-12 col-sm-12">
                <div class="card card-outline rounded-0">
                    <div class="card-body">
                        <div class="container-fluid">
                            <?php if($_settings->chk_flashdata('request_sent')): ?>
                                <div class="alert alert-success bg-gradient-teal rounded-0">
                                    <div><?= $_settings->flashdata('request_sent') ?></div>
                                </div>
                            <?php endif;?>
                           <form action="" id="request-form">
                               <input type="hidden" name="id">
                               <div class="form-group col-lg-6 col-md-8 col-sm-12 col-xs-12">
                                   <label for="fullname" class="control-label">Fullname <small class="text-dark">*</small></label>
                                   <input type="text" class="form-control form-control-sm rounded-0" name="fullname" id="fullname" required="required">
                               </div>
                               <div class="form-group col-lg-6 col-md-8 col-sm-12 col-xs-12">
                                   <label for="contact" class="control-label">Contac # <small class="text-dark">*</small></label>
                                   <input type="text" class="form-control form-control-sm rounded-0" name="contact" id="contact" required="required">
                               </div>
                               <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                   <label for="message" class="control-label">Message<small class="text-dark">*</small></label>
                                   <textarea rows="3" class="form-control form-control-sm rounded-0" name="message" id="message" required="required"></textarea>
                               </div>
                               <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                   <label for="location" class="control-label">Location <small class="text-dark">*</small></label>
                                   <textarea rows="3" class="form-control form-control-sm rounded-0" name="location" id="location" required="required"></textarea>
                               </div>
                           </form>
                        </div>
                    </div>
                    <div class="card-footer py-1 text-center">
                        <button class="btn btn-flat btn-sm btn-primary bg-gradient-primary" form="request-form"><i class="fa fa-paper-plane"></i> Submit</button>
                        <button class="btn btn-flat btn-sm btn-light bg-gradient-light border" type="reset" form="request-form"><i class="fa fa-times"></i> Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(function(){
        $('#request-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_request",
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
						location.reload()
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