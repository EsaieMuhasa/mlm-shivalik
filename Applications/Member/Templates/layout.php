<?php
use PHPBackend\AppConfig;
use PHPBackend\Page;
use PHPBackend\Request;
use Core\Shivalik\Filters\SessionMemberFilter;
use Core\Shivalik\Entities\MonthlyOrder;

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
?>
<!DOCTYPE html>

<html lang="en">
    <head>
     	<title>Shivalik</title>
    	<link rel="icon" type="image/jpg" href="<?php echo $config->get('logo'); ?>" />
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content=""/>
        <meta name="keyword" content=""/>
    	<meta name="viewport" content="width=device-width,height=device-height,user-scalable=1"/>

		<link href="/css/dist/bootstrap-index.css" rel="stylesheet"/>
        <link href="/css/dist/bootstrap.min.css" rel="stylesheet"/>
        <link href="/css/dist/bootstrap-theme.css" rel="stylesheet">
        <link href="/css/dist/elegant-icons-style.css" rel="stylesheet" />
        <link href="/css/dist/font-awesome.min.css" rel="stylesheet" />
        <link href="/css/dist/style.css" rel="stylesheet">
        <link href="/css/dist/style-responsive.css" rel="stylesheet" />
    
        <!--[if lt IE 9]>
          <script src="/js/dist/html5shiv.js"></script>
          <script src="/js/dist/respond.min.js"></script>
          <script src="/js/dist/lte-ie7.js"></script>
        <![endif]-->
    </head>
    
    <body>
        <!-- container section start -->
        <section id="container" class="">
            <!--header start-->
            <?php require dirname(dirname(dirname(__DIR__))).'/Core/Shivalik/inc/apps-header.php';?>
            
            <!--sidebar start-->
            <?php require dirname(dirname(dirname(__DIR__))).'/Core/Shivalik/inc/apps-navigation.php';?>
            <!--sidebar end-->
            
            <!--main content start-->
            <section id="main-content">
                <section class="wrapper">
                    <?php require dirname(dirname(dirname(__DIR__))).'/Core/Shivalik/inc/apps-msg-session.php';?>
                    
                    <?php if (isset($_REQUEST[SessionMemberFilter::ATT_MONTHLY_ORDER_FOR_ACCOUNT])) : ?>
                    <?php 
                    /**
                     * @var MonthlyOrder $monthly
                     */
                    $monthly = $_REQUEST[SessionMemberFilter::ATT_MONTHLY_ORDER_FOR_ACCOUNT]; 
                    if($monthly->getDisabilityDate() == null) :
                    ?>
                    <div class="row">
                    	<div class="col-xs-12">
                    		<div class="alert alert-info">
                    			<strong><span class="glyphicon glyphicon-warning-sign"></span> Purchase accounting for the month of <?php echo $monthly->getFormatedDateAjout("M Y") ?> </strong>
                    			<table class="table table-bordered table-condansed">
                    				<tbody>
                    					<tr>
                    						<td>Amount realize </td>
                    						<td><?php echo $monthly->getAmount(); ?> USD</td>
                    					</tr>
                    					<tr>
                    						<td>Used amount</td>
                    						<td><?php echo $monthly->getUsed(); ?> USD</td>
                    					</tr>
                    				</tbody>
                    				<tfoot>
                    					<tr>
                    						<th>Available amount</th>
                    						<th><?php echo $monthly->getAvailable(); ?> USD</th>
                    					</tr>
                    				</tfoot>
                    			</table>
                    		</div>
                    	</div>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php echo $_REQUEST[Page::ATT_VIEW];?>
                </section>
            </section>
            <!--main content end-->
            <?php require dirname(dirname(dirname(__DIR__))).'/Core/Shivalik/inc/apps-footer.php';?>
        </section>
        <!-- container section end -->
        <!-- javascripts -->
        <script src="/js/orgchart.js"></script>
        <script src="/js/dist/jquery.js"></script>
        <script src="/js/dist/bootstrap.min.js"></script>
        <!-- nice scroll -->
        <script src="/js/dist/jquery.scrollTo.min.js"></script>
        <script src="/js/dist/jquery.nicescroll.js" type="text/javascript"></script>
        <!--custome script for all page-->
        <script src="/js/dist/scripts.js"></script>
        <script src="/js/dist/chartjs.js" type="text/javascript"></script>
        <script src="/js/dist/app.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(function (){
            	$('#modal-session-message').modal('show');
            });
        </script>
        <script type="text/javascript" src="/js/dist/ckeditor.js"></script>
        <script src="/js/admin.js"></script>
        <script src="/js/mlm.js"></script>
    </body>
</html>