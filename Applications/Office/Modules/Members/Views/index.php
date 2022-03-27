<?php
use Applications\Office\Modules\Members\MembersController;
use PHPBackend\AppConfig;
use PHPBackend\Request;


/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
$maxMembers = intval($config->get('maxMembers')->getValue(), 10);
$max = intval($_REQUEST[MembersController::PARAM_MEMBER_COUNT], 10);

$offset = isset($_GET['offset'])? $_GET['offset'] : 0;

?>
<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header">
    		<i class="fa fa-users"></i> <?php echo ($_REQUEST[MembersController::ATT_VIEW_TITLE]); ?>
    	</h3>
    </div>
	<div class="col-xs-12 col-sm-5 col-md-6">
		<a class="btn btn-primary" href="/office/members/add.html"><span class="fa fa-plus"></span> News acount</a>
		
        <?php if ($maxMembers < $max) { ?>
        <div class="btn-group">
        	<?php if (isset($_GET['offset']) && ($_GET['offset']-$maxMembers)>=0){ ?>
        	<a href="<?php echo ($maxMembers).'-'.(isset($_GET['offset'])? ($_GET['offset']-$maxMembers): 0); ?>.html" class="btn btn-info"><span class="glyphicon glyphicon-step-backward"></span>Prev</a>
        	<?php }?>
        	
        	<?php if (!isset($_GET['offset']) || (isset($_GET['offset']) && (($_GET['offset']+$maxMembers) <= ($max)))){ ?>
        	<a href="<?php echo ($maxMembers).'-'.(isset($_GET['offset'])? ($_GET['offset']+$maxMembers): ($maxMembers)); ?>.html" class="btn btn-primary">Next<span class="glyphicon glyphicon-step-forward"></span></a>
        	<?php } ?>
        </div>
        <?php } ?>
	</div>
	<div class="col-xs-6 col-sm-7 col-md-6">
		<form action="" method="post">
			<div class="input-group">
    			<input type="text" name="id" value="<?php echo htmlspecialchars(isset($_REQUEST['withdrawal'])? $_REQUEST['withdrawal']->amount:'');?>" id="amount-office" class="form-control" placeholder="put here member ID" autocomplete="off"/>
				<span class="input-group-btn">
					<button class="btn btn-primary">Go</button>
				</span>
			</div>
		</form>
	</div>
</div>
<hr/>

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<ul class="panel-heading nav nav-tabs">
				<li role="presentation" class="<?php echo ((isset($_GET['affichage']) && $_GET['affichage'] == 'grid')? "active" : ""); ?>">
					<a href="/office/members/grid/<?php echo (isset($_GET['offset'])? "{$_GET['limit']}-{$offset}.html":""); ?>"><span class="glyphicon glyphicon-th-large"></span> Grid</a>
				</li>
				<li role="presentation" class="<?php echo ((!isset($_GET['affichage']) || $_GET['affichage'] == 'table')? "active" : ""); ?>">
					<a href="/office/members/table/<?php echo (isset($_GET['offset'])? "{$_GET['limit']}-{$offset}.html":""); ?>"><span class="fa fa-table"></span> Table</a>
				</li>
			</ul>
			<?php if (isset($_GET['affichage']) && $_GET['affichage'] == 'grid') { ?>
			<div class="panel-body">
				<div class="row">
				    <div class="col-lg-push-0 col-lg-12 col-md-push-0 col-md-12 col-sm-push-0 col-sm-12 col-xs-10 col-xs-push-1" style="padding-top: 20px;">
				    
				    	<?php if (!empty($_REQUEST[MembersController::ATT_MEMBERS])) { ?>
				    	<div class="row">
				    		<?php  foreach ($_REQUEST[MembersController::ATT_MEMBERS] as $user) : ?>
				    		<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
				        		<a class="thumbnail" href="/office/members/<?php echo  $user->id; ?>/">
				        			<strong style="font-size: 2rem;" class="">ID: <?php echo htmlspecialchars("{$user->matricule}"); ?></strong>
				        			<img class="image-rond" alt="icon <?php echo htmlspecialchars($user->name); ?>" src="/<?php echo ($user->photo); ?>">
				        			<em class="label label-<?php echo ($user->enable? 'primary':'danger') ?>"style=""><?php echo htmlspecialchars("{$user->name} {$user->postName} {$user->lastName}"); ?></em>
				        		</a>
				             </div>
				    		<?php endforeach;?>
				    	</div>
				    	<?php } else {?>
				    	<div class="alert alert-danger">
				    		<p>No member in database </p>
				    	</div>
				    	<?php }?>
				    </div>
				</div>
			</div>
			<?php } else { ?>
			<section class="table-responsive">
	        	<table class="table table-bordered">
	        		<thead>
	        			<tr>
	        				<th>N°</th>
	        				<th>Photo</th>
	        				<th>Names</th>
	        				<th>ID</th>
	        				<th>Username</th>
	        				<th>Creation date</th>
	        				<th>packet</th>
	        				<th>Options</th>
	        			</tr>
	        		</thead>
	        		<tbody>
	        			<?php $num = 0; ?>
						<?php foreach ($_REQUEST[MembersController::ATT_MEMBERS] as $user): ?>
	    					<tr>
	    						<td><?php 
	    						$num++;
	    						echo ($num+$offset);?>
	    						</td>
	    						<td style="width: 30px;">
	    							<img style="width: 30px;" src="/<?php echo ($user->photo);?>">
	    						</td>
	    						<td><?php echo htmlspecialchars($user->names);?></td>
	    						<td><?php echo ($user->matricule);?></td>
	    						<td><?php echo ($user->pseudo);?></td>
	    						<td><?php echo ($user->dateAjout->format('d/m/Y \a\t H\h:i'));?></td>
	    						<td title="<?php echo htmlspecialchars($user->packet->grade->name);?>">
	    							<img style="width: 30px;" alt="<?php echo htmlspecialchars($user->packet->grade->name);?>" src="/<?php echo htmlspecialchars($user->packet->grade->icons->getXs());?>"/>
	    						</td>
	    						<td>
	    							<a class="btn btn-info" href="/office/members/<?php echo  $user->id; ?>/">
	    								<span class="glyphicon glyphicon-user"></span> Show account
	    							</a>
	    						</td>
	    					</tr>
						<?php endforeach; ?>
	        		</tbody>
	        	</table>
	        </section>
			<?php } ?>
			
			<?php if ($maxMembers < $max) :?>
			<div class="panel-footer">
				<div class="">
				
					<?php
					$steep = 0;
					for($i=0; $i<($max); $i += $maxMembers) {  ?>
					<a href="<?php echo ($maxMembers).'-'.($steep*$maxMembers); ?>.html" class="btn btn-<?php echo (((isset($_GET['offset']) && ($_GET['offset'] == ($steep*$maxMembers))) || (!isset($_GET['offset']) && $steep==0))? 'danger':'primary'); ?>"><?php echo ($steep); ?></a>
					<?php $steep++;}?>
				
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
