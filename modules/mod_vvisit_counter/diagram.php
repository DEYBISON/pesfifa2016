<?php

// LT Diagram Builder 3.4 - PHP version using the gd extension
// Copyright (c) 2001-2006 Lutz Tautenhahn, all rights reserved.
//
// The Author grants you a non-exclusive, royalty free, license to use,
// modify and redistribute this software, provided that this copyright notice
// and license appear on all copies of the software.
// This software is provided "as is", without a warranty of any kind.

// no direct access
defined('_JEXEC') or die('Restricted access');

function UTC($yy, $mm, $dd, $hh, $nn, $ss)
{ return(mktime($hh, $nn, $ss, $mm, $dd, $yy));
}

function GetRGB($ss,&$rr,&$gg,&$bb)
{ $tt="000000";
  if (strlen($ss)==6) $tt=$ss;
  if (strlen($ss)==7) $tt=substr($ss, 1);
  $rr=intval(substr($tt, 0, 2),16);
  $gg=intval(substr($tt, 2, 2),16);
  $bb=intval(substr($tt, 4, 2),16);
}

function sign($rr)
{ if ($rr<0) return(-1); else return(1);
}

function DateFormat($vv, $ii, $ttype)
{ $Month=array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
  $Weekday=array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");
  if ($ii>15*24*60*60)
  { if ($ii<365*24*60*60)
    { $vv_date=$vv+15*24*60*60;
      $yy=strftime("%y",$vv_date);
      $mm=strftime("%m",$vv_date);
      if ($ttype==5) ;//You can add your own date format here
      if ($ttype==4) return($Month[intval($mm)-1]);
      if ($ttype==3) return($Month[intval($mm)-1]." ".$yy);
      return($mm."/".$yy);
    }
    $vv_date=$vv+183*24*60*60;
    $yy=strftime("%Y",$vv_date);
    return($yy);
  }
  $vv_date=$vv;
  $yy=strftime("%Y",$vv_date);
  $mm=strftime("%m",$vv_date);
  $dd=strftime("%d",$vv_date);
  $ww=strftime("%w",$vv_date);
  $hh=strftime("%H",$vv_date);
  $nn=strftime("%M",$vv_date);
  $ss=strftime("%S",$vv_date);
  if ($ii>=86400)//1 day
  { if ($ttype==5) ;//You can add your own date format here
    if ($ttype==4) return($Weekday[intval($ww)]);
    if ($ttype==3) return($mm."/".$dd);
    return($dd.".".$mm.".");
  }
  if ($ii>=21600)//6 hours
  { if ($hh==0)
    { if ($ttype==5) ;//You can add your own date format here
      if ($ttype==4) return($Weekday[intval($ww)]);
      if ($ttype==3) return($mm."/".$dd);
      return($dd.".".$mm.".");
    }
    else
    { if ($ttype==5) ;//You can add your own date format here
      if ($ttype==4) return(($hh<=12) ? $hh."am" : intval($hh)%12 ."pm");
      if ($ttype==3) return(($hh<=12) ? $hh."am" : intval($hh)%12 ."pm");
      return($hh.":00");
    }
  }
  if ($ii>=60)//1 min
  { if ($ttype==5) ;//You can add your own date format here
    if ($ttype==4) return((intval($hh)<=12) ? $hh.".".$nn."am" : intval($hh)%12 .".".$nn."pm");
    if ($nn=="00") $nn="";
    else $nn=":".$nn;
    if ($ttype==3) return((intval($hh)<=12) ? $hh.$nn."am" : intval($hh)%12 .$nn."pm");
    if ($nn=="") $nn=":00";
    return($hh.$nn);
  }
  return($nn.":".$ss);
}

class Diagram
{ var $left, $right, $top, $bottom;
  var $xmin, $xmax, $ymin, $ymax;
  var $xtext="";
  var $ytext="";
  var $title="";
  var $XScale=1;
  var $YScale=1;
  var $XScalePosition="bottom";
  var $YScalePosition="left";
  var $logsub=array(0.301, 0.477, 0.602, 0.699, 0.778, 0.845, 0.903, 0.954);
  var $XGrid=array(0,0,0);
  var $YGrid=array(0,0,0);
  var $XGridDelta=0;
  var $YGridDelta=0;
  var $XSubGrids=0;
  var $YSubGrids=0;
  var $SubGrids=0;
  var $XGridColor="";
  var $YGridColor="";
  var $XSubGridColor="";
  var $YSubGridColor="";
  var $Font=4;
  var $BFont=5;
  var $MaxGrids;
  var $LastColor="";
  var $LastImgColorIndex=0;
  var $ImgMapData="";
  var $Img;

  function GetImgColorIndex($theColor)
  { if ($theColor==$this->LastColor) return($this->LastImgColorIndex);
    GetRGB($theColor,$rr,$gg,$bb);
    $cc=imagecolorexact($this->Img,$rr,$gg,$bb);
    if ($cc<0) $cc = imagecolorallocate($this->Img, $rr, $gg, $bb);
    if ($cc<0) $cc = imagecolorclosest($this->Img, $rr, $gg, $bb);
    $this->LastColor=$theColor;
    $this->LastImgColorIndex=$cc;
    return($cc);
  }

  function SetFrame($theLeft, $theTop, $theRight, $theBottom)
  { $this->left   = $theLeft;
    $this->right  = $theRight;
    $this->top    = $theTop;
    $this->bottom = $theBottom;
  }

  function SetBorder($theLeftX, $theRightX, $theBottomY, $theTopY)
  { $this->xmin = $theLeftX;
    $this->xmax = $theRightX;
    $this->ymin = $theBottomY;
    $this->ymax = $theTopY;
  }

  function SetText($theScaleX, $theScaleY, $theTitle)
  { $this->xtext=$theScaleX;
    $this->ytext=$theScaleY;
    $this->title=$theTitle;
  }

  function SetGridColor($theGridColor, $theSubGridColor="undefined")
  { $this->XGridColor=$theGridColor;
    $this->YGridColor=$theGridColor;
    if ($theSubGridColor!="undefined")
    { $this->XSubGridColor=$theSubGridColor;
      $this->YSubGridColor=$theSubGridColor;
    }
  }

  function SetXGridColor($theGridColor, $theSubGridColor="undefined")
  { $this->XGridColor=$theGridColor;
    if ($theSubGridColor!="undefined") $this->XSubGridColor=$theSubGridColor;
  }

  function SetYGridColor($theGridColor, $theSubGridColor="undefined")
  { $this->YGridColor=$theGridColor;
    if ($theSubGridColor!="undefined") $this->YSubGridColor=$theSubGridColor;
  }

  function ScreenX($theRealX)
  { return(round(($theRealX-$this->xmin)/($this->xmax-$this->xmin)*($this->right-$this->left)+$this->left));
  }

  function ScreenY($theRealY)
  { return(round(($this->ymax-$theRealY)/($this->ymax-$this->ymin)*($this->bottom-$this->top)+$this->top));
  }

  function RealX($theScreenX)
  { return($this->xmin+($this->xmax-$this->xmin)*($theScreenX-$this->left)/($this->right-$this->left));
  }

  function RealY($theScreenY)
  { return($this->ymax-($this->ymax-$this->ymin)*($theScreenY-$this->top)/($this->bottom-$this->top));
  }

  function DateInterval($vv)
  { $bb=140*24*60*60; //140 days
    $this->SubGrids=4;
    if ($vv>=$bb) //140 days < 5 months
    { $bb=8766*60*60;//1 year
      if ($vv<$bb) //1 year
        return($bb/12); //1 month
      if ($vv<$bb*2) //2 years
        return($bb/6); //2 month
      if ($vv<$bb*5/2) //2.5 years
      { $this->SubGrids=6; return($bb/4); } //3 month
      if ($vv<$bb*5) //5 years
      { $this->SubGrids=6; return($bb/2); } //6 month
      if ($vv<$bb*10) //10 years
        return($bb); //1 year
      if ($vv<$bb*20) //20 years
        return($bb*2); //2 years
      if ($vv<$bb*50) //50 years
      { $this->SubGrids=5; return($bb*5); } //5 years
      if ($vv<$bb*100) //100 years
      { $this->SubGrids=5; return($bb*10); } //10 years
      if ($vv<$bb*200) //200 years
        return($bb*20); //20 years
      if ($vv<$bb*500) //500 years
      { $this->SubGrids=5; return($bb*50); } //50 years
      $this->SubGrids=5; return($bb*100); //100 years
    }
    $bb/=2; //70 days
    if ($vv>=$bb) { $this->SubGrids=7; return($bb/5); } //14 days
    $bb/=2; //35 days
    if ($vv>=$bb) { $this->SubGrids=7; return($bb/5); } //7 days
    $bb/=7; $bb*=4; //20 days
    if ($vv>=$bb) return($bb/5); //4 days
    $bb/=2; //10 days
    if ($vv>=$bb) return($bb/5); //2 days
    $bb/=2; //5 days
    if ($vv>=$bb) return($bb/5); //1 day
    $bb/=2; //2.5 days
    if ($vv>=$bb) return($bb/5); //12 hours
    $bb*=3; $bb/=5; //1.5 day
    if ($vv>=$bb) { $this->SubGrids=6; return($bb/6); } //6 hours
    $bb/=2; //18 hours
    if ($vv>=$bb) { $this->SubGrids=6; return($bb/6); } //3 hours
    $bb*=2; $bb/=3; //12 hours
    if ($vv>=$bb) return($bb/6); //2 hours
    $bb/=2; //6 hours
    if ($vv>=$bb) return($bb/6); //1 hour
    $bb/=2; //3 hours
    if ($vv>=$bb) { $this->SubGrids=6; return($bb/6); } //30 mins
    $bb/=2; //1.5 hours
    if ($vv>=$bb) { $this->SubGrids=5; return($bb/6); } //15 mins
    $bb*=2; $bb/=3; //1 hour
    if ($vv>=$bb) { $this->SubGrids=5; return($bb/6); } //10 mins
    $bb/=3; //20 mins
    if ($vv>=$bb) { $this->SubGrids=5; return($bb/4); } //5 mins
    $bb/=2; //10 mins
    if ($vv>=$bb) return($bb/5); //2 mins
    $bb/=2; //5 mins
    if ($vv>=$bb) return($bb/5); //1 min
    $bb*=3; $bb/=2; //3 mins
    if ($vv>=$bb) { $this->SubGrids=6; return($bb/6); } //30 secs
    $bb/=2; //1.5 mins
    if ($vv>=$bb) { $this->SubGrids=5; return($bb/6); } //15 secs
    $bb*=2; $bb/=3; //1 min
    if ($vv>=$bb) { $this->SubGrids=5; return($bb/6); } //10 secs
    $bb/=3; //20 secs
    if ($vv>=$bb) { $this->SubGrids=5; return($bb/4); } //5 secs
    $bb/=2; //10 secs
    if ($vv>=$bb) return($bb/5); //2 secs
    return($bb/10); //1 sec
  }

  function GetXGrid()
  { $dx=($this->xmax-$this->xmin);
    if (abs($dx)>0)
    { $invdifx=($this->right-$this->left)/($this->xmax-$this->xmin);
      if (($this->XScale==1)||(is_string($this->XScale)))
      { $r=1;
        while (abs($dx)>=100) { $dx/=10; $r*=10; }
        while (abs($dx)<10) { $dx*=10; $r/=10; }
        if (abs($dx)>=50) { $this->SubGrids=5; $deltax=10*$r*sign($dx); }
        else
        { if (abs($dx)>=20) { $this->SubGrids=5; $deltax=5*$r*sign($dx); }
          else { $this->SubGrids=4; $deltax=2*$r*sign($dx); }
        }
      }
      else $deltax=$this->DateInterval(abs($dx))*sign($dx);
      if ($this->XGridDelta!=0) $deltax=$this->XGridDelta;
      if ($this->XSubGrids!=0) $this->SubGrids=$this->XSubGrids;
      $x=floor($this->xmin/$deltax)*$deltax;
      $i=0;
      $this->XGrid[1]=$deltax;
      if ($deltax!=0) $this->MaxGrids=floor(abs(($this->xmax-$this->xmin)/$deltax))+2;
      else $this->MaxGrids=0;
      for ($j=$this->MaxGrids; $j>=-1; $j--)
      { $xr=$x+$j*$deltax;
        $x0=round($this->left+(-$this->xmin+$xr)*$invdifx);
        if (($x0>=$this->left)&&($x0<=$this->right))
        { if ($i==0) $this->XGrid[2]=$xr;
          $this->XGrid[0]=$xr;
          $i++;
        }
      }
    }
    return($this->XGrid);
  }

  function GetYGrid()
  { $dy=$this->ymax-$this->ymin;
    if (abs($dy)>0)
    { $invdify=($this->bottom-$this->top)/($this->ymax-$this->ymin);
      if (($this->YScale==1)||(is_string($this->YScale)))
      { $r=1;
        while (abs($dy)>=100) { $dy/=10; $r*=10; }
        while (abs($dy)<10) { $dy*=10; $r/=10; }
        if (abs($dy)>=50) { $this->SubGrids=5; $deltay=10*$r*sign($dy); }
        else
        { if (abs($dy)>=20) { $this->SubGrids=5; $deltay=5*$r*sign($dy); }
          else { $this->SubGrids=4; $deltay=2*$r*sign($dy); }
        }
      }
      else $deltay=$this->DateInterval(abs($dy))*sign($dy);
      if ($this->YGridDelta!=0) $deltay=$this->YGridDelta;
      if ($this->YSubGrids!=0) $this->SubGrids=$this->YSubGrids;
      $y=floor($this->ymax/$deltay)*$deltay;
      $this->YGrid[1]=$deltay;
      $i=0;
      if ($deltay!=0) $this->MaxGrids=floor(abs(($this->ymax-$this->ymin)/$deltay))+2;
      else $this->MaxGrids=0;
      for ($j=-1; $j<=$this->MaxGrids; $j++)
      { $yr=$y-$j*$deltay;
        $y0=round($this->top+($this->ymax-$yr)*$invdify);
        if (($y0>=$this->top)&&($y0<=$this->bottom))
        { if ($i==0) $this->YGrid[2]=$yr;
          $this->YGrid[0]=$yr;
          $i++;
        }
      }
    }
    return($this->YGrid);
  }

  function Draw($theDrawColor="", $theTextColor="000000", $isScaleText=false, $theTooltipText="", $theOnClickAction="", $theOnMouseoverAction="", $theOnMouseoutAction="")
  { if ($theDrawColor!="")
    { if (($ee=strchr($theDrawColor,"."))!="")
      { if ($ee==".gif") $ii=imagecreatefromgif($theDrawColor);
        if ($ee==".png") $ii=imagecreatefrompng($theDrawColor);
        if ($ee==".jpg") $ii=imagecreatefromjpeg($theDrawColor);
        if ($ee==".jpeg") $ii=imagecreatefromjpeg($theDrawColor);
        $ww = imagesx($ii);
        $hh = imagesy($ii);
        imagecopyresized($this->Img, $ii, $this->left, $this->top, 0, 0, $this->right-$this->left+1, $this->bottom-$this->top+1, $ww, $hh);
        imagedestroy($ii);
      }
      else
      { $ccDraw=$this->GetImgColorIndex($theDrawColor);
        imagefilledrectangle($this->Img,$this->left,$this->top,$this->right,$this->bottom,$ccDraw);
      }
    }
    $ccText=$this->GetImgColorIndex($theTextColor);
    if ($this->XGridColor!="") $ccGridX=$this->GetImgColorIndex($this->XGridColor);
    if ($this->YGridColor!="") $ccGridY=$this->GetImgColorIndex($this->YGridColor);
    if ($this->XSubGridColor!="") $ccSubGridX=$this->GetImgColorIndex($this->XSubGridColor);
    if ($this->YSubGridColor!="") $ccSubGridY=$this->GetImgColorIndex($this->YSubGridColor);

    if (($this->XScale==1)||(is_string($this->XScale)))
    { $u="";
      $fn="";
      if (is_string($this->XScale))
      { if (substr($this->XScale,0,9)=="function ") $fn=substr($this->XScale,9);
        else $u=$this->XScale;
      }
      $dx=($this->xmax-$this->xmin);
      if (abs($dx)>0)
      { $invdifx=($this->right-$this->left)/($this->xmax-$this->xmin);
        $r=1;
        while (abs($dx)>=100) { $dx/=10; $r*=10; }
        while (abs($dx)<10) { $dx*=10; $r/=10; }
        if (abs($dx)>=50) { $this->SubGrids=5; $deltax=10*$r*sign($dx); }
        else
        { if (abs($dx)>=20) { $this->SubGrids=5; $deltax=5*$r*sign($dx); }
          else { $this->SubGrids=4; $deltax=2*$r*sign($dx); }
        }
        if ($this->XGridDelta!=0) $deltax=$this->XGridDelta;
        if ($this->XSubGrids!=0) $this->SubGrids=$this->XSubGrids;
        $sub=$deltax*$invdifx/$this->SubGrids;
        $sshift=0;
        if (($this->XScalePosition=="top-left")||($this->XScalePosition=="bottom-left")) $sshift=-abs($deltax*$invdifx/2);
        if (($this->XScalePosition=="top-right")||($this->XScalePosition=="bottom-right")) $sshift=abs($deltax*$invdifx/2);
        $x=floor($this->xmin/$deltax)*$deltax;
        $itext=0;
        if ($deltax!=0) $this->MaxGrids=floor(abs(($this->xmax-$this->xmin)/$deltax))+2;
        else $this->MaxGrids=0;
        for ($j=$this->MaxGrids; $j>=-1; $j--)
        { $xr=$x+$j*$deltax;
          $x0=round($this->left+(-$this->xmin+$xr)*$invdifx);
          if ($this->XSubGridColor!="")
          { for ($k=1; $k<$this->SubGrids; $k++)
            { if (($x0-$k*$sub>$this->left+1)&&($x0-$k*$sub<$this->right-1))
                imageline($this->Img,round($x0-$k*$sub),$this->top+1,round($x0-$k*$sub),$this->bottom-1,$ccSubGridX);
            }
            if ($this->SubGrids==-1)
            { for ($k=0; $k<8; $k++)
              { if (($x0-$this->logsub[$k]*$sub*sign($deltax)>$this->left+1)&&($x0-$this->logsub[$k]*$sub*sign($deltax)<$this->right-1))
                  imageline($this->Img,round($x0-$this->logsub[$k]*$sub*sign($deltax)),$this->top+1,round($x0-$this->logsub[$k]*$sub*sign($deltax)),$this->bottom-1,$ccSubGridX);
              }
            }
          }
          if (($x0>=$this->left)&&($x0<=$this->right))
          { $itext++;
            if (($itext!=2)||(!$isScaleText))
            { if ($r>1)
              { if ($fn!="") $l=call_user_func($fn,$xr);
                else $l=$xr.$u;
              }
              else
              { if ($fn!="") $l=call_user_func($fn,round(10*$xr/$r)/round(10/$r));
                else $l=round(10*$xr/$r)/round(10/$r) .$u;
              }
              if (substr($l,0,1)==".") $l="0".$l;
              if (substr($l,0,2)=="-.") $l="-0"+substr($l,1);
            }
            else $l=$this->xtext;
            $dd=imagefontwidth(abs($this->Font))*strlen($l);
            $hh=imagefontheight(abs($this->Font));
            if (substr($this->XScalePosition,0,3)!="top")
            { if (($x0+$sshift>=$this->left)&&($x0+$sshift<=$this->right))
              { if ($this->Font>=0) imagestring($this->Img, $this->Font, $x0-$dd/2+$sshift, $this->bottom+8, $l, $ccText);
                else imagestringup($this->Img, -$this->Font, $x0-$hh/2+$sshift, $this->bottom+9+$dd, $l, $ccText);
              }
              imageline($this->Img, $x0, $this->bottom-5, $x0, $this->bottom+6, $ccText);
            }
            else
            { if (($x0+$sshift>=$this->left)&&($x0+$sshift<=$this->right))
              { if ($this->Font>=0) imagestring($this->Img, $this->Font, $x0-$dd/2+$sshift, $this->top-6-$hh, $l, $ccText);
                else imagestringup($this->Img, -$this->Font, $x0-$hh/2+$sshift, $this->top-9, $l, $ccText);
              }
              imageline($this->Img, $x0, $this->top-5, $x0, $this->top+6, $ccText);
            }
            if (($this->XGridColor!="")&&($x0>$this->left)&&($x0<$this->right))
              imageline($this->Img, $x0, $this->top+1, $x0, $this->bottom-1, $ccGridX);
          }
        }
      }
    }
    if ((!is_string($this->XScale))&&($this->XScale>1))
    { $dx=($this->xmax-$this->xmin);
      if (abs($dx)>0)
      { $invdifx=($this->right-$this->left)/($this->xmax-$this->xmin);
        $deltax=$this->DateInterval(abs($dx))*sign($dx);
        if ($this->XGridDelta!=0) $deltax=$this->XGridDelta;
        if ($this->XSubGrids!=0) $this->SubGrids=$this->XSubGrids;
        $sub=$deltax*$invdifx/$this->SubGrids;
        $sshift=0;
        if (($this->XScalePosition=="top-left")||($this->XScalePosition=="bottom-left")) $sshift=-abs($deltax*$invdifx/2);
        if (($this->XScalePosition=="top-right")||($this->XScalePosition=="bottom-right")) $sshift=abs($deltax*$invdifx/2);
        $x=floor($this->xmin/$deltax)*$deltax;
        $itext=0;
        if ($deltax!=0) $this->MaxGrids=floor(abs(($this->xmax-$this->xmin)/$deltax))+2;
        else $this->MaxGrids=0;
        for ($j=$this->MaxGrids; $j>=-2; $j--)
        { $xr=$x+$j*$deltax;
          $x0=round($this->left+(-$this->xmin+$x+$j*$deltax)*$invdifx);
          if ($this->XSubGridColor!="")
          { for ($k=1; $k<$this->SubGrids; $k++)
            { if (($x0-$k*$sub>$this->left+1)&&($x0-$k*$sub<$this->right-1))
                imageline($this->Img,round($x0-$k*$sub),$this->top+1,round($x0-$k*$sub),$this->bottom-1,$ccSubGridX);
            }
          }
          if (($x0>=$this->left)&&($x0<=$this->right))
          { $itext++;
            if (($itext!=2)||(!$isScaleText)) $l=DateFormat($xr, abs($deltax), $this->XScale);
            else $l=$this->xtext;
            $dd=imagefontwidth(abs($this->Font))*strlen($l);
            $hh=imagefontheight(abs($this->Font));
            if (substr($this->XScalePosition,0,3)!="top")
            { if (($x0+$sshift>=$this->left)&&($x0+$sshift<=$this->right))
              { if ($this->Font>=0) imagestring($this->Img, $this->Font, $x0-$dd/2+$sshift, $this->bottom+8, $l, $ccText);
                else imagestringup($this->Img, -$this->Font, $x0-$hh/2+$sshift, $this->bottom+9+$dd, $l, $ccText);
              }
              imageline($this->Img, $x0, $this->bottom-5, $x0, $this->bottom+6, $ccText);
            }
            else
            { if (($x0+$sshift>=$this->left)&&($x0+$sshift<=$this->right))
              { if ($this->Font>=0) imagestring($this->Img, $this->Font, $x0-$dd/2+$sshift, $this->top-6-$hh, $l, $ccText);
                else imagestringup($this->Img, -$this->Font, $x0-$hh/2+$sshift, $this->top-9, $l, $ccText);
              }
              imageline($this->Img, $x0, $this->top-5, $x0, $this->top+6, $ccText);
            }
            if (($this->XGridColor!="")&&($x0>$this->left)&&($x0<$this->right))
              imageline($this->Img, $x0, $this->top+1, $x0, $this->bottom-1, $ccGridX);
          }
        }
      }
    }
    if (($this->YScale==1)||(is_string($this->YScale)))
    { $u="";
      $fn="";
      if (is_string($this->YScale))
      { if (substr($this->YScale,0,9)=="function ") $fn=substr($this->YScale,9);
        else $u=$this->YScale;
      }
      $dy=$this->ymax-$this->ymin;
      if (abs($dy)>0)
      { $invdify=($this->bottom-$this->top)/($this->ymax-$this->ymin);
        $r=1;
        while (abs($dy)>=100) { $dy/=10; $r*=10; }
        while (abs($dy)<10) { $dy*=10; $r/=10; }
        if (abs($dy)>=50) { $this->SubGrids=5; $deltay=10*$r*sign($dy); }
        else
        { if (abs($dy)>=20) { $this->SubGrids=5; $deltay=5*$r*sign($dy); }
          else { $this->SubGrids=4; $deltay=2*$r*sign($dy); }
        }
        if ($this->YGridDelta!=0) $deltay=$this->YGridDelta;
        if ($this->YSubGrids!=0) $this->SubGrids=$this->YSubGrids;
        $sub=$deltay*$invdify/$this->SubGrids;
        $sshift=0;
        if (($this->YScalePosition=="left-top")||($this->YScalePosition=="right-top")) $sshift=-abs($deltay*$invdify/2);
        if (($this->YScalePosition=="left-bottom")||($this->YScalePosition=="right-bottom")) $sshift=abs($deltay*$invdify/2);
        $y=floor($this->ymax/$deltay)*$deltay;
        $itext=0;
        if ($deltay!=0) $this->MaxGrids=floor(abs(($this->ymax-$this->ymin)/$deltay))+2;
        else $this->MaxGrids=0;
        for ($j=-1; $j<=$this->MaxGrids; $j++)
        { $yr=$y-$j*$deltay;
          $y0=round($this->top+($this->ymax-$yr)*$invdify);
          if ($this->YSubGridColor!="")
          { for ($k=1; $k<$this->SubGrids; $k++)
            { if (($y0+$k*$sub>$this->top+1)&&($y0+$k*$sub<$this->bottom-1))
                imageline($this->Img,$this->left+1,round($y0+$k*$sub),$this->right-1,round($y0+$k*$sub),$ccSubGridY);
            }
            if ($this->SubGrids==-1)
            { for ($k=0; $k<8; $k++)
              { if (($y0+$this->logsub[$k]*$sub*sign($deltay)>$this->top+1)&&($y0+$this->logsub[$k]*$sub*sign($deltay)<$this->bottom-1))
                  imageline($this->Img,$this->left+1,round($y0+$this->logsub[$k]*$sub*sign($deltay)),$this->right-1,round($y0+$this->logsub[$k]*$sub*sign($deltay)),$ccSubGridY);
              }
            }
          }
          if (($y0>=$this->top)&&($y0<=$this->bottom))
          { $itext++;
            if (($itext!=2)||(!$isScaleText))
            { if ($r>1)
              { if ($fn!="") $l=call_user_func($fn,$yr);
                else $l=$yr.$u;
              }
              else
              { if ($fn!="") $l=call_user_func($fn,round(10*$yr/$r)/round(10/$r));
                else $l=round(10*$yr/$r)/round(10/$r) .$u;
              }
              if (substr($l,0,1)==".") $l="0".$l;
              if (substr($l,0,2)=="-.") $l="-0"+substr($l,1);
            }
            else $l=$this->ytext;
            $dd=imagefontwidth(abs($this->Font))*strlen($l);
            $hh=imagefontheight(abs($this->Font));
            if (substr($this->YScalePosition,0,5)!="right")
            { if (($y0+$sshift>=$this->top)&&($y0+$sshift<=$this->bottom))
                imagestring($this->Img, abs($this->Font), $this->left-$dd-10, $y0-$hh/2+$sshift, $l, $ccText);
              imageline($this->Img, $this->left-5, $y0, $this->left+6, $y0, $ccText);
            }
            else
            { if (($y0+$sshift>=$this->top)&&($y0+$sshift<=$this->bottom))
                imagestring($this->Img, abs($this->Font), $this->right+10, $y0-$hh/2+$sshift, $l, $ccText);
              imageline($this->Img, $this->right-5, $y0, $this->right+6, $y0, $ccText);
            }
            if (($this->YGridColor!="")&&($y0>$this->top)&&($y0<$this->bottom))
              imageline($this->Img, $this->left+1, $y0, $this->right-1, $y0, $ccGridY);
          }
        }
      }
    }
    if ((!is_string($this->YScale))&&($this->YScale>1))
    { $dy=$this->ymax-$this->ymin;
      if (abs($dy)>0)
      { $invdify=($this->bottom-$this->top)/($this->ymax-$this->ymin);
        $deltay=$this->DateInterval(abs($dy))*sign($dy);
        if ($this->YGridDelta!=0) $deltay=$this->YGridDelta;
        if ($this->YSubGrids!=0) $this->SubGrids=$this->YSubGrids;
        $sub=$deltay*$invdify/$this->SubGrids;
        $sshift=0;
        if (($this->YScalePosition=="left-top")||($this->YScalePosition=="right-top")) $sshift=-abs($deltay*$invdify/2);
        if (($this->YScalePosition=="left-bottom")||($this->YScalePosition=="right-bottom")) $sshift=abs($deltay*$invdify/2);
        $y=floor($this->ymax/$deltay)*$deltay;
        $itext=0;
        if ($deltay!=0) $this->MaxGrids=floor(abs(($this->ymax-$this->ymin)/$deltay))+2;
        else $this->MaxGrids=0;
        for ($j=-2; $j<=$this->MaxGrids; $j++)
        { $yr=$y-$j*$deltay;
          $y0=round($this->top+($this->ymax-$y+$j*$deltay)*$invdify);
          if ($this->YSubGridColor!="")
          { for ($k=1; $k<$this->SubGrids; $k++)
            { if (($y0+$k*$sub>$this->top+1)&&($y0+$k*$sub<$this->bottom-1))
                imageline($this->Img,$this->left+1,round($y0+$k*$sub),$this->right-1,round($y0+$k*$sub),$ccSubGridY);
            }
          }
          if (($y0>=$this->top)&&($y0<=$this->bottom))
          { $itext++;
            if (($itext!=2)||(!$isScaleText)) $l=DateFormat($yr, abs($deltay), $this->YScale);
            else $l=$this->ytext;
            $dd=imagefontwidth(abs($this->Font))*strlen($l);
            $hh=imagefontheight(abs($this->Font));
            if (substr($this->YScalePosition,0,5)!="right")
            { if (($y0+$sshift>=$this->top)&&($y0+$sshift<=$this->bottom))
                imagestring($this->Img, abs($this->Font), $this->left-$dd-10, $y0-$hh/2+$sshift, $l, $ccText);
              imageline($this->Img, $this->left-5, $y0, $this->left+6, $y0, $ccText);
            }
            else
            { if (($y0+$sshift>=$this->top)&&($y0+$sshift<=$this->bottom))
                imagestring($this->Img, abs($this->Font), $this->right+10, $y0-$hh/2+$sshift, $l, $ccText);
              imageline($this->Img, $this->right-5, $y0, $this->right+6, $y0, $ccText);
            }
            if (($this->YGridColor!="")&&($y0>$this->top)&&($y0<$this->bottom))
              imageline($this->Img, $this->left+1, $y0, $this->right-1, $y0, $ccGridY);
          }
        }
      }
    }
    imagerectangle($this->Img,$this->left,$this->top,$this->right,$this->bottom,$ccText);
    if ($this->title!="")
    { $dd=imagefontwidth(abs($this->Font))*strlen($this->title);
      $hh=imagefontheight(abs($this->Font));
      if (substr($this->XScalePosition,0,3)!="top")
        imagestring($this->Img, abs($this->Font), ($this->left+$this->right-$dd)/2, $this->top-$hh-4, $this->title, $ccText);
      else
        imagestring($this->Img, abs($this->Font), ($this->left+$this->right-$dd)/2, $this->bottom+4, $this->title, $ccText);
    }
    if (($theTooltipText!="")||($theOnClickAction!="")||($theOnMouseoverAction!="")||($theOnMouseoutAction!=""))
    { $tmpMapData="<area shape=rect coords='".$this->left.",".$this->top.",".$this->right.",".$this->bottom."' ";
      if ($theTooltipText!="") $tmpMapData=$tmpMapData." title='".$theTooltipText."' alt='".$theTooltipText."'";
      if ($theOnClickAction!="") $tmpMapData=$tmpMapData." href='javascript:".$theOnClickAction."'";
      if ($theOnMouseoverAction!="") $tmpMapData=$tmpMapData." onMouseover='".$theOnMouseoverAction."'";
      if ($theOnMouseoutAction!="") $tmpMapData=$tmpMapData." onMouseout='".$theOnMouseoutAction."'";
      $tmpMapData=$tmpMapData.">\n";
      $this->ImgMapData=$tmpMapData.$this->ImgMapData;
    }
  }

  function Bar($theLeft, $theTop, $theRight, $theBottom, $theDrawColor, $theText="", $theTextColor="000000", $theTooltipText="", $theOnClickAction="", $theOnMouseoverAction="", $theOnMouseoutAction="")
  { if ($theText!="") $dd=imagefontwidth(abs($this->BFont))*strlen($theText)-2;
    else $dd=0;
    $ll=$theLeft;
    $rr=$theRight;
    $tt=$theTop;
    $bb=$theBottom;
    if ($this->BFont>0)
    { if ((!is_float($ll))&&(is_float($rr))) $ll=$rr-$dd-2;
      if ((!is_float($rr))&&(is_float($ll))) $rr=$ll+$dd+2;
      if ((!is_float($tt))&&(is_float($bb))) $tt=$bb-12-$this->BFont;
      if ((!is_float($bb))&&(is_float($tt))) $bb=$tt+12+$this->BFont;
    }
    else
    { if ((!is_float($ll))&&(is_float($rr))) $ll=$rr-12+$this->BFont;
      if ((!is_float($rr))&&(is_float($ll))) $rr=$ll+12-$this->BFont;
      if ((!is_float($tt))&&(is_float($bb))) $tt=$bb-$dd-2;
      if ((!is_float($bb))&&(is_float($tt))) $bb=$tt+$dd+2;
    }
    if ($theDrawColor!="")
    { if (($ee=strchr($theDrawColor,"."))!="")
      { if ($ee==".gif") $ii=imagecreatefromgif($theDrawColor);
        if ($ee==".png") $ii=imagecreatefrompng($theDrawColor);
        if ($ee==".jpg") $ii=imagecreatefromjpeg($theDrawColor);
        if ($ee==".jpeg") $ii=imagecreatefromjpeg($theDrawColor);
        $ww = imagesx($ii);
        $hh = imagesy($ii);
        imagecopyresized($this->Img, $ii, $ll, $tt, 0, 0, $rr-$ll+1, $bb-$tt+1, $ww, $hh);
        imagedestroy($ii);
      }
      else
      { $cc=$this->GetImgColorIndex($theDrawColor);
        imagefilledrectangle($this->Img,$ll,$tt,$rr,$bb,$cc);
      }
    }
    if ($theText!="")
    { $cc=$this->GetImgColorIndex($theTextColor);
      if ($this->BFont>0) imagestring ($this->Img, $this->BFont, ($ll+$rr-$dd)/2, $tt+1, $theText, $cc);
      else imagestringup ($this->Img, -$this->BFont, $ll+1, ($tt+$bb+$dd)/2, $theText, $cc);
    }
    if (($theTooltipText!="")||($theOnClickAction!="")||($theOnMouseoverAction!="")||($theOnMouseoutAction!=""))
    { $tmpMapData="<area shape=rect coords='".$ll.",".$tt.",".$rr.",".$bb."' ";
      if ($theTooltipText!="") $tmpMapData=$tmpMapData." title='".$theTooltipText."' alt='".$theTooltipText."'";
      if ($theOnClickAction!="") $tmpMapData=$tmpMapData." href='javascript:".$theOnClickAction."'";
      if ($theOnMouseoverAction!="") $tmpMapData=$tmpMapData." onMouseover='".$theOnMouseoverAction."'";
      if ($theOnMouseoutAction!="") $tmpMapData=$tmpMapData." onMouseout='".$theOnMouseoutAction."'";
      $tmpMapData=$tmpMapData.">\n";
      $this->ImgMapData=$tmpMapData.$this->ImgMapData;
    }
  }

  function Box($theLeft, $theTop, $theRight, $theBottom, $theDrawColor, $theText="", $theTextColor="000000", $theBorderWidth=1, $theBorderColor="000000", $theTooltipText="", $theOnClickAction="", $theOnMouseoverAction="", $theOnMouseoutAction="")
  { if ($theText!="") $dd=imagefontwidth(abs($this->BFont))*strlen($theText)-2;
    else $dd=0;
    $ll=$theLeft;
    $rr=$theRight;
    $tt=$theTop;
    $bb=$theBottom;
    if ($this->BFont>0)
    { if ((!is_float($ll))&&(is_float($rr))) $ll=$rr-$dd-2-2*$theBorderWidth;
      if ((!is_float($rr))&&(is_float($ll))) $rr=$ll+$dd+2+2*$theBorderWidth;
      if ((!is_float($tt))&&(is_float($bb))) $tt=$bb-12-$this->BFont-2*$theBorderWidth;
      if ((!is_float($bb))&&(is_float($tt))) $bb=$tt+12+$this->BFont+2*$theBorderWidth;
    }
    else
    { if ((!is_float($ll))&&(is_float($rr))) $ll=$rr-12+$this->BFont-2*$theBorderWidth;
      if ((!is_float($rr))&&(is_float($ll))) $rr=$ll+12-$this->BFont+2*$theBorderWidth;
      if ((!is_float($tt))&&(is_float($bb))) $tt=$bb-$dd-2-2*$theBorderWidth;
      if ((!is_float($bb))&&(is_float($tt))) $bb=$tt+$dd+2+2*$theBorderWidth;
    }
    if ($theDrawColor!="")
    { if (($ee=strchr($theDrawColor,"."))!="")
      { if ($ee==".gif") $ii=imagecreatefromgif($theDrawColor);
        if ($ee==".png") $ii=imagecreatefrompng($theDrawColor);
        if ($ee==".jpg") $ii=imagecreatefromjpeg($theDrawColor);
        if ($ee==".jpeg") $ii=imagecreatefromjpeg($theDrawColor);
        $ww = imagesx($ii);
        $hh = imagesy($ii);
        imagecopyresized($this->Img, $ii, $ll, $tt, 0, 0, $rr-$ll+1, $bb-$tt+1, $ww, $hh);
        imagedestroy($ii);
      }
      else
      { $cc=$this->GetImgColorIndex($theDrawColor);
        imagefilledrectangle($this->Img,$ll,$tt,$rr,$bb,$cc);
      }
    }
    $cc=$this->GetImgColorIndex($theBorderColor);
    for ($ii=0; $ii<$theBorderWidth; $ii++)
      imagerectangle($this->Img,$ll+$ii,$tt+$ii,$rr-$ii,$bb-$ii,$cc);
    if ($theText!="")
    { $cc=$this->GetImgColorIndex($theTextColor);
      if ($this->BFont>0) imagestring ($this->Img, $this->BFont, ($ll+$rr-$dd)/2, $tt+$theBorderWidth+1, $theText, $cc);
      else imagestringup ($this->Img, -$this->BFont, $ll+$theBorderWidth+1, ($tt+$bb+$dd)/2, $theText, $cc);
    }
    if (($theTooltipText!="")||($theOnClickAction!="")||($theOnMouseoverAction!="")||($theOnMouseoutAction!=""))
    { $tmpMapData="<area shape=rect coords='".$ll.",".$tt.",".$rr.",".$bb."' ";
      if ($theTooltipText!="") $tmpMapData=$tmpMapData." title='".$theTooltipText."' alt='".$theTooltipText."'";
      if ($theOnClickAction!="") $tmpMapData=$tmpMapData." href='javascript:".$theOnClickAction."'";
      if ($theOnMouseoverAction!="") $tmpMapData=$tmpMapData." onMouseover='".$theOnMouseoverAction."'";
      if ($theOnMouseoutAction!="") $tmpMapData=$tmpMapData." onMouseout='".$theOnMouseoutAction."'";
      $tmpMapData=$tmpMapData.">\n";
      $this->ImgMapData=$tmpMapData.$this->ImgMapData;
    }
  }

  function Dot($theX, $theY, $theSize, $theType, $theColor, $theTooltipText="", $theOnClickAction="", $theOnMouseoverAction="", $theOnMouseoutAction="")
  { if (is_string($theType))
    { if (($ee=strchr($theType,"."))!="")
      { if ($ee==".gif") $ii=imagecreatefromgif($theType);
        if ($ee==".png") $ii=imagecreatefrompng($theType);
        if ($ee==".jpg") $ii=imagecreatefromjpeg($theType);
        if ($ee==".jpeg") $ii=imagecreatefromjpeg($theType);
        $ww = imagesx($ii);
        $hh = imagesy($ii);
        imagecopyresized($this->Img, $ii, $theX-$theSize/2+1, $theY-$theSize/2+1, 0, 0, $theSize, $theSize, $ww, $hh);
        imagedestroy($ii);
      }
    }
    else
    { $cc=$this->GetImgColorIndex($theColor);
      if ($theType%6==0)
        imagefilledellipse($this->Img,$theX,$theY,$theSize,$theSize,$cc);
      if ($theType%6==1)
      { imagesetthickness($this->Img,$theSize/4+1);
        imageline($this->Img,$theX-$theSize/2,$theY,$theX+$theSize/2,$theY,$cc);
        imageline($this->Img,$theX,$theY-$theSize/2,$theX,$theY+$theSize/2,$cc);
        imagesetthickness($this->Img,1);
      }
      if ($theType%6==2)
        imagefilledrectangle($this->Img,$theX-$theSize/2,$theY-$theSize/2,$theX+$theSize/2,$theY+$theSize/2,$cc);
      if ($theType%6==3)
      { imagesetthickness($this->Img,round($theSize/6));
        imageline($this->Img,$theX-5*$theSize/12,$theY-3*$theSize/12,$theX,$theY+7*$theSize/12,$cc);
        imageline($this->Img,$theX,$theY+7*$theSize/12,$theX+5*$theSize/12,$theY-3*$theSize/12+1,$cc);
        imageline($this->Img,$theX-5*$theSize/12,$theY-3*$theSize/12,$theX+5*$theSize/12,$theY-3*$theSize/12,$cc);
        imagesetthickness($this->Img,1);
      }
      if ($theType%6==4)
      { imagesetthickness($this->Img,round($theSize/6));
        imageline($this->Img,$theX-5*$theSize/12,$theY+3*$theSize/12,$theX,$theY-7*$theSize/12,$cc);
        imageline($this->Img,$theX,$theY-7*$theSize/12,$theX+5*$theSize/12,$theY+3*$theSize/12,$cc);
        imageline($this->Img,$theX-5*$theSize/12,$theY+3*$theSize/12,$theX+5*$theSize/12,$theY+3*$theSize/12,$cc);
        imagesetthickness($this->Img,1);
      }
      if ($theType%6==5)
      { imagesetthickness($this->Img,round($theSize/6));
        imagerectangle($this->Img,$theX-5*$theSize/12,$theY-5*$theSize/12,$theX+5*$theSize/12,$theY+5*$theSize/12,$cc);
        imagesetthickness($this->Img,1);
      }
    }
    if (($theTooltipText!="")||($theOnClickAction!="")||($theOnMouseoverAction!="")||($theOnMouseoutAction!=""))
    { $tmpMapData="<area shape=circle coords='".$theX.",".$theY.",".round($theSize/2)."' ";
      if ($theTooltipText!="") $tmpMapData=$tmpMapData." title='".$theTooltipText."' alt='".$theTooltipText."'";
      if ($theOnClickAction!="") $tmpMapData=$tmpMapData." href='javascript:".$theOnClickAction."'";
      if ($theOnMouseoverAction!="") $tmpMapData=$tmpMapData." onMouseover='".$theOnMouseoverAction."'";
      if ($theOnMouseoutAction!="") $tmpMapData=$tmpMapData." onMouseout='".$theOnMouseoutAction."'";
      $tmpMapData=$tmpMapData.">\n";
      $this->ImgMapData=$tmpMapData.$this->ImgMapData;
    }
  }

  function Pixel($theX, $theY, $theColor)
  { $cc=$this->GetImgColorIndex($theColor);
    imagesetpixel($this->Img,$theX,$theY,$cc);
  }

  function Line($theX0, $theY0, $theX1, $theY1, $theColor, $theSize=1, $theTooltipText="", $theOnClickAction="", $theOnMouseoverAction="", $theOnMouseoutAction="")
  { $cc=$this->GetImgColorIndex($theColor);
    imagesetthickness($this->Img,2*$theSize-1);
    imageline($this->Img,$theX0,$theY0,$theX1,$theY1,$cc);
    imagesetthickness($this->Img,1);
    if (($theTooltipText!="")||($theOnClickAction!="")||($theOnMouseoverAction!="")||($theOnMouseoutAction!=""))
    { $LL=1;
      $DDX=$theX1-$theX0;
      $DDY=$theY1-$theY0;
      if (($DDX!=0)||($DDY!=0)) $LL=sqrt(($DDX*$DDX)+($DDY*$DDY));
      $tmpMapData="<area shape=polygon coords='".round($theX0+($theSize+1)*$DDY/$LL).",".round($theY0-($theSize+1)*$DDX/$LL).",".round($theX1+($theSize+1)*$DDY/$LL).",".round($theY1-($theSize+1)*$DDX/$LL).",".round($theX1-($theSize+1)*$DDY/$LL).",".round($theY1+($theSize+1)*$DDX/$LL).",".round($theX0-($theSize+1)*$DDY/$LL).",".round($theY0+($theSize+1)*$DDX/$LL)."' ";
      if ($theTooltipText!="") $tmpMapData=$tmpMapData." title='".$theTooltipText."' alt='".$theTooltipText."'";
      if ($theOnClickAction!="") $tmpMapData=$tmpMapData." href='javascript:".$theOnClickAction."'";
      if ($theOnMouseoverAction!="") $tmpMapData=$tmpMapData." onMouseover='".$theOnMouseoverAction."'";
      if ($theOnMouseoutAction!="") $tmpMapData=$tmpMapData." onMouseout='".$theOnMouseoutAction."'";
      $tmpMapData=$tmpMapData.">\n";
      $this->ImgMapData=$tmpMapData.$this->ImgMapData;
    }
  }

  function Area($theX0, $theY0, $theX1, $theY1, $theColor, $theBase=0, $theTooltipText="", $theOnClickAction="", $theOnMouseoverAction="", $theOnMouseoutAction="")
  { $cc=$this->GetImgColorIndex($theColor);
    $pp=array($theX0, $theBase, $theX0, $theY0, $theX1, $theY1, $theX1, $theBase-1);
    imagefilledpolygon($this->Img, $pp, 4 , $cc);
    if (($theTooltipText!="")||($theOnClickAction!="")||($theOnMouseoverAction!="")||($theOnMouseoutAction!=""))
    { $tmpMapData="<area shape=polygon coords='".$theX0.",".$theBase.",".$theX0.",".$theY0.",".$theX1.",".$theY1.",".$theX1.",".$theBase."' ";
      if ($theTooltipText!="") $tmpMapData=$tmpMapData." title='".$theTooltipText."' alt='".$theTooltipText."'";
      if ($theOnClickAction!="") $tmpMapData=$tmpMapData." href='javascript:".$theOnClickAction."'";
      if ($theOnMouseoverAction!="") $tmpMapData=$tmpMapData." onMouseover='".$theOnMouseoverAction."'";
      if ($theOnMouseoutAction!="") $tmpMapData=$tmpMapData." onMouseout='".$theOnMouseoutAction."'";
      $tmpMapData=$tmpMapData.">\n";
      $this->ImgMapData=$tmpMapData.$this->ImgMapData;
    }
  }

  function Arrow($theX0, $theY0, $theX1, $theY1, $theColor, $theSize=1, $theTooltipText="", $theOnClickAction="", $theOnMouseoverAction="", $theOnMouseoutAction="")
  { $cc=$this->GetImgColorIndex($theColor);
    $LL=1;
    $ccL=6*$theSize+8;
    $ccB=2*$theSize+2;
    $DDX=$theX1-$theX0;
    $DDY=$theY1-$theY0;
    if (($DDX!=0)||($DDY!=0)) $LL=sqrt(($DDX*$DDX)+($DDY*$DDY));
    $pp=array($theX1-round(($ccL*$DDX+$ccB*$DDY)/$LL),$theY1-round(($ccL*$DDY-$ccB*$DDX)/$LL),$theX1,$theY1,$theX1-round(($ccL*$DDX-$ccB*$DDY)/$LL),$theY1-round(($ccL*$DDY+$ccB*$DDX)/$LL));
    imagefilledpolygon($this->Img, $pp, 3 , $cc);
    imagesetthickness($this->Img,2*$theSize-1);
    imageline($this->Img,$theX0,$theY0,($pp[0]+$pp[2]+$pp[4])/3,($pp[1]+$pp[3]+$pp[5])/3,$cc);
    imagesetthickness($this->Img,1);
    if (($theTooltipText!="")||($theOnClickAction!="")||($theOnMouseoverAction!="")||($theOnMouseoutAction!=""))
    { $tmpMapData="<area shape=polygon coords='".
      $pp[2].",".$pp[3].",".$pp[4].",".$pp[5].",".$pp[0].",".$pp[1].",".$pp[2].",".$pp[3].",".
      round($theX0-$theSize*$DDY/$LL).",".round($theY0+$theSize*$DDX/$LL).",".
      round($theX0+$theSize*$DDY/$LL).",".round($theY0-$theSize*$DDX/$LL)."' ";
      if ($theTooltipText!="") $tmpMapData=$tmpMapData." title='".$theTooltipText."' alt='".$theTooltipText."'";
      if ($theOnClickAction!="") $tmpMapData=$tmpMapData." href='javascript:".$theOnClickAction."'";
      if ($theOnMouseoverAction!="") $tmpMapData=$tmpMapData." onMouseover='".$theOnMouseoverAction."'";
      if ($theOnMouseoutAction!="") $tmpMapData=$tmpMapData." onMouseout='".$theOnMouseoutAction."'";
      $tmpMapData=$tmpMapData.">\n";
      $this->ImgMapData=$tmpMapData.$this->ImgMapData;
    }
  }

  function Pie($theXCenter, $theYCenter, $theOffset, $theRadius, $theAngle0, $theAngle1, $theColor, $theTooltipText="", $theOnClickAction="", $theOnMouseoverAction="", $theOnMouseoutAction="")
  { $cc=$this->GetImgColorIndex($theColor);
    $aa0=$theAngle0;
    while ($aa0>=360) $aa0-=360;
    while ($aa0<0) $aa0+=360;
    $aa1=$theAngle1;
    while ($aa1>=360) $aa1-=360;
    while ($aa1<0) $aa1+=360;
    $xxo=0;
    $yyo=0;
    if ($aa0<$aa1)
    { $xxo=sin(($aa0+$aa1)*pi()/360)*$theOffset;
      $yyo=-cos(($aa0+$aa1)*pi()/360)*$theOffset;
    }
    if ($aa0>$aa1)
    { $xxo=sin(($aa0+$aa1+360)*pi()/360)*$theOffset;
      $yyo=-cos(($aa0+$aa1+360)*pi()/360)*$theOffset;
    }
    if ($aa0==$aa1)
    { if ($theAngle0==$theAngle1) return;
      $aa1+=360;
    }
    imagefilledarc($this->Img, round($theXCenter+$xxo), round($theYCenter+$yyo), 2*$theRadius, 2*$theRadius, $aa0+270, $aa1+270, $cc, IMG_ARC_PIE);
    if (($theTooltipText!="")||($theOnClickAction!="")||($theOnMouseoverAction!="")||($theOnMouseoutAction!=""))
    { if ($aa0==$aa1)
        $tmpMapData="<area shape=circle coords='".$theXCenter.",".$theYCenter.",".$theRadius."' ";
      else
      { $tmpMapData="<area shape=polygon coords='".round($theXCenter+$xxo).",".round($theYCenter+$yyo);
        if ($aa0<$aa1) $dd=$aa1-$aa0;
        else $dd=$aa1-$aa0+360;
        $nn=round($dd/20)+1;
        for ($ii=0; $ii<=$nn; $ii++)
        { $aa=($aa0+$dd*$ii/$nn)%360;
          $tmpMapData.=",".round($theXCenter+$xxo+sin($aa*pi()/180)*$theRadius);
          $tmpMapData.=",".round($theYCenter+$yyo-cos($aa*pi()/180)*$theRadius);
        }
        $tmpMapData.="' ";
      }
      if ($theTooltipText!="") $tmpMapData=$tmpMapData." title='".$theTooltipText."' alt='".$theTooltipText."'";
      if ($theOnClickAction!="") $tmpMapData=$tmpMapData." href='javascript:".$theOnClickAction."'";
      if ($theOnMouseoverAction!="") $tmpMapData=$tmpMapData." onMouseover='".$theOnMouseoverAction."'";
      if ($theOnMouseoutAction!="") $tmpMapData=$tmpMapData." onMouseout='".$theOnMouseoutAction."'";
      $tmpMapData=$tmpMapData.">\n";
      $this->ImgMapData=$tmpMapData.$this->ImgMapData;
    }
  }
}
?>