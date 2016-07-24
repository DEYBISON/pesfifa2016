<?php


defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeatureBranding extends GantryFeature {
    var $_feature_name = 'branding';

	function render($position) {
	    ob_start();
	    ?>
	    <div class="rt-block">
			<a href="http://www.gantry-framework.org/" title="Gantry Framework" class="powered-by"></a>
		</div>
		<?php
	    return ob_get_clean();
	}
}