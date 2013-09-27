<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$this->language = $doc->language;


$itemid   = $app->input->getCmd('Itemid', '');

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" >
<head>
	<title><?php echo $this->title; ?> <?php echo $this->error->getMessage();?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="icon" type="image/png" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/img/favicon_32x32.ico">
	 
     <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/normalize.min.css" type="text/css" />
     <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/main.css" type="text/css" />

	
</head>
<body class="<?php echo ($itemid ? ' bgid-' . $itemid : '')?>">
    
    <header>
            <div class="inner">
                <div id="logo"><a href="<?php echo $this->baseurl ?>"><img src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/img/logo.jpg" alt="Servicios sin limites" /></a></div>
                <div id="idiomas">
                    <jdoc:include type="modules" name="idiomas" style="none" />
                </div>
                <nav id="menu">
                   <?php if (JModuleHelper::getModule('menu')) : ?>
                        <?php
                                        $module = JModuleHelper::getModule('menu');
                                        echo JModuleHelper::renderModule($module);
                                    ?>
                        <?php endif; ?>
                </nav>
            </div>
            
        </header>
        <section id="banner">
            <jdoc:include type="modules" name="banner" style="none" />
            
        </section>
        <section id="content">
            <div class="inner">
                <div class="text404">
                            <h1 class="page-header"><?php echo JText::_('JERROR_LAYOUT_PAGE_NOT_FOUND'); ?></h1>
                                <div class="well">
                                <div class="row-fluid">
                                    <div class="span6">
                                        <p><strong><?php echo JText::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST'); ?></strong></p>
                                        <p><?php echo JText::_('JERROR_LAYOUT_NOT_ABLE_TO_VISIT'); ?></p>
                                        <ul>
                                            <li><?php echo JText::_('JERROR_LAYOUT_AN_OUT_OF_DATE_BOOKMARK_FAVOURITE'); ?></li>
                                            <li><?php echo JText::_('JERROR_LAYOUT_MIS_TYPED_ADDRESS'); ?></li>
                                            <li><?php echo JText::_('JERROR_LAYOUT_SEARCH_ENGINE_OUT_OF_DATE_LISTING'); ?></li>
                                            <li><?php echo JText::_('JERROR_LAYOUT_YOU_HAVE_NO_ACCESS_TO_THIS_PAGE'); ?></li>
                                        </ul>
                                    </div>
                                    <div class="span6">
                                        <?php if (JModuleHelper::getModule('search')) : ?>
                                            <p><strong><?php echo JText::_('JERROR_LAYOUT_SEARCH'); ?></strong></p>
                                            <p><?php echo JText::_('JERROR_LAYOUT_SEARCH_PAGE'); ?></p>
                                            <?php
                                                $module = JModuleHelper::getModule('search');
                                                echo JModuleHelper::renderModule($module);
                                            ?>
                                        <?php endif; ?>
                                        <p><?php echo JText::_('JERROR_LAYOUT_GO_TO_THE_HOME_PAGE'); ?></p>
                                        <p><a href="<?php echo $this->baseurl; ?>/index.php" class="btn"><i class="icon-home"></i> <?php echo JText::_('JERROR_LAYOUT_HOME_PAGE'); ?></a></p>
                                    </div>
                                </div>
                                <hr />
                                <p><?php echo JText::_('JERROR_LAYOUT_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR'); ?></p>
                                <blockquote>
                                    <span class="label label-inverse"><?php echo $this->error->getCode(); ?></span> <?php echo $this->error->getMessage();?>
                                </blockquote>
                            </div>

                       </div>
                
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
