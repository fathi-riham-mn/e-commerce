
<style>
    .carousel-item>img{
        object-fit:cover !important;
    }
    #carouselExampleControls .carousel-inner{
        height:35em !important;
    }
    .product-img-holder{
        width:100%;
        height:15em;
        overflow:hidden;
    }
    .product-img{
        width:100%;
        height:100%;
        object-fit: cover;
        object-position: center center;
        transition: all .3s ease-in-out;
    }
    .product-item:hover .product-img{
        transform: scale(1.2)
    }
</style>
<section class="py-3">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
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
        </div>
        <div class="row justify-content-center mt-n3">
            <div class="col-lg-10 col-md-11 col-sm-11 col-sm-11">
                <div class="card card-outline rounded-0">
                    <div class="card-body">
                        <div class="row row-cols-xl-4 row-md-6 col-sm-12 col-xs-12 gy-2 gx-2">
                            <?php 
                                $qry = $conn->query("SELECT *, (COALESCE((SELECT SUM(quantity) FROM `stock_list` where product_id = product_list.id), 0) - COALESCE((SELECT SUM(quantity) FROM `order_items` where product_id = product_list.id), 0)) as `available` FROM `product_list` where (COALESCE((SELECT SUM(quantity) FROM `stock_list` where product_id = product_list.id), 0) - COALESCE((SELECT SUM(quantity) FROM `order_items` where product_id = product_list.id), 0)) > 0 order by RAND() limit 4");
                                while($row = $qry->fetch_assoc()):
                            ?>
                            <div class="col">
                                <a class="card rounded-0 shadow product-item text-decoration-none text-reset" href="./?p=products/view_product&id=<?= $row['id'] ?>">
                                    <div class="position-relative">
                                        <div class="img-top position-relative product-img-holder">
                                            <img src="<?= validate_image($row['image_path']) ?>" alt="" class="product-img">
                                        </div>
                                        <div class="position-absolute bottom-1 right-1" style="bottom:.5em;right:.5em">
                                            <span class="badge badge-dark border text-light px-4 rounded-pill"><?= format_num($row['price'], 2) ?></span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div style="line-height:1em">
                                            <div class="card-title w-100 mb-0"><?= $row['name'] ?></div>
                                            <div class="card-description w-100"><small class="text-muted"><?= $row['brand'] ?></small></div>
                                            <div class="card-description w-100"><small class="text-muted">Stock: <?= format_num($row['available'],0) ?></small></div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <div class="text-center py-1">
                            <a href="./?p=products" class="btn btn-lg btn-deafault text-light bg-gradient-dark col-lg-4 col-md-6 col-sm-12 col-xs-12 rounded-pill">Explore More Product</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>