<?php 
use Applications\Admin\Modules\Products\ProductsController;
use Core\Shivalik\Entities\Product;

/**
 * @var Product $product
 * @var Product[] $products
 */
$product = $_REQUEST[ProductsController::ATT_PRODUCT];
$products = $_REQUEST[ProductsController::ATT_PRODUCTS];

$offset = isset($_GET['offset'])? $_GET['offset'] : 0;
$limit = isset($_GET['limit'])? $_GET['limit'] : 4;

$prev = $offset - 4;
$next = $offset + 4;
$count = intval($_REQUEST[ProductsController::ATT_COUNT_PRODUCT], 10);
?>
<section class="row">
	<div class="col-xs-12">
		<div class="thumbnail">
    		<h2><?php echo htmlspecialchars($product->name); ?></h2>
    		<div class="row">
    			<div class="col-xs-12 col-sm-8 col-md-8">
            		<div class="alert alert-info">
            			<strong class="">Defualt unit price: <?php echo $product->defaultUnitPrice; ?> $</strong>, record date <?php echo $product->dateAjout->format('D, d M Y \a\t H:i:s')?>
            		</div>
            		<div class="text-justify">
            			<p><?php echo htmlspecialchars($product->description); ?></p>
            		</div>
            		
            		<div>
            			<a href="update.html" class="btn btn-primary">Update</a>
            		</div>
    			</div>
    			<div class="col-md-4 col-sm-4 col-xs-12">
    				<img class="thumbnail" src="/<?php echo $product->picture;?>" alt="<?php echo htmlspecialchars($product->name); ?>"/>
    			</div>
    		</div>
		</div>
	</div>
	
	<div class="col-xs-12">
		<h2>Other products</h2>
		<div class="row">
			<?php foreach ($products as $p) : ?>
			<div class="col-sm-4 col-md-3 col-lg-3">
                <div class="thumbnail">
        			<img src="/<?php echo $p->picture;?>" alt="<?php echo htmlspecialchars($p->name); ?>"/>
                    <div class="caption">
                        <h3 class="h3 <?php echo ($p->id == $product->id? 'text-info' : ''); ?>"><?php echo htmlspecialchars($p->name); ?></h3>
                        <p class="text-justify"><?php echo htmlspecialchars($p->getDescription(100)); ?></p>
                        <p>
                        	<a href="/admin/products/<?php echo "{$p->id}/{$limit}-more-skips-{$offset}.html"; ?>" class="btn btn-primary" role="button">See more</a>
                        	<a href="/admin/products/<?php echo $p->id; ?>/update.html" class="btn btn-default" role="button">Update</a>
                        </p>
                    </div>
                </div>
			</div>
			<?php endforeach;?>
		</div>
		<nav aria-label="products pager navigation">
            <ul class="pager">
            	<?php if ($prev>=0) : ?>
                <li class="previous"><a href="/admin/products/<?php echo "{$product->id}/{$limit}-more-skips-{$prev}.html"; ?>">Previous</a></li>
            	<?php endif;?>
            	<?php if ($next < $count) : ?>
                <li class="next"><a href="/admin/products/<?php echo "{$product->id}/{$limit}-more-skips-{$next}.html"; ?>">Next</a></li>
            	<?php endif;?>
            </ul>
        </nav>
	</div>
</section>