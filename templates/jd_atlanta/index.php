<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );

// load and inititialize gantry class
require_once(dirname(__FILE__) . '/lib/gantry/gantry.php');
$gantry->init();

// get the current preset
$gpreset = str_replace(' ','',strtolower($gantry->get('name')));

?>
<!doctype html>
<html xml:lang="<?php echo $gantry->language; ?>" lang="<?php echo $gantry->language;?>" >
<head>
	<?php if ($gantry->get('layout-mode') == '960fixed') : ?>
	<meta name="viewport" content="width=960px, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<?php elseif ($gantry->get('layout-mode') == '1200fixed') : ?>
	<meta name="viewport" content="width=1200px, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<?php else : ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php endif; ?>
<?php if ($gantry->browser->name == 'ie') : ?>
	<meta content="IE=edge" http-equiv="X-UA-Compatible" />
<?php endif; ?>	
    <?php
        $gantry->displayHead();
		$gantry->addStyle('grid-responsive.css', 5);
		$gantry->addLess('bootstrap.less', 'bootstrap.css', 6);
        if ($gantry->browser->name == 'ie'){
        	if ($gantry->browser->shortversion == 9){
        		$gantry->addInlineScript("if (typeof RokMediaQueries !== 'undefined') window.addEvent('domready', function(){ RokMediaQueries._fireEvent(RokMediaQueries.getQuery()); });");
        	}
			if ($gantry->browser->shortversion == 8){
				$gantry->addScript('html5shim.js');
			}
		}
		if ($gantry->get('layout-mode', 'responsive') == 'responsive') $gantry->addScript('rokmediaqueries.js');
		if ($gantry->get('loadtransition')) {
		$gantry->addScript('load-transition.js');
		$hidden = ' class="rt-hidden"';}

    ?>

<?php require_once(dirname(__FILE__) . '/theme.php');	
$app = JFactory::getApplication();
$templateName = $app->getTemplate();
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="templates/<?php echo $templateName; ?>/less/kunena.css">
<link rel="stylesheet" type="text/css" href="templates/<?php echo $templateName; ?>/less/virtuemart.css">
<link rel="stylesheet" type="text/css" href="templates/<?php echo $templateName; ?>/less/hikashop.css">
<link rel="stylesheet" type="text/css" href="templates/<?php echo $templateName; ?>/less/k2.css">
<link rel="stylesheet" type="text/css" href="templates/<?php echo $templateName ;?>/less/jevents.css">
<link rel="stylesheet" type="text/css" href="templates/<?php echo $templateName; ?>/less/responsive.css">

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>

<script>
jQuery(document).ready(function(){
jQuery(window).scroll(function() {    
    var scroll = jQuery(window).scrollTop();
    if (scroll >= 60) {
        jQuery(".theader").addClass("scroll");
    }
	if (scroll <= 60) {
        jQuery(".theader").removeClass("scroll");
    }
});
})
</script>

<script>
	jQuery("#rt-scroll").hide();
	jQuery("#rt-scroll").click(function () {
		jQuery("body, html").animate({scrollTop: 0}, 800);
		return false;
	});
	jQuery(window).scroll(function(){
		if(jQuery(window).scrollTop() > 0){
			jQuery("#rt-scroll").fadeIn('slow');
		}else{
			if(jQuery("#rt-scroll").is(':visible')){
				jQuery("#rt-scroll").fadeOut('slow');
			}
		}
	});
</script>
<body <?php echo $gantry->displayBodyTag(); ?>>
    <?php /** Begin Top Surround **/ if ($gantry->countModules('top') or $gantry->countModules('header')) : ?>
    <header id="rt-top-surround">
		<?php /** Begin Top **/ if ($gantry->countModules('top')) : ?>
		<?php
		if($topbg!= 'transparent') { ?>
			<div id="rt-top" style="background:<?php echo $topbg;?> no-repeat center top; background-size:cover;">
			<?php } else{ ?>
			<div id="rt-top" <?php echo $gantry->displayClassesByTag('rt-top'); ?> class="ttop">	
		<?php } ?>
			<div class="rt-container">
				<?php echo $gantry->displayModules('top','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Top **/ endif; ?>
		<?php /** Begin Header **/ if ($gantry->countModules('header')) : ?>
		<?php
		if($headerbg!= 'transparent') { ?>
			<div id="rt-header" style="background:<?php echo $headerbg;?> no-repeat center top; background-size:cover;">
			<?php } else{ ?>
			<div id="rt-header" class="theader">	
		<?php } ?>
			<div class="rt-container">
				<?php echo $gantry->displayModules('header','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Header **/ endif; ?>
	</header>
	<?php /** End Top Surround **/ endif; ?>
	
	<?php /** Begin Slider **/ if ($gantry->countModules('slider')) : ?>
	<?php
		if($sliderbg!= 'transparent') { ?>
			<div id="rt-slider" style="background:<?php echo $sliderbg;?> no-repeat center top; background-size:cover;">
			<?php } else{ ?>
			<div id="rt-slider">	
	<?php } ?>
        <div class="rt-container">
            <?php echo $gantry->displayModules('slider','standard','standard'); ?>
            <div class="clear"></div>
        </div>
    </div>
    <?php /** End Slider **/ endif; ?>

	<?php /** Begin subscribe **/ if ($gantry->countModules('subscribe')) : ?>
		<div id="rt-subscribe">
			<div class="rt-container">
				<?php echo $gantry->displayModules('subscribe','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
	<?php /** End subscribe **/ endif; ?>	

	<?php /** Begin Drawer **/ if ($gantry->countModules('drawer')) : ?>
	<?php
		if($drawerbg!= 'transparent') { ?>
			<div id="rt-drawer" style="background:<?php echo $drawerbg;?> no-repeat center top; background-size:cover;">
			<?php } else{ ?>
			<div id="rt-drawer">	
	<?php } ?>
        <div class="rt-container">
            <?php echo $gantry->displayModules('drawer','standard','standard'); ?>
            <div class="clear"></div>
        </div>
    </div>
    <?php /** End Drawer **/ endif; ?>
	<?php /** Begin Showcase **/ if ($gantry->countModules('showcase')) : ?>
	<?php
		if($showcasebg!= 'transparent') { ?>
			<div id="rt-showcase" style="background:<?php echo $showcasebg;?> no-repeat center top; background-size:cover;">
			<?php } else{ ?>
			<div id="rt-showcase" class="rt-showcase-pattern">	
	<?php } ?>
			<div class="rt-container">
				<?php echo $gantry->displayModules('showcase','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	<?php /** End Showcase **/ endif; ?>
	<div id="rt-transition"<?php if ($gantry->get('loadtransition')) echo $hidden; ?>>
		<div id="rt-mainbody-surround">
			<?php /** Begin Feature **/ if ($gantry->countModules('feature')) : ?>
		<?php
			if($featurebg!= 'transparent') { ?>
			<div id="rt-feature" style="background:<?php echo $featurebg;?> no-repeat center top; background-size:cover;">
			<?php } else{ ?>
			<div id="rt-feature">	
		<?php } ?>
				<div class="rt-container">
					<?php echo $gantry->displayModules('feature','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Feature **/ endif; ?>
			<?php /** Begin Utility **/ if ($gantry->countModules('utility')) : ?>
		<?php
			if($utilitybg!= 'transparent') { ?>
			<div id="rt-utility" style="background:<?php echo $utilitybg;?> no-repeat center top; background-size:cover;">
			<?php } else{ ?>
			<div id="rt-utility">	
		<?php } ?>
				<div class="rt-container">
					<?php echo $gantry->displayModules('utility','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Utility **/ endif; ?>
			<?php /** Begin Breadcrumbs **/ if ($gantry->countModules('breadcrumb')) : ?>
		<?php
			if($breadcrumbsbg!= 'transparent') { ?>
			<div id="rt-breadcrumbs" style="background:<?php echo $breadcrumbsbg;?> no-repeat center top; background-size:cover;">
			<?php } else{ ?>
			<div id="rt-breadcrumbs">	
		<?php } ?>
				<div class="rt-container">
					<?php echo $gantry->displayModules('breadcrumb','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Breadcrumbs **/ endif; ?>
			<?php /** Begin Main Top **/ if ($gantry->countModules('maintop')) : ?>
			<?php
			if($maintopbg!= 'transparent') { ?>
			<div id="rt-maintop" style="background:<?php echo $maintopbg;?> no-repeat center top; background-size:cover;">
			<?php } else{ ?>
			<div id="rt-maintop">	
		<?php } ?>
				<div class="rt-container">
					<?php echo $gantry->displayModules('maintop','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Main Top **/ endif; ?>
			<?php /** Begin Full Width**/ if ($gantry->countModules('fullwidth')) : ?>
			<?php
			if($fullwidthbg!= 'transparent') { ?>
			<div id="rt-fullwidth" style="background:<?php echo $fullwidthbg;?> no-repeat center top; background-size:cover;">
			<?php } else{ ?>
			<div id="rt-fullwidth">	
			<?php } ?>
				<?php echo $gantry->displayModules('fullwidth','basic','basic'); ?>
					<div class="clear"></div>
				</div>
			<?php /** End Full Width **/ endif; ?>
			<?php /** Begin Main Bottom **/ if ($gantry->countModules('mainbottom')) : ?>
			<?php
			if($mainbottombg!= 'transparent') { ?>
			<div id="rt-mainbottom" style="background:<?php echo $mainbottombg;?> no-repeat center top; background-size:cover;">
			<?php } else{ ?>
			<div id="rt-mainbottom">	
			<?php } ?>
				<div class="rt-container">
					<?php echo $gantry->displayModules('mainbottom','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Main Bottom **/ endif; ?>
			<?php /** Begin Extension **/ if ($gantry->countModules('extension')) : ?>
			<?php
			if($extensionbg!= 'transparent') { ?>
			<div id="rt-extension" style="background:<?php echo $extensionbg;?> no-repeat center top; background-size:cover;">
			<?php } else{ ?>
			<div id="rt-extension">	
			<?php } ?>
				<div class="rt-container">
					<?php echo $gantry->displayModules('extension','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Extension **/ endif; ?>
		</div>
	</div>
	
	<?php /** Begin jd-position1 **/ if ($gantry->countModules('jd-position1')) : ?>
	<?php
		if($jdposition1bg!= 'transparent') { ?>
		<div id="jd-position1" style="background:<?php echo $jdposition1bg;?> no-repeat center top; background-size:cover;">
		<?php } else{ ?>
		<div id="jd-position1">	
	<?php } ?>
		<div class="rt-container">
			<?php echo $gantry->displayModules('jd-position1','standard','standard'); ?>
			<div class="clear"></div>
		</div>
	</div>
	<?php /** End jd-position1 **/ endif; ?>

	<?php /** Begin jd-position2 **/ if ($gantry->countModules('jd-position2')) : ?>
	<?php
		if($jdposition2bg!= 'transparent') { ?>
		<div id="jd-position2" style="background:<?php echo $jdposition2bg;?> no-repeat center top; background-size:cover;">
		<?php } else{ ?>
		<div id="jd-position2">	
	<?php } ?>
		<div class="rt-container">
			<?php echo $gantry->displayModules('jd-position2','standard','standard'); ?>
			<div class="clear"></div>
		</div>
	</div>
	<?php /** End jd-position2 **/ endif; ?>
	<?php /** Begin Main Body **/ ?>
	<div id="content-col">
		<div class="rt-container">
			<?php echo $gantry->displayMainbody('mainbody','sidebar','standard','standard','standard','standard','standard'); ?>
		</div>
	</div>
	<?php /** End Main Body **/ ?>

	<?php /** Begin jd-position3 **/ if ($gantry->countModules('jd-position3')) : ?>
	<?php
		if($jdposition3bg!= 'transparent') { ?>
		<div id="jd-position3" style="background:<?php echo $jdposition3bg;?> no-repeat center top; background-size:cover;">
		<?php } else{ ?>
		<div id="jd-position3">	
	<?php } ?>
		<div class="rt-container">
			<?php echo $gantry->displayModules('jd-position3','standard','standard'); ?>
			<div class="clear"></div>
		</div>
	</div>
	<?php /** End jd-position3 **/ endif; ?>
	
	<?php /** Begin Bottom **/ if ($gantry->countModules('bottom')) : ?>
	<?php
		if($bottombg!= 'transparent') { ?>
		<div id="rt-bottom" style="background:<?php echo $bottombg;?> no-repeat center top; background-size:cover;">
		<?php } else{ ?>
		<div id="rt-bottom">	
	<?php } ?>
		<div class="rt-container">
			<?php echo $gantry->displayModules('bottom','standard','standard'); ?>
			<div class="clear"></div>
		</div>
	</div>
	<?php /** End Bottom **/ endif; ?>
	
	<?php /** Begin Footer Section **/ if ($gantry->countModules('footer') or $gantry->countModules('copyright')) : ?>
	<footer id="rt-footer-surround">
		<?php /** Begin Footer **/ if ($gantry->countModules('footer')) : ?>
		<?php
		if($footerbg!= 'transparent') { ?>
		<div id="rt-footer" style="background:<?php echo $footerbg;?> no-repeat center top; background-size:cover;">
		<?php } else{ ?>
		<div id="rt-footer">	
		<?php } ?>
			<div class="rt-container">
				<?php echo $gantry->displayModules('footer','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Footer **/ endif; ?>
		<?php /** Begin Copyright **/ if ($gantry->countModules('copyright')) : ?>
		<?php
		if($copyrightbg!= 'transparent') { ?>
		<div id="rt-copyright" style="background:<?php echo $copyrightbg;?> no-repeat center top; background-size:cover;">
		<?php } else{ ?>
		<div id="rt-copyright">	
		<?php } ?>
			<div class="rt-container">
				<?php echo $gantry->displayModules('copyright','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<?php /** End Copyright **/ endif; ?>
	</footer>
	<?php /** End Footer Surround **/ endif; ?>
	<div id="jd-copyright">
		<div class="rt-container">
			<div class="rt-block">
				<p>&copy; Copyright <?php echo date('Y'); ?> <strong style="text-transform:capitalize;"><?php $config = JFactory::getConfig(); echo $config['sitename']; ?></strong><a href="https://www.joomdev.com/products/templates" target="_blank"> Joomla Template</a> by <a href="http://www.joomdev.com" target="_blank">JoomDev</a></p>
			</div>
		</div>
	</div>
	
	<?php /** Begin scroll **/ if ($gantry->countModules('scroll')) : ?>
		<div id="rt-scroll">
			<div class="rt-container">
				<?php echo $gantry->displayModules('scroll','standard','standard'); ?>
				<div class="clear"></div>
			</div>
		</div>
	<?php /** End scroll **/ endif; ?>	
	
	<?php /** Begin Debug **/ if ($gantry->countModules('debug')) : ?>
	<div id="rt-debug">
		<div class="rt-container">
			<?php echo $gantry->displayModules('debug','standard','standard'); ?>
			<div class="clear"></div>
		</div>
	</div>
	<?php /** End Debug **/ endif; ?>
	<?php /** Begin Analytics **/ if ($gantry->countModules('analytics')) : ?>
	<?php echo $gantry->displayModules('analytics','basic','basic'); ?>
	<?php /** End Analytics **/ endif; ?>
	
	</body>
</html>
<?php
$gantry->finalize();
?>
