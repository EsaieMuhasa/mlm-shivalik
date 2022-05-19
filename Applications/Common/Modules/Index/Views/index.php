<?php
use Applications\Common\Modules\Index\IndexController;
use Core\Shivalik\Filters\SessionAdminFilter;
use Core\Shivalik\Filters\SessionOfficeFilter;
use Core\Shivalik\Filters\SessionMemberFilter;

$admin = isset($_SESSION[SessionAdminFilter::ADMIN_CONNECTED_SESSION])? $_SESSION[SessionAdminFilter::ADMIN_CONNECTED_SESSION] : null;//admin centrale
$office = isset($_SESSION[SessionOfficeFilter::OFFICE_CONNECTED_SESSION])? $_SESSION[SessionOfficeFilter::OFFICE_CONNECTED_SESSION] : null;//admin d'un office secondaire
$member = isset($_SESSION[SessionMemberFilter::MEMBER_CONNECTED_SESSION])? $_SESSION[SessionMemberFilter::MEMBER_CONNECTED_SESSION] : null;//membre adherant
?>
<header class="banner">
    <div class="sliders">
        <div class="banner-carousel">
            <img src="/img/intro-bg.jpg" alt="">
            <img src="/img/intro-bg.jpg" alt="">
            <img src="/img/intro-bg.jpg" alt="">
            <img src="/img/intro-bg.jpg" alt="">
            <img src="/img/intro-bg.jpg" alt="">
        </div>
        <div class="home-carousel-mins"></div>
    </div>

    <div class="container">
        <div class="welcom">

            <h1>We Are Shivalik Herbals</h1>
            <p>
                Shivalik Herbals is one of the best Ayurvedic &amp; Natural Health Care Company in India. It is GMP (Good manufacturing practices as per the norms of WHO) &amp; ISO 9001:2008 certified company with a portfolio of over 200 Herbal/Ayurvedic products. We have a unique range of world class ayurvedic products and have expertise in Herbal &amp; Ayurvedic Capsules, Tablets, Powders, Tea, Prash, Oils &amp; creams. Your search ends here as here you can find the perfect balance between science of ancient Ayurveda and the modern manufacturing and testing techniques of all the times. We are pioneer in this field for the last 25 years. With its perfect solutions Shivalik Herbals has touched lives of millions of people across globe.
            </p>

            <div class="text-center">
                <?php if ($admin != null) { ?>
            	<a class="btn btn-primary btn-lg custom-btn" href="/admin/" title="<?php echo htmlspecialchars($admin->getNames()); ?>">
            		<img style="width: 20px;border-radius: 50%;" alt="" src="/<?php echo ($admin->getPhoto()); ?>">
            		<?php echo htmlspecialchars($admin->getLastName()); ?>
        		</a>
        		<?php } else if ($office != null) { ?>
            	<a class="btn btn-primary btn-lg custom-btn" href="/office/" title="<?php echo htmlspecialchars($office->getNames()); ?>">
            		<img style="width: 20px;border-radius: 50%;" alt="" src="/<?php echo ($office->getPhoto()); ?>">
            		<?php echo htmlspecialchars($office->getLastName()); ?>
        		</a>
            	<?php } else if ($member != null) { ?>
            	<a class="btn btn-primary btn-lg custom-btn" href="/member/" title="<?php echo htmlspecialchars($member->getNames()); ?>">
            		<img style="width: 20px;border-radius: 50%;" alt="" src="/<?php echo ($member->getPhoto()); ?>">
            		<?php echo htmlspecialchars($member->getLastName()); ?>
        		</a>
            	<?php } else {?>
                <a class="btn btn-primary btn-lg custom-btn" href="/login.html">Login</a>
            	<?php }?>
            	
                <a class="btn btn-default btn-lg custom-btn" href="/about.html">Learn more</a>
            </div>
        </div>
    </div>
</header>

<section class="">
    <div class="why-section">
        <div class="container">
            <h1>Why Shivalik Herbals ?</h1>
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-land-6 col-xs-12">
                    <div class="default-card">
                        <div class="card-icon">
                            <span class="fa fa-ok-circled"></span>
                        </div>
                        <div class="card-caption">
                            <p>
                                Best quality at right price.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6  col-xs-land-6 col-xs-12">
                    <div class="default-card">
                        <div class="card-icon">
                            <span class="fa fa-location"></span>
                        </div>
                        <div class="card-caption">
                            <p>
                                Product can be customised according to buyer requirement.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-land-6 col-xs-12">
                    <div class="default-card">
                        <div class="card-icon">
                            <span class="fa fa-user-md"></span>
                        </div>
                        <div class="card-caption">
                            <p>
                                We manufacture the complete range of Herbal <?php echo htmlspecialchars('&'); ?> Ayurvedic Products.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-land-6 col-xs-12">
                    <div class="default-card">
                        <div class="card-icon">
                            <span class="fa fa-basket"></span>
                        </div>
                        <div class="card-caption">
                            <p>
                                Our Products has undergone stringent laboratory tests at various stages.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-land-6 col-xs-12">
                    <div class="default-card">
                        <div class="card-icon">
                            <span class="fa fa-users"></span>
                        </div>
                        <div class="card-caption">
                            <p>
                                Experienced &amp; well qualified team.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-land-6 col-xs-12">
                    <div class="default-card">
                        <div class="card-icon">
                            <span class="fa fa-money"></span>
                        </div>
                        <div class="card-caption">
                            <p>
                                Advanced machinery.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-land-6 col-xs-12">
                    <div class="default-card">
                        <div class="card-icon">
                            <span class="fa fa-sitemap"></span>
                        </div>
                        <div class="card-caption">
                            <p>
                                Timely delivery.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6  col-xs-land-6 col-xs-12">
                    <div class="default-card">
                        <div class="card-icon">
                            <span class="fa fa-globe"></span>
                        </div>
                        <div class="card-caption">
                            <p>
                                Our Products has undergone stringent laboratory tests at various stages.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<?php if (!empty($_REQUEST[IndexController::ATT_PRODUCTS])) : ?>
    <div class="default-products">

        <div class="container">
            <h1>Ouwer products</h1>
        </div>

        <div class="init-default-carousel default-products-carousel">
        	<?php foreach ($_REQUEST[IndexController::ATT_PRODUCTS] as $product) : ?>
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
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
</section>
<div class="certified-section">
	<div class="container">
		<h1>A GMP &amp; ISO 9001: 2015 Certified Company</h1>
    	
    	<div class="row">
    		<div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
    			<img src="/img/haccp.png" class="thumbnail"/>
    		</div>
    		
    		<div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
    			<img src="/img/iso-9001.png" class="thumbnail"/>
    		</div>
    		
    		<div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
    			<img src="/img/halal.png" class="thumbnail"/>
    		</div>
    		
    		<div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
    			<img src="/img/iso-22000.png" class="thumbnail"/>
    		</div>
    		
    		<div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
    			<img src="/img/gmp.png" class="thumbnail"/>
    		</div>
    		
    		<div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
    			<img src="/img/natural.png" class="thumbnail"/>
    		</div>
    	</div>
	</div>
</div>
