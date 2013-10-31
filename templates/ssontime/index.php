<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.3monkiescr
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;



$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$this->language = $doc->language;


$itemid   = $app->input->getCmd('Itemid', '');

// Add JavaScript Frameworks
//JHtml::_('bootstrap.framework');

// Add Stylesheets
$doc->addStyleSheet('templates/'.$this->template.'/css/normalize.min.css');
$doc->addStyleSheet('templates/'.$this->template.'/css/main.css');



?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" >
<head>
	
	<jdoc:include type="head" />
    <link rel="icon" type="image/png" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/img/favicon_32x32.ico">
	<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>
    
 <?php if ($itemid == 112 || $itemid == 107):?>
        <style type="text/css">
           
            #content .item-page{
                border: none;
                background: transparent;
                padding: 0;
            }
        </style>
    <?php endif; ?>
     <script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/js/vendor/modernizr-2.6.2.min.js"></script>
</head>

<body class="<?php echo ($itemid ? ' bgid-' . $itemid : '')?>">
	
	<header>
            <div class="inner">
                <div id="logo"><a href="<?php echo $this->baseurl ?>"><img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/img/logo.png" alt="Servicios sin limites" /></a></div>
                <div id="idiomas">
                    <jdoc:include type="modules" name="idiomas" style="none" />
                </div>
                <nav id="menu">
                   <jdoc:include type="modules" name="menu" style="none" />
                </nav>
            </div>
            
        </header>
        <section id="banner">
            <jdoc:include type="modules" name="banner" style="none" />
            
        </section>
         <?php if ($this->countModules('promociones')) : ?>
             <div id="promociones">
                <jdoc:include type="modules" name="promociones" style="none" />
            </div>
            <?php endif; ?>
        <section id="content">
            <div class="inner">
                 <jdoc:include type="component" />
                
            </div>
        </section>
        <footer>
            <div class="inner">
                <nav id="menu-footer">
                    <jdoc:include type="modules" name="menu_footer" style="none" />
                </nav>
                <nav id="redes">
                    <jdoc:include type="modules" name="redes" style="none" />
                 </div>
                <div id="copyright">
                    <p>Copyright @ 2013</p>
                </div>
            </div>
        </footer>


       
        <script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/js/vendor/jquery-1.10.1.min.js"></script>
        <script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/js/vendor/jquery.cycle.all.js"></script>
        <script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/js/main.js"></script>

        <script>
            /*var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
            (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src='//www.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g,s)}(document,'script'));*/
        </script>


    

	<jdoc:include type="modules" name="debug" style="none" />
</body>
</html>
