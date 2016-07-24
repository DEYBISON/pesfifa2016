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

<!-- Start K2 Item Layout -->
<div class="latestItemView">

	<!-- Plugins: BeforeDisplay -->
	<?php echo $this->item->event->BeforeDisplay; ?>

	<!-- K2 Plugins: K2BeforeDisplay -->
	<?php echo $this->item->event->K2BeforeDisplay; ?>



	<?php if($this->item->params->get('latestItemImage') && !empty($this->item->image)) { ?>
	<!-- Item Image -->
	<div class="latestItemImageBlock">
		<span class="latestItemImage">
			<a class="itemImage" href="<?php echo $this->item->link; ?>" title="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>">
				<img src="<?php echo $this->item->image; ?>" alt="<?php if(!empty($this->item->image_caption)) echo K2HelperUtilities::cleanHtml($this->item->image_caption); else echo K2HelperUtilities::cleanHtml($this->item->title); ?>" style="width:<?php echo $this->item->imageWidth; ?>px;height:auto;" />
			</a>

			<?php if($this->item->params->get('latestItemCommentsAnchor') && ( ($this->item->params->get('comments') == '2' && !$this->user->guest) || ($this->item->params->get('comments') == '1')) ) { ?>
			<!-- Anchor link to comments below -->
			<span class="latestItemCommentsLink commentLink">
				<?php if(!empty($this->item->event->K2CommentsCounter)) { ?>
				<!-- K2 Plugins: K2CommentsCounter -->
				<?php echo $this->item->event->K2CommentsCounter; ?>
				<?php } else { ?>
				<a href="<?php echo $this->item->link; ?>#itemCommentsAnchor">
					<?php echo $this->item->numOfComments; ?>
				</a>
				<?php } ?>
			</span>
			<?php } ?>
		</span>
	</div>
	<?php } ?>


	<div class="latestItemHeader itemHeader">

		<?php if($this->item->params->get('latestItemDateCreated')) { ?>
		<!-- Date created -->
		<span class="latestItemDateCreated">
			<?php echo JHTML::_('date', $this->item->created , JText::_('DATE_FORMAT_LC3')); ?>
		</span>
		<?php } ?>

		<?php if($this->item->params->get('latestItemCategory')) { ?>
		<!-- Item category name -->
		<span class="latestItemCategory">
			<a href="<?php echo $this->item->category->link; ?>"><?php echo $this->item->category->name; ?></a>
		</span>
		<?php } ?>
	</div>

	<?php if($this->item->params->get('latestItemTitle')) { ?>
	<!-- Item title -->
	<h2 class="latestItemTitle itemTitle">
		<?php if ($this->item->params->get('latestItemTitleLinked')) { ?>
		<a href="<?php echo $this->item->link; ?>">
			<?php echo $this->item->title; ?>
		</a>
		<?php } else { ?>
		<?php echo $this->item->title; ?>
		<?php } ?>
	</h2>
	<?php } ?>

	<!-- Plugins: AfterDisplayTitle -->
	<?php echo $this->item->event->AfterDisplayTitle; ?>

	<!-- K2 Plugins: K2AfterDisplayTitle -->
	<?php echo $this->item->event->K2AfterDisplayTitle; ?>

	<div class="latestItemBody">

		<!-- Plugins: BeforeDisplayContent -->
		<?php echo $this->item->event->BeforeDisplayContent; ?>

		<!-- K2 Plugins: K2BeforeDisplayContent -->
		<?php echo $this->item->event->K2BeforeDisplayContent; ?>

		<?php if($this->item->params->get('latestItemIntroText')) { ?>
		<!-- Item introtext -->
		<div class="latestItemIntroText">
			<?php echo $this->item->introtext; ?>
		</div>
		<?php } ?>

		<div class="clr"></div>

		<!-- Plugins: AfterDisplayContent -->
		<?php echo $this->item->event->AfterDisplayContent; ?>

		<!-- K2 Plugins: K2AfterDisplayContent -->
		<?php echo $this->item->event->K2AfterDisplayContent; ?>

		<div class="clr"></div>
	</div>

	<div class="clr"></div>

	<?php if($this->params->get('latestItemVideo') && !empty($this->item->video)) { ?>
	<!-- Item video -->
	<div class="latestItemVideoBlock">
		<h3><?php echo JText::_('K2_RELATED_VIDEO'); ?></h3>
		<span class="latestItemVideo<?php if($this->item->videoType=='embedded'): ?> embedded embed-responsive embed-responsive-16by9<?php endif; ?>"><?php echo $this->item->video; ?></span>
	</div>
	<?php } ?>

	<?php if ($this->item->params->get('latestItemReadMore')) { ?>
	<!-- Item "read more..." link -->
	<div class="latestItemReadMore">
		<a class="k2ReadMore" href="<?php echo $this->item->link; ?>">
			<?php echo JText::_('K2_READ_MORE'); ?>
		</a>
	</div>
	<?php } ?>

	<div class="clr"></div>

	<!-- Plugins: AfterDisplay -->
	<?php echo $this->item->event->AfterDisplay; ?>

	<!-- K2 Plugins: K2AfterDisplay -->
	<?php echo $this->item->event->K2AfterDisplay; ?>

	<div class="clr"></div>
</div>
<!-- End K2 Item Layout -->
