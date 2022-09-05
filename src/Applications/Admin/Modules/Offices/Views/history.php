<?php
use Applications\Admin\Modules\Offices\OfficesController;
use Core\Shivalik\Entities\Office;
use PHPBackend\AppConfig;
use PHPBackend\Request;
use PHPBackend\Calendar\Month;
use PHPBackend\Calendar\Year;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\Withdrawal;
use Core\Shivalik\Entities\VirtualMoney;
use Core\Shivalik\Entities\RaportWithdrawal;
 

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];

/**
 * @var Month $month
 */
$month = $_REQUEST[OfficesController::ATT_MONTH];
$next = $month->nextMonth();
$prev = $month->previousMonth();
$year = new Year($month->getYear());

/**
 * @var Office $office
 */
$office = $_REQUEST[OfficesController::ATT_OFFICE];

/**
 * @var Member[] $members
 */
$members = $_REQUEST[OfficesController::ATT_MEMBERS];

/**
 * @var GradeMember[] $packets
 */
$packets = $_REQUEST[OfficesController::ATT_GRADES_MEMBERS];

/**
 * @var Withdrawal[] $withdrawals
 */
$withdrawals = $_REQUEST[OfficesController::ATT_WITHDRAWALS];

/**
 * @var VirtualMoney[] $virtuals
 */
$virtuals = $_REQUEST[OfficesController::ATT_VIRTUAL_MONEYS];

/**
 * @var RaportWithdrawal[] $raports
 */
$raports = $_REQUEST[OfficesController::ATT_RAPORTS_WITHDRAWALS];
?>

<div class="row">
	<div class="col-lg-8 col-md-7 col-sm-12 col-xs-12">
		<h1 class="">history of activities from <?php echo ($month->hasSelectedDate()? $month->getFirstSelectedDate()->format("d ") : '').$month; ?></h1>
		<hr/>
		<?php if (!empty($members)) : ?>
        <section class="table-responsive">
        	<table class="table panel panel-default">
        		<caption>Membership</caption>
        		<thead class="panel-heading">
        			<tr>
        				<th>N°</th>
        				<th>Photo</th>
        				<th>Names</th>
        				<th>ID</th>
        				<th>packet</th>
        				<th>Creation date</th>
        			</tr>
        		</thead>
        		<tbody class="panel-body">
        			<?php $num = 0; ?>
					<?php foreach ($members as $user): ?>
    					<tr>
    						<td><?php  $num++; echo ($num);?> </td>
    						<td style="width: 30px;">
    							<img style="width: 30px;" src="/<?php echo ($user->photo);?>">
    						</td>
    						<td><?php echo htmlspecialchars($user->names);?></td>
    						<td><?php echo ($user->matricule);?></td>
    						<td></td>
    						<td><?php echo ($user->dateAjout->format('d/m/Y \a\t H\h:i'));?></td>
    					</tr>
					<?php endforeach; ?>
        		</tbody>
        	</table>
        </section>
        <?php endif; ?>
        
        <?php if (!empty($packets)) : ?>
        <section class="table-responsive">
        	<table class="table panel panel-default">
        		<caption>Upgrades</caption>
        		<thead class="panel-heading">
        			<tr>
        				<th>N°</th>
        				<th>Old</th>
        				<th>New</th>
        				<th>Names</th>
        				<th>ID</th>
        				<th>date and time</th>
        			</tr>
        		</thead>
        		<tbody class="panel-body">
        			<?php $num = 0; ?>
					<?php foreach ($packets as $packet): ?>
    					<tr>
    						<td><?php  $num++; echo ($num);?> </td>
    						<td style="width: 30px;" title="<?php echo htmlspecialchars($packet->getOld()->getGrade()->getName());?>">
    							<img alt="<?php echo htmlspecialchars($packet->getOld()->getGrade()->getName());?>" style="width: 30px;" src="/<?php echo ($packet->getOld()->getGrade()->getIcon());?>" >
    						</td>
    						<td style="width: 30px;" title="<?php echo htmlspecialchars($packet->getGrade()->getName());?>">
    							<img style="width: 30px;" alt="<?php echo htmlspecialchars($packet->getGrade()->getName());?>" src="/<?php echo ($packet->getGrade()->getIcon());?>">
    						</td>
    						<td><?php echo htmlspecialchars($packet->getMember()->getNames());?></td>
    						<td><?php echo ($packet->getMember()->getMatricule());?></td>
    						<td><?php echo ($packet->dateAjout->format('d/m/Y \a\t H\h:i'));?></td>
    					</tr>
					<?php endforeach; ?>
        		</tbody>
        	</table>
        </section>
        <?php endif; ?>
        
        <?php if (!empty($raports)) : ?>
        <section class="table-responsive">
        	<table class="table panel panel-default">
        		<caption>Withdrawals raport</caption>
        		<thead class="panel-heading">
        			<tr>
        				<th>N°</th>
        				<th>Date and time</th>
        				<th></th>
        				<th></th>
        				<th>Sold</th>
        			</tr>
        		</thead>
        		<tbody class="panel-body">
        			<?php $num = 0; ?>
					<?php foreach ($raports as $raport): ?>
    					<tr class="danger">
    						<th><?php  $num++; echo ($num);?></th>
    						<th colspan="3"><?php echo ($raport->dateAjout->format('d/m/Y \a\t H\h:i'));?></th>
    						<th><?php echo ("{$raport->getSold()} {$config->get('devise')}");?></th>
    					</tr>
    					
    					<?php foreach ($raport->getWithdrawals() as $key => $withdrawal) : ?>
    					
    					<tr>
    						<td><?php echo ("{$num}.{$key}.");?> </td>
    						<td><?php echo ($withdrawal->dateAjout->format('d/m/Y \a\t H\h:i'));?></td>
    						<td><?php echo htmlspecialchars($withdrawal->getMember()->getNames());?></td>
    						<td><?php echo ($withdrawal->getMember()->getMatricule());?></td>
    						<td><?php echo ("{$withdrawal->getAmount()} {$config->get('devise')}");?></td>
    					</tr>
    					
    					<?php endforeach;?>
					<?php endforeach; ?>
        		</tbody>
        	</table>
        </section>
        <?php endif;?>
        
        <?php if (!empty($withdrawals)) : ?>
        <section class="table-responsive">
        	<table class="table panel panel-default">
        		<caption>Withdrawals</caption>
        		<thead class="panel-heading">
        			<tr>
        				<th>N°</th>
        				<th>Names</th>
        				<th>ID</th>
        				<th>Amount</th>
        				<th>Served</th>
        				<th>Raport</th>
        				<th>date and time</th>
        			</tr>
        		</thead>
        		<tbody class="panel-body">
        			<?php $num = 0; ?>
					<?php foreach ($withdrawals as $withdrawal): ?>
    					<tr>
    						<td><?php  $num++; echo ($num);?> </td>
    						<td><?php echo htmlspecialchars($withdrawal->getMember()->getNames());?></td>
    						<td><?php echo ($withdrawal->getMember()->getMatricule());?></td>
    						<td><?php echo ("{$withdrawal->getAmount()} {$config->get('devise')}");?></td>
    						<td class="<?php echo ($withdrawal->getAdmin()!=null? "text-success":"text-danger"); ?>">
    							<span class="glyphicon glyphicon-<?php echo ($withdrawal->getAdmin()!=null? "ok":"remove"); ?>"></span>
    						</td>
    						<td class="<?php echo ($withdrawal->getRaport()!=null? "text-success":"text-danger"); ?>">
    							<span class="glyphicon glyphicon-<?php echo ($withdrawal->getRaport()!=null? "ok":"remove"); ?>"></span>
    						</td>
    						<td><?php echo ($withdrawal->dateAjout->format('d/m/Y \a\t H\h:i'));?></td>
    					</tr>
					<?php endforeach; ?>
        		</tbody>
        	</table>
        </section>
        <?php endif;?>
        
        <?php if (!empty($virtuals)) : ?>
        <section class="table-responsive">
        	<table class="table panel panel-default">
        		<caption>Virtual money</caption>
        		<thead class="panel-heading">
        			<tr>
        				<th>N°</th>
        				<th>Expected</th>
        				<th>Amount</th>
        				<th>date and time</th>
        			</tr>
        		</thead>
        		<tbody class="panel-body">
        			<?php $num = 0; ?>
					<?php foreach ($virtuals as $virtual): ?>
    					<tr>
    						<td><?php  $num++; echo ($num);?> </td>
    						<td><?php echo ("{$virtual->getExpected()} {$config->get('devise')}");?></td>
    						<td><?php echo ("{$virtual->getAmount()} {$config->get('devise')}");?></td>
    						<td><?php echo ($virtual->dateAjout->format('d/m/Y \a\t H\h:i'));?></td>
    					</tr>
					<?php endforeach; ?>
        		</tbody>
        	</table>
        </section>
        <?php endif;?>
	</div>
	
	
	<!-- calendar -->
	<div class="col-lg-4 col-md-5 col-md-offset-0 col-sm-6 col-sm-offset-6 col-xs-12 col-xs-offset-0">
		<div class="panel panel-default">
	        <header class="panel-heading">
	            <h2 class="panel-title"><?php echo $month->toString();?></h2>
	            <span class="pull-right">
	            	<span class="btn-group">
    	            	<a title="<?php echo ($prev);?>" class="btn btn-primary" href="/admin/offices/<?php echo ("{$office->getId()}/history/{$prev->getFirstDay()->format('m-Y')}");?>.html">
    	        			<span class="glyphicon glyphicon-step-backward"></span>
        	        	</a>
        	        	<a title="<?php echo ($next);?>" class="btn btn-info" href="/admin/offices/<?php echo ("{$office->getId()}/history/{$next->getFirstDay()->format('m-Y')}");?>.html">
        	        		<span class="glyphicon glyphicon-step-forward"></span>
        	        	</a>
	            	</span>
	            </span>
	        </header>
	        
	        <table class="table table-bordered text-center table-links">
	            <thead class="text-center">
	                <tr>
	                	<?php foreach($month->getShortDaysName() as $dayName) : ?>
	                    <th class="text-center"><?php echo $dayName;?></th>
	                    <?php endforeach; ?>
	                </tr>
	            </thead>
	            
	            <tbody>
	            	<?php while ($month->hasNextWeek()) :
	            	   $month->nextWeek();
	            	?>
	                <tr>
	                    <?php while ($month->hasNextDay()) : ?>
	                    <?php $date =  $month->nextDay(); ?>
	                    <td class="<?php echo ($month->isSelectedDate($date)? 'selected-date ':'').($month->isToday($date)? ' today':'').($month->isEventDate($date)? 'has-event':'');?>">
	                        <?php if ($month->inMonth($date)) :?>
	                    	<a href="/admin/offices/<?php echo ("{$office->getId()}/history/{$date->format('d-m-Y')}");?>.html" class="btn btn-xs <?php echo ($month->isSelectedDate($date)? 'btn-primary ':'').($month->isToday($date)? ' text-primary':'').($month->isEventDate($date)? 'text-danger':'');?>">
	                    		<span class=""><?php echo $date->format('d');?></span>
	                    	</a>
	                        <?php endif; ?>
	                    </td>
	                    <?php endwhile; ?>
	                </tr>
	                <?php endwhile; ?>
	            </tbody>
	        </table>
	        <div class="panel-footer">
	        	<div class="row">
	        		<div class="col-xs-3">
        	        	<a title="<?php echo ($month->getYear()-1);?>" class="btn btn-primary btn-block" href="/admin/offices/<?php echo "{$office->getId()}/history/01-".($month->getYear()-1);?>.html">
        	        		<span class="glyphicon glyphicon-fast-backward"></span> <?php echo htmlspecialchars($month->getYear()-1)?>
        	        	</a>
	        		</div>
	        		<div class="col-xs-6">
        	        	<div class="dropup">
                        	<button class="btn btn-info dropdown-toggle btn-block" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        		<?php echo htmlspecialchars($month)?> <span class="caret"></span>
                        	</button>
                        	<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        		<?php for ($i = 1; $i <= 12 ; $i++) : 
                        		  $m = $year->get($i); ?>                        		
                        		<li class="<?php echo ($i == $month->getMonth()? "active" : ""); ?>">
                        			<a href="/admin/offices/<?php echo "{$office->getId()}/history/{$m->getMonth()}-{$year}";?>.html"><?php echo htmlspecialchars($m->getName())?></a>
                        		</li>
                        		<?php endfor;?>
                        	</ul>
                        </div>
	        		</div>
	        		<div class="col-xs-3">
        	        	<a title="<?php echo ($month->getYear()+1);?>" class="btn btn-primary btn-block" href="/admin/offices/<?php echo "{$office->getId()}/history/01-".($month->getYear()+1);?>.html">
        	        		<?php echo htmlspecialchars($month->getYear()+1)?> <span class="glyphicon glyphicon-fast-forward"></span> 
        	        	</a>
	        		</div>
	        	</div> 
	        </div>
	   	</div>
	</div>
</div>