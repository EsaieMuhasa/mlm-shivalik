<?php 
use Applications\Common\Modules\Index\IndexController;
use Core\Shivalik\Entities\Product;

/**
 * @var Product $products
 */
$products = $_REQUEST[IndexController::ATT_PRODUCTS];
?>

<div class="default-products">

    <div class="container">
        <h1 class="text-center">Ouwer products</h1>
        <div class="row">
            <?php foreach ($_REQUEST[IndexController::ATT_PRODUCTS] as $product) : ?>
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                <article class="product">
                    <div class="product-header">
                        <img src="/<?php echo $product->picture; ?>" alt="">
                        <div class="prix">
                            <p class="enable-prix"><?php echo $product->defaultUnitPrice; ?> $</p>
                            <p class="disable-prix"></p>
                        </div>
                    </div>
                    <aside>
                        <strong style="border-bottom: none;"><?php echo htmlspecialchars($product->name)?></strong>
                        <!-- 
                        <p><?php echo htmlspecialchars($product->getDescription(150))?>...</p>
                        <a href="" class="btn btn-default custom-btn">See more</a>
                         -->
                    </aside>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>