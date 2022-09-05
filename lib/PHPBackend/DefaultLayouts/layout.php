<?php
use PHPBackend\Page;
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
    	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    	<title>PHPBackend</title>
    	<style type="text/css">
    	   body{
    	       padding: 20px;
    	       margin: 0px;
    	       font-size: 1.2rem;
    	   }
    	   
    	   h1, h2 {
    	       text-align: center;
    	       font-weight: normal;
    	       padding: 5px;
    	   }
    	   
    	   pre {
    	       max-width: 100%;
    	       overflow: auto;
    	   }
    	   
    	   .panel {
    	       border: 1px solid #d0d0d0;
    	       padding: 10px;
    	   }
    	   
    	   .panel-title{
    	       border-bottom: 1px solid #d0d0d0;
    	   }
    	   
    	   .panel-body{
    	   }
    	   .panel-footer{
    	       padding-top: 10px;
    	       border-top: 1px solid #e0e0e0;
    	   }
    	   
    	   .panel-body>ul {
    	       list-style-type: none;
    	       padding: 0px;
    	   }
    	</style>
    </head>
    <body>
    	<div>
    		<?php echo $_REQUEST[Page::ATT_VIEW];?>
    	</div>
    </body>
</html>