<?php
use Applications\Member\Modules\MyOffice\MyOfficeController;
use Applications\Member\MemberApplication;
use PHPBackend\AppConfig;
use PHPBackend\Request;
use PHPBackend\Calendar\Year;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\GradeMember;
use Core\Shivalik\Entities\Withdrawal;
use Core\Shivalik\Entities\VirtualMoney;
 

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];

/**
 * @var \PHPBackend\Calendar\Month $month
 */
$month = $_REQUEST[MyOfficeController::ATT_MONTH];
$next = $month->nextMonth();
$prev = $month->previousMonth();
$year = new Year($month->getYear());

/**
 * @var Office $office
 */
$office = MemberApplication::getConnectedMember()->getOfficeAccount();

/**
 * @var Member[] $members
 */
$members = $_REQUEST[MyOfficeController::ATT_MEMBERS];

/**
 * @var GradeMember[] $packets
 */
$packets = $_REQUEST[MyOfficeController::ATT_GRADES_MEMBERS];

/**
 * @var Withdrawal[] $withdrawals
 */
$withdrawals = $_REQUEST[MyOfficeController::ATT_WITHDRAWALS];

/**
 * @var VirtualMoney[] $virtuals
 */
$virtuals = $_REQUEST[MyOfficeController::ATT_VIRTUAL_MONEYS];
?>

<div class="row">
	<div class="col-lg-8 col-md-7 col-sm-12 col-xs-12">
		<h1 class="">history of activities from <?php echo ($month->hasSelectedDate()? $month->getFirstSelectedDate()->format("d ") : '').$month; ?></h1>
		<hr/>
		<?php if (!empty($members)) : ?>
		<div class="panel panel-default">
	        <header class="panel-heading">
	        	<h2 class="panel-title">Membership</h2>
	        </header>
	        <section class="table-responsive">
	        	<table class="table">
	        		<caption></caption>
	        		<thead>
	        			<tr>
	        				<th>N°</th>
	        				<th>Photo</th>
	        				<th>Names</th>
	        				<th>ID</th>
	        				<th>packet</th>
	        				<th>Creation date</th>
	        			</tr>
	        		</thead>
	        		<tbody>
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
        </div>
        <?php endif; ?>
        
        <?php if (!empty($packets)) : ?>
        <div class="panel panel-default">
	        <header class="panel-heading">
	        	<h2 class="panel-title">Upgrades</h2>
	        </header>
	        <section class="table-responsive">
	        	<table class="table">
	        		<caption></caption>
	        		<thead>
	        			<tr>
	        				<th>N°</th>
	        				<th>Old</th>
	        				<th>New</th>
	        				<th>Names</th>
	        				<th>ID</th>
	        				<th>date and time</th>
	        			</tr>
	        		</thead>
	        		<tbody>
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
        </div>
        <?php endif; ?>
        
        <?php if (!empty($withdrawals)) : ?>
        <div class="panel panel-default">
	        <header class="panel-heading">
	        	<h2 class="panel-title">Withdrawals</h2>
	        </header>
	        <section class="table-responsive">
	        	<table class="table">
	        		<thead>
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
	        		<tbody>
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
        </div>
        <?php endif;?>
        
        <?php if (!empty($virtuals)) : ?>
        <div class="panel panel-default">
	        <header class="panel-heading">
	        	<h2 class="panel-title">Virtual money</h2>
	        </header>
	        <section class="table-responsive">
	        	<table class="table">
	        		<caption></caption>
	        		<thead>
	        			<tr>
	        				<th>N°</th>
	        				<th>Amount</th>
	        				<th>date and time</th>
	        			</tr>
	        		</thead>
	        		<tbody>
	        			<?php $num = 0; ?>
						<?php foreach ($virtuals as $virtual): ?>
	    					<tr>
	    						<td><?php  $num++; echo ($num);?> </td>
	    						<td><?php echo ("{$virtual->getAmount()} {$config->get('devise')}");?></td>
	    						<td><?php echo ($virtual->dateAjout->format('d/m/Y \a\t H\h:i'));?></td>
	    					</tr>
						<?php endforeach; ?>
	        		</tbody>
	        	</table>
	        </section>
        </div>
        <?php endif;?>
	</div>
	
	
	<!-- calendar -->
	<div class="col-lg-4 col-md-5 col-md-offset-0 col-sm-6 col-sm-offset-6 col-xs-12 col-xs-offset-0">
		<div class="panel panel-default">
	        <header class="panel-heading">
	            <h2 class="panel-title"><?php echo $month->toString();?></h2>
	            <span class="pull-right">
	            	<span class="btn-group">
    	            	<a title="<?php echo ($prev);?>" class="btn btn-primary" href="/member/office/history/<?php echo ("{$prev->getFirstDay()->format('m-Y')}");?>.html">
    	        			<span class="glyphicon glyphicon-step-backward"></span>
        	        	</a>
        	        	<a title="<?php echo ($next);?>" class="btn btn-info" href="/member/office/history/<?php echo ("{$next->getFirstDay()->format('m-Y')}");?>.html">
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
	                    	<a href="/member/office/history/<?php echo ("{$date->format('d-m-Y')}");?>.html" class="btn btn-xs <?php echo ($month->isSelectedDate($date)? 'btn-primary ':'').($month->isToday($date)? ' text-primary':'').($month->isEventDate($date)? 'text-danger':'');?>">
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
        	        	<a title="<?php echo ($month->getYear()-1);?>" class="btn btn-primary btn-block" href="/member/office/history/01-<?php echo ($month->getYear()-1);?>.html">
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
                        			<a href="/member/office/history/<?php echo "{$m->getMonth()}-{$year}";?>.html"><?php echo htmlspecialchars($m->getName())?></a>
                        		</li>
                        		<?php endfor;?>
                        	</ul>
                        </div>
	        		</div>
	        		<div class="col-xs-3">
        	        	<a title="<?php echo ($month->getYear()+1);?>" class="btn btn-primary btn-block" href="/member/office/history/01-<?php echo ($month->getYear()+1);?>.html">
        	        		<?php echo htmlspecialchars($month->getYear()+1)?> <span class="glyphicon glyphicon-fast-forward"></span> 
        	        	</a>
	        		</div>
	        	</div> 
	        </div>
	   	</div>
	</div>
</div>