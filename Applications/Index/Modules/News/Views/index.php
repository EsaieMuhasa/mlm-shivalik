<?php
use Applications\Admin\AdminApplication;
use Applications\Member\MemberApplication;
use Applications\Office\OfficeApplication;
?>

<header id="header" style="">
    <div class="intro">
        <div class="container">
            <div class="row">
				<div class="intro-text">
                    <h1>Shivalik</h1>
                    <p>Welcom to Shivalik herbals</p>
                    <?php if (AdminApplication::getConnectedUser() != null) { ?>
                	<a href="/admin/" style="text-transform: inherit;" class="btn btn-custom btn-lg" title="<?php echo htmlspecialchars(AdminApplication::getConnectedUser()->getNames()); ?>">
                		<img style="width: 20px;border-radius: 50%;" alt="" src="/<?php echo (AdminApplication::getConnectedUser()->getPhoto()); ?>">
                		<?php echo htmlspecialchars(AdminApplication::getConnectedUser()->getLastName().' '.AdminApplication::getConnectedUser()->getName()); ?>
            		</a>
            		<?php }else if (OfficeApplication::getConnectedUser() != null) { ?>
                	<a href="/office/" style="text-transform: inherit;" class="btn btn-custom btn-lg" title="<?php echo htmlspecialchars(OfficeApplication::getConnectedUser()->getNames()); ?>">
                		<img style="width: 20px;border-radius: 50%;" alt="" src="/<?php echo (OfficeApplication::getConnectedUser()->getPhoto()); ?>">
                		<?php echo htmlspecialchars(OfficeApplication::getConnectedUser()->getLastName().' '.OfficeApplication::getConnectedUser()->getName()); ?>
            		</a>
                	<?php } else if (MemberApplication::getConnectedMember() != null) { ?>
                	<a href="/member/" style="text-transform: inherit;" class="btn btn-custom btn-lg" title="<?php echo htmlspecialchars(MemberApplication::getConnectedMember()->getNames()); ?>">
                		<img style="width: 20px;border-radius: 50%;" alt="" src="/<?php echo (MemberApplication::getConnectedMember()->getPhoto()); ?>">
                		<?php echo htmlspecialchars(MemberApplication::getConnectedMember()->getLastName().' '.MemberApplication::getConnectedMember()->getName()); ?>
            		</a>
                	<?php } else {?>
                	<a href="/login.html" class="btn btn-custom btn-lg">Login</a>
                	<?php }?>
                </div>
            </div>
        </div>
    </div>
</header>


<!-- About Section -->
<div id="about">
    <div class="container">
        <div class="section-title text-center center">
        	<h2>About Shivalik</h2>
        	<hr>
        </div>
        <div class="row">
        	<div class="col-xs-12 col-md-6">
        		<img src="img/about.jpg" class="img-responsive" alt=""/>
        	</div>
        	<div class="col-xs-12 col-md-6">
				<div class="about-text">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis sed dapibus leo nec ornare diam. Sed commodo nibh ante facilisis bibendum dolor feugiat at. Duis sed dapibus leo nec ornare diam commodo nibh.</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis sed dapibus leo nec ornare diam. Sed commodo nibh ante facilisis bibendum dolor feugiat at. Duis sed dapibus leo nec ornare.</p>
                    <a href="#portfolio" class="btn btn-default btn-lg page-scroll">our products</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Portfolio Section -->
<div id="portfolio">
    <div class="container">
        <div class="section-title text-center center">
        	<h2>Products</h2>
        	<hr>
        </div>
        
        <div class="row">
        	<div class="portfolio-items">
        		<?php for ($i = 1; $i <= 20; $i++) :?>
        		<div class="col-sm-6 col-md-3 col-lg-3 web">
        			<div class="portfolio-item">
    					<div class="hover-bg">
    						<a href="<?php echo "/img/products/{$i}.jpg" ?>" title="<?php echo $i; ?>" data-lightbox-gallery="gallery1">
                				<span class="hover-text">
                  					<strong><?php echo $i; ?></strong>
                				</span>
                				<img src="<?php echo "/img/products/{$i}.jpg";?>" class="img-responsive" alt="picture product">
            				</a>
        				</div>
    				</div>
    			</div>
    			<?php endfor;?>
    		</div>
		</div>
	</div>
</div>



<!-- Contact Section -->
  <div id="contact" class="text-center">
    <div class="container">
      <div class="section-title center">
        <h2>Get In Touch</h2>
        <hr>
      </div>
      <div class="col-md-8 col-md-offset-2">
        <form name="sentMessage" id="contactForm" novalidate>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <input type="text" id="name" class="form-control" placeholder="Name" required="required">
                <p class="help-block text-danger"></p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <input type="email" id="email" class="form-control" placeholder="Email" required="required">
                <p class="help-block text-danger"></p>
              </div>
            </div>
          </div>
          <div class="form-group">
            <textarea name="message" id="message" class="form-control" rows="4" placeholder="Message" required></textarea>
            <p class="help-block text-danger"></p>
          </div>
          <div id="success"></div>
          <button type="submit" class="btn btn-default btn-lg">Send Message</button>
        </form>
        <div class="social">
          <ul>
            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
            <li><a href="#"><i class="fa fa-dribbble"></i></a></li>
            <li><a href="#"><i class="fa fa-behance"></i></a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>