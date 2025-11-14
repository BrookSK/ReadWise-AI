<?php
/*************************************************************************
* FPDF                                                                         
* Version: 1.86 (stripped header)                                             
* Minimal single-file FPDF library (original by Olivier Plathey)              
* Source: http://www.fpdf.org/                                                
*************************************************************************/
if(class_exists('FPDF')) return;
class FPDF
{
protected $page,$n,$offsets,$buffer,$pages,$state,$compress,$k,$DefOrientation,$CurOrientation,$StdPageSizes,$DefPageSize,$CurPageSize,$PageSizes,$wPt,$hPt,$w,$h,$lMargin,$tMargin,$rMargin,$bMargin,$cMargin,$x,$y,$lasth,$LineWidth,$fontpath,$CoreFonts,$fonts,$FontFiles,$encodings,$cmaps,$FontFamily,$FontStyle,$underline,$CurrentFont,$FontSizePt,$FontSize,$DrawColor,$FillColor,$TextColor,$ColorFlag,$ws,$AutoPageBreak,$PageBreakTrigger,$InHeader,$InFooter,$ZoomMode,$LayoutMode,$title,$subject,$author,$keywords,$creator,$AliasNbPages,$PDFVersion;
function __construct($orientation='P',$unit='mm',$size='A4')
{
    $this->page=0;$this->n=2;$this->buffer='';$this->pages=array();$this->PageSizes=array();$this->state=0;$this->fonts=array();$this->FontFiles=array();$this->encodings=array();$this->cmaps=array();$this->FontFamily='';$this->FontStyle='';$this->FontSizePt=12;$this->underline=false;$this->DrawColor='0 G';$this->FillColor='0 g';$this->TextColor='0 g';$this->ColorFlag=false;$this->ws=0;
    if($unit=='pt') $this->k=1; elseif($unit=='mm') $this->k=72/25.4; elseif($unit=='cm') $this->k=72/2.54; elseif($unit=='in') $this->k=72; else $this->Error('Incorrect unit: '.$unit);
    $this->StdPageSizes=array('a3'=>array(841.89,1190.55),'a4'=>array(595.28,841.89),'a5'=>array(420.94,595.28),'letter'=>array(612,792),'legal'=>array(612,1008));
    $size=$this->_getpagesize($size);$this->DefPageSize=$size;$this->CurPageSize=$size;
    $this->DefOrientation=strtoupper($orientation);$this->CurOrientation=$this->DefOrientation;
    $this->wPt=$size[0];$this->hPt=$size[1];$this->w=$this->wPt/$this->k;$this->h=$this->hPt/$this->k;
    $margin=28.35/$this->k;$this->SetMargins($margin,$margin);
    $this->cMargin=$margin/10;$this->LineWidth=0.567/$this->k;
    $this->SetAutoPageBreak(true,2*$margin);
    $this->fontpath=__DIR__;
    $this->CoreFonts=array('courier'=>'Courier','helvetica'=>'Helvetica','times'=>'Times','symbol'=>'Symbol','zapfdingbats'=>'ZapfDingbats');
    $this->PDFVersion='1.3';
}
function SetMargins($left,$top,$right=null){$this->lMargin=$left;$this->tMargin=$top;$this->rMargin=$right===null?$left:$right;}
function SetAutoPageBreak($auto,$margin=0){$this->AutoPageBreak=$auto;$this->bMargin=$margin;$this->PageBreakTrigger=$this->h-$margin;}
function AddPage($orientation=''){if($this->state==0)$this->Open();$family=$this->FontFamily;$style=$this->FontStyle;$size=$this->FontSizePt;$lw=$this->LineWidth;$dc=$this->DrawColor;$fc=$this->FillColor;$tc=$this->TextColor;$cf=$this->ColorFlag;$this->page++;$this->pages[$this->page]='';$this->state=2;$this->x=$this->lMargin;$this->y=$this->tMargin;$this->FontFamily='';$this->FontStyle='';$this->FontSizePt=0;$this->underline=false;$this->DrawColor=$dc;$this->FillColor=$fc;$this->TextColor=$tc;$this->ColorFlag=$cf;$this->SetFont($family,$style,$size);$this->SetLineWidth($lw);}
function SetFont($family,$style='',$size=0){$family=strtolower($family);if($family=='arial')$family='helvetica';if(!isset($this->fonts[$family.$style])){$this->fonts[$family.$style]=array('i'=>count($this->fonts)+1,'name'=>$this->CoreFonts[$family]??'Helvetica');}
    $this->CurrentFont=&$this->fonts[$family.$style];if($size==0)$size=$this->FontSizePt;$this->FontSizePt=$size;$this->FontSize=$size/$this->k;}
function SetLineWidth($width){$this->LineWidth=$width;}
function SetDrawColor($r,$g=null,$b=null){$this->DrawColor=sprintf('%.3F G',$r/255);} 
function SetTextColor($r,$g=null,$b=null){$this->TextColor=sprintf('%.3F g',$r/255);} 
function SetFillColor($r,$g=null,$b=null){$this->FillColor=sprintf('%.3F g',$r/255);} 
function Ln($h=null){$this->y+=$h?:$this->lasth;$this->x=$this->lMargin;}
function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=false,$link=''){ $s=sprintf("BT /F%d %.2F Tf %.2F %.2F Td (%s) Tj ET",$this->CurrentFont['i'],$this->FontSizePt,$this->x*$this->k,($this->h-$this->y)*$this->k,$this->_escape($txt));$this->_out($s);$this->lasth=$h;$this->x+=$w;if($ln>0)$this->Ln($h);} 
function MultiCell($w,$h,$txt){$lines=explode("\n",wordwrap($txt,80));foreach($lines as $line){$this->Cell($w,$h,$line,0,1);} }
function SetTitle($title){$this->title=$title;}
function Output($dest='I',$name='doc.pdf'){if($this->state<3)$this->Close();$out=$this->buffer; if($dest=='F'){file_put_contents($name,$out);return '';} header('Content-Type: application/pdf'); header('Content-Length: '.strlen($out)); header('Content-Disposition: inline; filename="'.($name?:'document')."" ); echo $out; return '';} 
function Open(){ $this->state=1; $this->_begindoc(); }
function Close(){ if($this->state==3) return; $this->_enddoc(); $this->state=3; }
protected function _escape($s){return str_replace(['\\','(',' )',"\r"],["\\\\","\\(","\\)",""],$s);} 
protected function _textstring($s){return '('.$this->_escape($s).')';}
protected function _newobj(){ $this->n++; $this->offsets[$this->n]=strlen($this->buffer); $this->_out($this->n.' 0 obj'); }
protected function _out($s){$this->buffer.=$s."\n";}
protected function _putpages(){ $nb=$this->page; if($nb==0)$nb=1; $o=array(); for($n=1;$n<=$nb;$n++){ $this->_newobj(); $this->_out('<</Type /Page/Parent 1 0 R/Resources<</Font<</F'.$n.' '.$this->fonts['helvetica']['i'].' 0 R>>>>/MediaBox[0 0 '.$this->wPt.' '.$this->hPt.']>>'); $this->_out('endobj'); $o[$n]=$this->n; $this->_newobj(); $this->_out('<</Length 0>>'); $this->_out('stream'); $this->_out($this->pages[$n] ?? ''); $this->_out('endstream'); $this->_out('endobj'); }
 $this->offsets[1]=strlen($this->buffer); $this->_out('1 0 obj'); $kids=''; for($n=1;$n<=$nb;$n++) $kids.=$o[$n].' 0 R '; $this->_out('<</Type /Pages/Count '.$nb.'/Kids['.$kids.']>>'); $this->_out('endobj'); }
protected function _putresources(){ $this->_newobj(); $this->_out('<</Type /Font/Subtype /Type1/BaseFont /Helvetica>>'); $this->_out('endobj'); $this->fonts['helvetica']['i']=$this->n; $this->_putpages(); }
protected function _begindoc(){ $this->_out('%PDF-'.$this->PDFVersion); }
protected function _enddoc(){ $this->_putresources(); $this->_newobj(); $this->_out('<</Type /Catalog/Pages 1 0 R>>'); $this->_out('endobj'); $xref=strlen($this->buffer); $this->_out('xref'); $this->_out('0 '.($this->n+1)); $this->_out('0000000000 65535 f '); foreach($this->offsets as $ofs) $this->_out(sprintf('%010d 00000 n ',$ofs)); $this->_out('trailer'); $this->_out('<</Size '.($this->n+1).'/Root '.($this->n).' 0 R>>'); $this->_out('startxref'); $this->_out($xref); $this->_out('%%EOF'); }
}
?>
