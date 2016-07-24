<?php
/**
 * @version		2.6.x
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2014 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

?>

<!-- Start K2 Latest Layout -->
<div id="k2Container" class="latestView<?php if($this->params->get('pageclass_sfx')) echo ' '.$this->params->get('pageclass_sfx'); ?>">

	<?php if($this->params->get('show_page_title')) { ?>
	<!-- Page title -->
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx')?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php } ?>

	<?php foreach($this->blocks as $key=>$block) { ?>
	<?php
	$i 		= 0;
	$j 		= 0;
	$count 	= count($this->blocks);
	$cols 	= $this->params->get('latestItemsCols');

	if($i==0) echo '<div class="row-fluid">';
	$i++;
	$j++;
	?>

	<div class="latestItemsContainer">

		<?php if($this->source=='categories') { $category=$block; ?>
		
		<?php if($this->params->get('categoryFeed') || $this->params->get('categoryImage') || $this->params->get('categoryTitle') || $this->params->get('categoryDescription')) { ?>
		<!-- Start K2 Category block -->
		<div class="latestItemsCategory clearfix">
			<?php if($this->params->get('categoryFeed')) { ?>
			<!-- RSS feed icon -->
			<div class="k2FeedIcon">
				<a href="<?php echo $category->feed; ?>" title="<?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?>">
					<span><?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?></span>
				</a>
				<div class="clr"></div>
			</div>
			<?php } ?>

			<div class="clr"></div>

			<!-- K2 Plugins: K2CategoryDisplay -->
			<?php echo $category->event->K2CategoryDisplay; ?>
		</div>
		<!-- End K2 Category block -->
		<?php } ?>

		<?php } else { $user=$block; ?>

		<?php if ($this->params->get('userFeed') || $this->params->get('userImage') || $this->params->get('userName') || $this->params->get('userDescription') || $this->params->get('userURL') || $this->params->get('userEmail')) { ?>
		

		<?php if($this->params->get('userFeed')) { ?>
		<!-- RSS feed icon -->
		<div class="k2FeedIcon">
			<a href="<?php echo $user->feed; ?>" title="<?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?>">
				<span><?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?></span>
			</a>
			<div class="clr"></div>
		</div>
		<?php } ?>

		<!-- Start K2 User block -->
		<div class="latestItemsUser">

			<?php if ($this->params->get('userImage') && !empty($user->avatar)) { ?>
			<div class="media">
				<div class="pull-left">
					<img src="<?php echo $user->avatar; ?>" alt="<?php echo $user->name; ?>" style="width:<?php echo $this->params->get('userImageWidth'); ?>px;height:auto;" />
				</div>

				<div class="media-body">
					<?php } ?>

					<?php if ($this->params->get('userName')) { ?>
					<h4 class="media-heading"><a rel="author" href="<?php echo $user->link; ?>"><?php echo $user->name; ?></a></h4>
					<?php } ?>

					<?php if ($this->params->get('userDescription') && isset($user->profile->description)) { ?>
					<p class="latestItemsUserDescription"><?php echo $user->profile->description; ?></p>
					<?php } ?>

					<?php if ($this->params->get('userURL') || $this->params->get('userEmail')) { ?>
					<p class="latestItemsUserAdditionalInfo">
						<?php if ($this->params->get('userURL') && isset($user->profile->url)) { ?>
						<span class="latestItemsUserURL">
							<?php echo JText::_('K2_WEBSITE_URL'); ?>: <a rel="me" href="<?php echo $user->profile->url; ?>" target="_blank"><?php echo $user->profile->url; ?></a>
						</span>
						<?php } ?>

						<?php if ($this->params->get('userEmail')) { ?>
						<span class="latestItemsUserEmail">
							<?php echo JText::_('K2_EMAIL'); ?>: <?php echo JHTML::_('Email.cloak', $user->email); ?>
						</span>
						<?php } ?>
					</p>
					<?php } ?>

					<div class="clr"></div>

					<?php echo $user->event->K2UserDisplay; ?>

					<div class="clr"></div>

					<?php if ($this->params->get('userImage') && !empty($user->avatar)) { ?>
				</div>
			</div>
			<?php } ?>

		</div>
		<!-- End K2 User block -->
		<?php } ?>

		<?php } ?>

		<!-- Start Items list -->
		<div class="latestItemList">
			<?php if($this->params->get('latestItemsDisplayEffect')=="first") { ?>

			<?php foreach ($block->items as $itemCounter=>$item) { K2HelperUtilities::setDefaultImage($item, 'latest', $this->params); ?>
			<?php if($itemCounter==0) { ?>
			<?php $this->item=$item; echo $this->loadTemplate('item'); ?>
			<?php } else { ?>
			<h2 class="latestItemTitleList">
				<?php if ($item->params->get('latestItemTitleLinked')) { ?>
				<a href="<?php echo $item->link; ?>">
					<?php echo $item->title; ?>
				</a>
				<?php } else { ?>
				<?php echo $item->title; ?>
				<?php } ?>
			</h2>
			<?php } ?>
			<?php } ?>

			<?php } else { ?>

			<?php foreach ($block->items as $item) { K2HelperUtilities::setDefaultImage($item, 'latest', $this->params); ?>
			<?php $this->item=$item; echo $this->loadTemplate('item'); ?>
			<?php } ?>

			<?php } ?>
		</div>
		<!-- End Item list -->

	</div>

	<?php
	if( ($i==$cols) || ($j==$count) ) {
		echo '</div><!--/.row-fluid-->';
		$i = 0;
	}
	?>
	<?php } ?>
</div>
<!-- End K2 Latest Layout -->
