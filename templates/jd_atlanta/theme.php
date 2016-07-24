<?php 
	if(!empty($gantry->document->params['positiontop']->topimage)){
		 $path = json_decode($gantry->document->params['positiontop']->topimage);
		 $topbg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$topbg=(isset($gantry->document->params['positiontop']->topcolor)) ? $gantry->document->params['positiontop']->topcolor : 'transparent';
	}
	if(!empty($gantry->document->params['positionheader']->headerimage)){
		 $path = json_decode($gantry->document->params['positionheader']->headerimage);
		 $headerbg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$headerbg=(isset($gantry->document->params['positionheader']->headercolor)) ? $gantry->document->params['positionheader']->headercolor : 'transparent';
	}
	if(!empty($gantry->document->params['positionslider']->sliderimage)){
		 $path = json_decode($gantry->document->params['positionslider']->sliderimage);
		 $sliderbg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$sliderbg=(isset($gantry->document->params['positionslider']->slidercolor)) ? $gantry->document->params['positionslider']->slidercolor : 'transparent';
	}
	if(!empty($gantry->document->params['positiondrawer']->drawerimage)){
		 $path = json_decode($gantry->document->params['positiondrawer']->drawerimage);
		 $drawerbg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$drawerbg=(isset($gantry->document->params['positiondrawer']->drawercolor)) ? $gantry->document->params['positiondrawer']->drawercolor : 'transparent';
	}
	if(!empty($gantry->document->params['positionshowcase']->showcaseimage)){
		 $path = json_decode($gantry->document->params['positionshowcase']->showcaseimage);
		 $showcasebg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$showcasebg=(isset($gantry->document->params['positionshowcase']->showcasecolor)) ? $gantry->document->params['positionshowcase']->showcasecolor : 'transparent';
	}
	if(!empty($gantry->document->params['positionfeature']->featureimage)){
		 $path = json_decode($gantry->document->params['positionfeature']->featureimage);
		 $featurebg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$featurebg=(isset($gantry->document->params['positionfeature']->featurecolor)) ? $gantry->document->params['positionfeature']->featurecolor : 'transparent';
	}
	if(!empty($gantry->document->params['positionutility']->utilityimage)){
		 $path = json_decode($gantry->document->params['positionutility']->utilityimage);
		 $utilitybg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$utilitybg=(isset($gantry->document->params['positionutility']->utilitycolor)) ? $gantry->document->params['positionutility']->utilitycolor : 'transparent';
	}
	if(!empty($gantry->document->params['positionbreadcrumbs']->breadcrumbsimage)){
		 $path = json_decode($gantry->document->params['positionbreadcrumbs']->breadcrumbsimage);
		 $breadcrumbsbg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$breadcrumbsbg=(isset($gantry->document->params['positionbreadcrumbs']->breadcrumbscolor)) ? $gantry->document->params['positionbreadcrumbs']->breadcrumbscolor : 'transparent';
	}
	if(!empty($gantry->document->params['positionmaintop']->maintopimage)){
		 $path = json_decode($gantry->document->params['positionmaintop']->maintopimage);
		 $maintopbg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$maintopbg=(isset($gantry->document->params['positionmaintop']->maintopcolor)) ? $gantry->document->params['positionmaintop']->maintopcolor : 'transparent';
	}
	if(!empty($gantry->document->params['positionfullwidth']->fullwidthimage)){
		 $path = json_decode($gantry->document->params['positionfullwidth']->fullwidthimage);
		 $fullwidthbg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$fullwidthbg=(isset($gantry->document->params['positionfullwidth']->fullwidthcolor)) ? $gantry->document->params['positionfullwidth']->fullwidthcolor : 'transparent';
	}
	if(!empty($gantry->document->params['positionmainbottom']->mainbottomimage)){
		 $path = json_decode($gantry->document->params['positionmainbottom']->mainbottomimage);
		 $mainbottombg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$mainbottombg=(isset($gantry->document->params['positionmainbottom']->mainbottomcolor)) ? $gantry->document->params['positionmainbottom']->mainbottomcolor : 'transparent';
	}
	if(!empty($gantry->document->params['positionextension']->extensionimage)){
		 $path = json_decode($gantry->document->params['positionextension']->extensionimage);
		 $extensionbg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$extensionbg=(isset($gantry->document->params['positionextension']->extensioncolor)) ? $gantry->document->params['positionextension']->extensioncolor : 'transparent';
	}
	if(!empty($gantry->document->params['positionjdposition1']->jdposition1image)){
		 $path = json_decode($gantry->document->params['positionjdposition1']->jdposition1image);
		 $jdposition1bg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$jdposition1bg=(isset($gantry->document->params['positionjdposition1']->jdposition1color)) ? $gantry->document->params['positionjdposition1']->jdposition1color : 'transparent';
	}
	if(!empty($gantry->document->params['positionjdposition2']->jdposition2image)){
		 $path = json_decode($gantry->document->params['positionjdposition2']->jdposition2image);
		 $jdposition2bg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$jdposition2bg=(isset($gantry->document->params['positionjdposition2']->jdposition2color)) ? $gantry->document->params['positionjdposition2']->jdposition2color : 'transparent';
	}
	if(!empty($gantry->document->params['positionjdposition3']->jdposition3image)){
		 $path = json_decode($gantry->document->params['positionjdposition3']->jdposition3image);
		 $jdposition3bg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$jdposition3bg=(isset($gantry->document->params['positionjdposition3']->jdposition3color)) ? $gantry->document->params['positionjdposition3']->jdposition3color : 'transparent';
	}
	if(!empty($gantry->document->params['positionbottom']->bottomimage)){
		 $path = json_decode($gantry->document->params['positionbottom']->bottomimage);
		 $bottombg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$bottombg=(isset($gantry->document->params['positionbottom']->bottomcolor)) ? $gantry->document->params['positionbottom']->bottomcolor : 'transparent';
	}
	if(!empty($gantry->document->params['positionfooter']->footerimage)){
		 $path = json_decode($gantry->document->params['positionfooter']->footerimage);
		 $footerbg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$footerbg=(isset($gantry->document->params['positionfooter']->footercolor)) ? $gantry->document->params['positionfooter']->footercolor : 'transparent';
	}
	if(!empty($gantry->document->params['positioncopyright']->copyrightimage)){
		 $path = json_decode($gantry->document->params['positioncopyright']->copyrightimage);
		 $copyrightbg= (isset( $path )) ? 'url('.$path->path.')': "";		
		 }else{
		$copyrightbg=(isset($gantry->document->params['positioncopyright']->copyrightcolor)) ? $gantry->document->params['positioncopyright']->copyrightcolor : 'transparent';
	}
?>