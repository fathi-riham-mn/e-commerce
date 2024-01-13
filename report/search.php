<style>
    #uni_modal .modal-footer{
        display:none !important;
    }
</style>
<div class="container-fluid">
    <form action="" id="search-report">
        <div class="form-group">
            <label for="search" class="control-label">Search by  (Request Code, Name, or Contact #)</label>
            <input type="text" class="form-control form-control-sm rounded-0" name="search" id="search">
        </div>
    </form>
</div>
<hr class="mx-n3">
<div class="px-2 text-center">
    <button class="btn btn-flat btn-sm btn-primary bg-gradient-primary" form="search-report"><i class="fa fa-search"></i> Search</button>
    <button class="btn btn-flat btn-sm btn-light bg-gradient-light border" type="button" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
</div>
<script>
    $(function(){
        $('#search-report').submit(function(e){
            e.preventDefault()
            location.href = "./?p=report/list&"+$(this).serialize();
        })
    })
</script>