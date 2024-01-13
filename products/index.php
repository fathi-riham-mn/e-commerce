<style>
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
<?php 
$page_title = "Our Available Products";
$page_description = "";
if(isset($_GET['cid']) && is_numeric($_GET['cid'])){
    $category_qry = $conn->query("SELECT * FROM `category_list` where `id` = '{$_GET['cid']}' and `status` = 1 and `delete_flag` = 0");
    if($category_qry->num_rows > 0){
        $cat_result = $category_qry->fetch_assoc();
        $page_title = $cat_result['name'];
        $page_description = $cat_result['description'];
    }
}

?>
<section class="py-3">
	<div class="container">
		<div class="content bg-gradient-dark py-5 px-3">
			<h4 class=""><?= $page_title ?></h4>
            <?php 
                if(isset($_GET['search'])){
                    echo "<h4 class='text-center'><b>Search Result for '".$_GET['search']."'</b></h4>";
                }
            ?>
            <?php if(!empty($page_description)): ?>
                <hr>
                <p class="m-0"><small><em><?= html_entity_decode($page_description) ?></em></small></p>
            <?php endif; ?>
		</div>
		<div class="row mt-n3 justify-content-center">
            <div class="col-lg-10 col-md-11 col-sm-11 col-sm-11">
                <div class="card card-outline rounded-0">
                    <div class="card-body">
                        <div class="row row-cols-xl-4 row-md-6 col-sm-12 col-xs-12 gy-2 gx-2">
                            <?php 
                            $cat_where = "";
                            if(isset($cat_result['id'])){
                                $cat_where = " and `category_id` = '{$cat_result['id']}' "; 
                            }
                            elseif(isset($_GET['search'])){
                            $cat_where = " and (name LIKE '%{$_GET['search']}%' or description LIKE '%{$_GET['search']}%')";}
                            elseif(isset($_GET['c']) && isset($_GET['s'])){
                            $cat_where = " and (md5(category_id) = '{$_GET['c']}'";}
                            elseif(isset($_GET['c'])){
                            $cat_where = " and md5(category_id) = '{$_GET['c']}' ";}

                                $qry = $conn->query("SELECT *, (COALESCE((SELECT SUM(quantity) FROM `stock_list` where product_id = product_list.id ), 0) - COALESCE((SELECT SUM(quantity) FROM `order_items` where product_id = product_list.id), 0)) as `available` FROM `product_list` where (COALESCE((SELECT SUM(quantity) FROM `stock_list` where product_id = product_list.id ), 0) - COALESCE((SELECT SUM(quantity) FROM `order_items` where product_id = product_list.id), 0)) > 0 {$cat_where} order by RAND()");
                                while($row = $qry->fetch_assoc()):
                            ?>
                            <div class="col">
                                <a class="card rounded-0 shadow product-item text-decoration-none text-reset h-100" href="./?p=products/view_product&id=<?= $row['id'] ?>">
                                    <div class="position-relative">
                                        <div class="img-top position-relative product-img-holder">
                                            <img src="<?= validate_image($row['image_path']) ?>" alt="" class="product-img">
                                        </div>
                                        <div class="position-absolute bottom-1 right-1" style="bottom:.5em;right:.5em">
                                            <span class="badge badge-light bg-gradient-light border text-dark px-4 rounded-pill"><?= format_num($row['price'], 2) ?></span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div style="line-height:1em">
                                            <div class="card-title w-100 mb-0"><?= $row['name'] ?></div>
                                            <div class="d-flex justify-content-between w-100 mb-3">
                                                <div class=""><small class="text-muted"><?= $row['brand'] ?></small></div>
                                                <div class=""><small class="text-muted">Stock: <?= format_num($row['available'],0) ?></small></div>
                                            </div>
                                            <div class="card-description truncate text-muted"><?= html_entity_decode($row['description']) ?></div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
</section>