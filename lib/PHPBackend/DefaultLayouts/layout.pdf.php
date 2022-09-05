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
    	       text-align: left;
    	   }
    	   
    	   h1, h2 {
    	       font-weight: normal;
    	       padding: 5px;
    	       border-bottom: 1px solid #2f2f2f;
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
    	<p style="border-top: 1px solid #2f2f2f;">
    		<small>Générer par PHPBackend, une initialitve de l'Ir <a style="text-decoration: none; color: #000030;" href="mailto:esaiemuhasa.dev@gmail.com">Esaie MUHASA</a></small>
    	</p>
    </body>
</html>