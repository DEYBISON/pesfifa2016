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

<!-- Start K2 Tag Layout -->
<div id="k2Container" class="tagView<?php if($this->params->get('pageclass_sfx')) echo ' '.$this->params->get('pageclass_sfx'); ?>">

	<?php if($this->params->get('show_page_title')) { ?>
	<!-- Page title -->
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx')?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php } ?>

	<?php if($this->params->get('tagFeedIcon',1)) { ?>
	<!-- RSS feed icon -->
	<div class="k2FeedIcon">
		<a href="<?php echo $this->feed; ?>" title="<?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?>">
			<span><?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?></span>
		</a>
		<div class="clr"></div>
	</div>
	<?php } ?>

	<?php if(count($this->items)) { ?>
	<div class="tagItemList">
		<?php foreach($this->items as $item) { ?>

		<!-- Start K2 Item Layout -->
		<div class="tagItemView wow fadeIn">

			<?php if($item->params->get('tagItemImage',1) && !empty($item->imageGeneric)) { ?>
			<!-- Item Image -->
			<div class="tagItemImageBlock">
				<span class="tagItemImage">
					<a class="itemImage" href="<?php echo $item->link; ?>" title="<?php if(!empty($item->image_caption)) echo K2HelperUtilities::cleanHtml($item->image_caption); else echo K2HelperUtilities::cleanHtml($item->title); ?>">
						<img src="<?php echo $item->imageGeneric; ?>" alt="<?php if(!empty($item->image_caption)) echo K2HelperUtilities::cleanHtml($item->image_caption); else echo K2HelperUtilities::cleanHtml($item->title); ?>" style="width:<?php echo $item->params->get('itemImageGeneric'); ?>px; height:auto;" />
					</a>
				</span>
			</div>
			<?php } ?>


			<?php if($item->params->get('tagItemTitle',1)) { ?>
			<!-- Item title -->
			<h3 class="tagItemTitle itemTitle">
				<?php if ($item->params->get('tagItemTitleLinked',1)) { ?>
				<a href="<?php echo $item->link; ?>">
					<?php echo $item->title; ?>
				</a>
				<?php } else { ?>
				<?php echo $item->title; ?>
				<?php } ?>
			</h3>
			<?php } ?>			

			<div class="itemInner">
				<div class="tagItemHeader itemHeader">

					<?php if($item->params->get('tagItemDateCreated',1)) { ?>
					<!-- Date created -->
					<span class="tagItemDateCreated">
						<i class="icon-time"></i>  <?php echo JHTML::_('date', $item->created , JText::_('DATE_FORMAT_LC3')); ?>
					</span>
					<?php } ?>

					<?php if($item->params->get('tagItemCategory')) { ?>
					<!-- Item category name -->
					<span class="tagItemCategory">
						<i class="icon-folder-close"></i>  <a href="<?php echo $item->category->link; ?>"><?php echo $item->category->name; ?></a>
					</span>
					<?php } ?>

				</div>

				<div class="clr"></div>


				<div class="tagItemBody">
					<?php if($item->params->get('tagItemIntroText',1)) { ?>
					<!-- Item introtext -->
					<div class="tagItemIntroText">
						<?php echo $item->introtext; ?>
					</div>
					<?php } ?>

					<div class="clr"></div>

					<?php if ($item->params->get('tagItemReadMore')) { ?>
					<!-- Item "read more..." link -->
					<div class="tagItemReadMore">
						<a class="k2ReadMore" href="<?php echo $item->link; ?>">
							<?php echo JText::_('K2_READ_MORE'); ?>
						</a>
					</div>
					<?php } ?>

				</div>


				<?php if($item->params->get('tagItemExtraFields',0) && count($item->extra_fields)) { ?>
				<!-- Item extra fields -->  
				<div class="tagItemExtraFields">
					<h4><?php echo JText::_('K2_ADDITIONAL_INFO'); ?></h4>
					<ul>
						<?php foreach ($item->extra_fields as $key=>$extraField) { ?>
						<?php if($extraField->value != '') { ?>
						<li class="<?php echo ($key%2) ? "odd" : "even"; ?> type<?php echo ucfirst($extraField->type); ?> group<?php echo $extraField->group; ?>">
							<?php if($extraField->type == 'header') { ?>
							<h4 class="tagItemExtraFieldsHeader"><?php echo $extraField->name; ?></h4>
							<?php } else { ?>
							<span class="tagItemExtraFieldsLabel"><?php echo $extraField->name; ?></span>
							<span class="tagItemExtraFieldsValue"><?php echo $extraField->value; ?></span>
							<?php } ?>
						</li>
						<?php } ?>
						<?php } ?>
					</ul>
					<div class="clr"></div>
				</div>
				<?php } ?>


				<div class="clr"></div>
			</div>
		</div>
		<!-- End K2 Item Layout -->

		<?php } ?>
	</div>

	<!-- Pagination -->
	<?php if($this->pagination->getPagesLinks()) { ?>
	<div class="pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<?php } ?>

	<?php } ?>

</div>
<!-- End K2 Tag Layout -->
