<?php
use Applications\Admin\AdminApplication;
use Applications\Member\MemberApplication;
use Applications\Office\OfficeApplication;
use Applications\Common\Modules\Index\IndexController;
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
                At Shivalik Herbals we are manufacturing Herbal <?php echo htmlspecialchars('&'); ?> Ayurvedic Products Since 2004 and we are working in this field for the last 25 years. Shivalik Herbals is a GMP <?php echo htmlspecialchars('&'); ?> ISO 9001:2015 certified company. We are manufacturing Herbal <?php echo htmlspecialchars('&'); ?> Ayurvedic Capsules, Powders,Tea, Prash, Oils <?php echo htmlspecialchars('&'); ?> cosmetics. Shivalik Herbals is the place where you can find the perfect balance between science of ancient Ayurveda and the modern manufacturing and testing techniques.
            </p>

            <div class="text-center">
                <?php if (AdminApplication::getConnectedUser() != null) { ?>
            	<a class="btn btn-primary btn-lg custom-btn" href="/admin/" title="<?php echo htmlspecialchars(AdminApplication::getConnectedUser()->getNames()); ?>">
            		<img style="width: 20px;border-radius: 50%;" alt="" src="/<?php echo (AdminApplication::getConnectedUser()->getPhoto()); ?>">
            		<?php echo htmlspecialchars(AdminApplication::getConnectedUser()->getLastName()); ?>
        		</a>
        		<?php } else if (OfficeApplication::getConnectedUser() != null) { ?>
            	<a class="btn btn-primary btn-lg custom-btn" href="/office/" title="<?php echo htmlspecialchars(OfficeApplication::getConnectedUser()->getNames()); ?>">
            		<img style="width: 20px;border-radius: 50%;" alt="" src="/<?php echo (OfficeApplication::getConnectedUser()->getPhoto()); ?>">
            		<?php echo htmlspecialchars(OfficeApplication::getConnectedUser()->getLastName()); ?>
        		</a>
            	<?php } else if (MemberApplication::getConnectedMember() != null) { ?>
            	<a class="btn btn-primary btn-lg custom-btn" href="/member/" title="<?php echo htmlspecialchars(MemberApplication::getConnectedMember()->getNames()); ?>">
            		<img style="width: 20px;border-radius: 50%;" alt="" src="/<?php echo (MemberApplication::getConnectedMember()->getPhoto()); ?>">
            		<?php echo htmlspecialchars(MemberApplication::getConnectedMember()->getLastName()); ?>
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
                                Product can be supplied to any part of the world according to buyer requirement.
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
                                Best quality at right price.
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
                                Product can be supplied to any part of the world according to buyer requirement.
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
                                We manufacture the complete range of Herbal <?php echo htmlspecialchars('&'); ?> Ayurvedic Products.
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
                        <p class="disable-prix">4.3 $</p>
                    </div>
                </div>
                <aside>
                    <strong><?php echo htmlspecialchars($product->name)?></strong>
                    <p><?php echo htmlspecialchars($product->getDescription(150))?>...</p>
                    <a href="" class="btn btn-default custom-btn">See more</a>
                </aside>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
