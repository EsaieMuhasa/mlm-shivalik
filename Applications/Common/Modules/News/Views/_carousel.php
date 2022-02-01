<header id="header">
    <div id="baniere" class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding: 0px;">
    	<div id="baniere-site" class="carousel slide" data-ride="carousel">
    		<ol class="carousel-indicators">
    		<?php for ($i = 0; $i<5; $i++) {?>
    			<li data-target="#baniere-site" data-slide-to="<?php echo ($i+1);?>" class="<?php echo ($i==0? 'active':'');?>"></li>
    		<?php }?>
    		</ol>
    		<div class="carousel-inner">
    		<?php for ($i=0; $i<5; $i++) {?>
    			<div class="item <?php echo ($i==0? 'active':'');?>">
    				<div class="carousel-caption">
    					<h1>Shivalik</h1>
                    	<p>Wellcom to Shivalik internationnal</p>
                    	<a href="/login.html" class="btn btn-custom btn-lg">Login</a>
                    	
    				</div>
        			<img src="/img/bg-1.jpg" alt="" style="width: 100%;">
        		</div>
        	<?php }?>
        	</div>
    		<a class="left carousel-control" href="#baniere-site" role="button" data-slide="prev">
    			<span class="scroll-btn">
    				<span class="icon-prev" aria-hidden="true"></span>
    				<span class="sr-only">Prcedant</span>
    			</span>
    		</a>
    		<a class="right carousel-control" href="#baniere-site" role="button" data-slide="next">
    			<span class="scroll-btn">	
    				<span class="icon-next" aria-hidden="true"></span>
    				<span class="sr-only">Suivant</span>
    			</span>
    		</a>
    	</div>
    </div>
</header>