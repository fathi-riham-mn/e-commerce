<style>
    .carousel-item>img{
        object-fit:cover !important;
    }
    #carouselExampleControls .carousel-inner{
        height:35em !important;
    }
</style>
<div class="container">
    <div class="content">
        <div id="carouselExampleControls" class="carousel slide bg-dark" data-ride="carousel">
            <div class="carousel-inner">
                <?php 
                    $upload_path = "uploads/banner";
                    if(is_dir(base_app.$upload_path)): 
                    $file= scandir(base_app.$upload_path);
                    $_i = 0;
                        foreach($file as $img):
                            if(in_array($img,array('.','..')))
                                continue;
                    $_i++;
                        
                ?>
                <div class="carousel-item h-100 <?php echo $_i == 1 ? "active" : '' ?>">
                    <img src="<?php echo validate_image($upload_path.'/'.$img) ?>" class="d-block w-100  h-100" alt="<?php echo $img ?>">
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-target="#carouselExampleControls" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-target="#carouselExampleControls" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
    <div class="row mt-lg-n4 mt-md-n4 justify-content-center">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="card rounded-0">
                <div class="card-body">
                    <h3 class="text-center"><b>Contact Us</b></h3>
                    <center><hr style="height:2px;width:5em;opacity:1" class="bg-dark"></center>
                    <dl>
                        <dt class="text-muted">Email</dt>
                        <dd class="pl-3"><?= $_settings->info('email') ?></dd>
                        <dt class="text-muted">Telephone #</dt>
                        <dd class="pl-3"><?= $_settings->info('phone') ?></dd>
                        <dt class="text-muted">Mobile #</dt>
                        <dd class="pl-3"><?= $_settings->info('mobile') ?></dd>
                        <dt class="text-muted">Address</dt>
                        <dd class="pl-3"><?= $_settings->info('address') ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
    })
</script>
