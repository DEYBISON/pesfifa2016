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

// Get user stuff (do not change)
$user = JFactory::getUser();

?>

<!-- Start K2 User Layout -->

<div id="k2Container" class="userView<?php if($this->params->get('pageclass_sfx')) echo ' '.$this->params->get('pageclass_sfx'); ?>">

	<?php if($this->params->get('show_page_title') && $this->params->get('page_title')!=$this->user->name) { ?>
	<!-- Page title -->
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx')?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
	<?php } ?>


	<?php if($this->params->get('userFeedIcon',1)) { ?>
	<!-- RSS feed icon -->
	<div class="k2FeedIcon">
		<a href="<?php echo $this->feed; ?>" title="<?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?>">
			<span><?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?></span>
		</a>
		<div class="clr"></div>
	</div>
	<?php } ?>

	<?php if ($this->params->get('userImage') || $this->params->get('userName') || $this->params->get('userDescription') || $this->params->get('userURL') || $this->params->get('userEmail')) { ?>
	<div class="userBlock wow fadeIn">

		<?php if(isset($this->addLink) && JRequest::getInt('id')==$user->id) { ?>
		<!-- Item add link -->
		<span class="userItemAddLink">
			<a class="modal" rel="{handler:'iframe',size:{x:990,y:550}}" href="<?php echo $this->addLink; ?>">
				<?php echo JText::_('K2_POST_A_NEW_ITEM'); ?>
			</a>
		</span>
		<?php } ?>

		<?php if ($this->params->get('userImage') && !empty($this->user->avatar)) { ?>

		<div class="media">
			<div class="pull-left">
				<img src="<?php echo $this->user->avatar; ?>" alt="<?php echo htmlspecialchars($this->user->name, ENT_QUOTES, 'UTF-8'); ?>" style="width:<?php echo $this->params->get('userImageWidth'); ?>px; height:auto;" />
			</div>

			<div class="media-body">

				<?php } ?>

				<?php if ($this->params->get('userName')) { ?>
				<h4 class="media-heading"><?php echo $this->user->name; ?></h4>
				<?php } ?>

				<?php if ($this->params->get('userDescription') && trim($this->user->profile->description)) { ?>
				<div class="userDescription"><?php echo $this->user->profile->description; ?></div>
				<?php } ?>

				<?php if (($this->params->get('userURL') && isset($this->user->profile->url) && $this->user->profile->url) || $this->params->get('userEmail')) { ?>
				<div class="userAdditionalInfo">
					<?php if ($this->params->get('userURL') && isset($this->user->profile->url) && $this->user->profile->url) { ?>
					<span class="userURL">
						<?php echo JText::_('K2_WEBSITE_URL'); ?>: <a href="<?php echo $this->user->profile->url; ?>" target="_blank" rel="me"><?php echo $this->user->profile->url; ?></a>
					</span>
					<?php } ?>

					<?php if ($this->params->get('userEmail')) { ?>
					<span class="userEmail">
						<?php echo JText::_('K2_EMAIL'); ?>: <?php echo JHTML::_('Email.cloak', $this->user->email); ?>
					</span>
					<?php } ?>	
				</div>
				<?php } ?>

				<div class="clr"></div>

				<?php echo $this->user->event->K2UserDisplay; ?>

				<div class="clr"></div>

				<?php if ($this->params->get('userImage') && !empty($this->user->avatar)) { ?>
			</div>
		</div>
		<?php } ?>
	</div>
	<?php } ?>


	<?php if(count($this->items)) { ?>
	<!-- Item list -->
	<div class="userItemList">
		<?php foreach ($this->items as $item) { ?>
		
		<!-- Start K2 Item Layout -->
		<div class="wow fadeIn userItemView<?php if(!$item->published || ($item->publish_up != $this->nullDate && $item->publish_up > $this->now) || ($item->publish_down != $this->nullDate && $item->publish_down < $this->now)) echo ' userItemViewUnpublished'; ?><?php echo ($item->featured) ? ' userItemIsFeatured' : ''; ?>">

			<!-- Plugins: BeforeDisplay -->
			<?php echo $item->event->BeforeDisplay; ?>
			
			<!-- K2 Plugins: K2BeforeDisplay -->
			<?php echo $item->event->K2BeforeDisplay; ?>

			<?php if($this->params->get('userItemTitle')) { ?>
			<!-- Item title -->
			<h3 class="userItemTitle itemTitle">
				<?php if(isset($item->editLink)) { ?>
				<!-- Item edit link -->
				<span class="userItemEditLink">
					<a class="modal" rel="{handler:'iframe',size:{x:990,y:550}}" href="<?php echo $item->editLink; ?>">
						<?php echo JText::_('K2_EDIT_ITEM'); ?>
					</a>
				</span>
				<?php } ?>

				<?php if ($this->params->get('userItemTitleLinked') && $item->published) { ?>
				<a href="<?php echo $item->link; ?>">
					<?php echo $item->title; ?>
				</a>
				<?php } else { ?>
				<?php echo $item->title; ?>
				<?php } ?>
				<?php if(!$item->published || ($item->publish_up != $this->nullDate && $item->publish_up > $this->now) || ($item->publish_down != $this->nullDate && $item->publish_down < $this->now)) { ?>
				<span>
					<sup>
						<?php echo JText::_('K2_UNPUBLISHED'); ?>
					</sup>
				</span>
				<?php } ?>
			</h3>
			<?php } ?>

				<div class="userItemHeader itemHeader">
		  <?php if($this->params->get('userItemCategory') || $this->params->get('userItemTags')): ?>
		  <div class="userItemLinks">
				<?php if($this->params->get('userItemDateCreated')): ?>
				<!-- Date created -->
				<span class="userItemDateCreated">
					<?php echo JHTML::_('date', $item->created , JText::_('K2_DATE_FORMAT_LC2')); ?>
				</span>
				<?php endif; ?>
				<?php if($this->params->get('userItemCategory')): ?>
				<!-- Item category name -->
				<span class="userItemCategory">
					<a href="<?php echo $item->category->link; ?>"><?php echo $item->category->name; ?></a>
				</span>
				<?php endif; ?>

				<div class="clr"></div>
		  </div>
		  <?php endif; ?>  
				</div>

			<?php if($this->params->get('userItemImage') && !empty($item->imageGeneric)) { ?>
			<!-- Item Image -->
			<div class="userItemImageBlock">
				<span class="userItemImage">
					<a class="itemImage" href="<?php echo $item->link; ?>" title="<?php if(!empty($item->image_caption)) echo K2HelperUtilities::cleanHtml($item->image_caption); else echo K2HelperUtilities::cleanHtml($item->title); ?>">
						<img src="<?php echo $item->imageGeneric; ?>" alt="<?php if(!empty($item->image_caption)) echo K2HelperUtilities::cleanHtml($item->image_caption); else echo K2HelperUtilities::cleanHtml($item->title); ?>" style="width:900px; height:auto;" />
					</a>

				</span>
			</div>
			<?php } ?>

			<!-- Plugins: AfterDisplayTitle -->
			<?php echo $item->event->AfterDisplayTitle; ?>

			<!-- K2 Plugins: K2AfterDisplayTitle -->
			<?php echo $item->event->K2AfterDisplayTitle; ?>			

			<div class="itemInner">
				<div class="userItemBody">

					<!-- Plugins: BeforeDisplayContent -->
					<?php echo $item->event->BeforeDisplayContent; ?>

					<!-- K2 Plugins: K2BeforeDisplayContent -->
					<?php echo $item->event->K2BeforeDisplayContent; ?>

					<?php if($this->params->get('userItemIntroText')) { ?>
					<!-- Item introtext -->
					<div class="userItemIntroText">
						<?php echo $item->introtext; ?>
					</div>
					<?php } ?>

					<div class="clr"></div>

					<!-- Plugins: AfterDisplayContent -->
					<?php echo $item->event->AfterDisplayContent; ?>

					<!-- K2 Plugins: K2AfterDisplayContent -->
					<?php echo $item->event->K2AfterDisplayContent; ?>

					<div class="clr"></div>

					<?php if ($this->params->get('userItemReadMore')) { ?>
					<!-- Item "read more..." link -->
					<div class="userItemReadMore">
						<a class="k2ReadMore" href="<?php echo $item->link; ?>">
							<?php echo JText::_('K2_READ_MORE'); ?>
						</a>
					</div>
					<?php } ?>

				</div>

				<div class="clr"></div>

				<!-- Plugins: AfterDisplay -->
				<?php echo $item->event->AfterDisplay; ?>

				<!-- K2 Plugins: K2AfterDisplay -->
				<?php echo $item->event->K2AfterDisplay; ?>

				<div class="clr"></div>
			</div>
		</div>
		<!-- End K2 Item Layout -->

		<?php } ?>
	</div>

	<!-- Pagination -->
	<?php if(count($this->pagination->getPagesLinks())): ?>
	<div class="k2Pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
		<div class="clr"></div>
		<?php echo $this->pagination->getPagesCounter(); ?>
	</div>
	<?php endif; ?>

	<?php } ?>

</div>

<!-- End K2 User Layout -->
