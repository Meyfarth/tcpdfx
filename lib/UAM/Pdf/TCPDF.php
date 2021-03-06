<?php
namespace UAM\Pdf;

$error_reporting = error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);

use \fpdi\FPDI;

include_once dirname(__FILE__) . '/../../../config/tcpdf_config.php';

class TCPDF extends FPDI
{
    protected $bottom_margin = 25;

    protected $letterhead;
    protected $letterhead_idx;

    protected $watermark = null;
    protected $watermark_font = array('Verdana', 'B', 58);
    protected $watermark_color = array(255, 205, 205);
    protected $watermark_cell_height = 10;

    protected $pagination_in_header = 'p. %page%/%pages%';
    protected $pagination_in_footer = false;
    protected $pagination_font = array('Verdana', '', 8);
    protected $pagination_x;
    protected $pagination_y;

    protected $document_title_font = array('Georgia', 'B', 16);
    protected $document_title_border = 'B';
    protected $document_title_align = 'C';
    protected $document_title_cell_height = 12;

    protected $title_font = 'Verdana';
    protected $title_size = 11;
    protected $title_style = 'B';
    protected $title_cell_height = 6;
    protected $title_align = 'L';

    protected $subtitle_font = 'Verdana';
    protected $subtitle_size = 11;
    protected $subtitle_style = 'B';
    protected $subtitle_cell_height = 6;


    protected $default_cell_padding = array('L' => 0.3, 'T' => 0.3, 'R' => 0.3, 'B' => 0.3);

    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false)
    {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);

        $this->init();
    }

    /**
     * Returns the name to be used by default for this document when output.
     *
     * When the Output method is called with a null $name parameter, this method
     * is invoked to supply a default document name.
     *
     * By default, this method simply returns 'doc'pdf'. Override this method
     * according to your requirements.
     */
    public function getName()
    {
        return 'doc.pdf';
    }

    /**
     * Returns the path to the file to be used as letterhead.
     */
    public function getLetterhead()
    {
        return $this->letterhead;
    }

    /**
     * Sets the path to the file to be used as letterhead.
     */
    public function setLetterhead($letterhead)
    {
        $this->letterhead = $letterhead;
    }

    /**
     * Returns the text to use as watermark.
     *
     * @return string
     */
    public function getWatermark()
    {
        return $this->watermark;
    }

    /**
     * Sets the text of the watermark.
     *
     * @param $watermark
     */
    public function setWatermark($watermark)
    {
        $this->watermark = $watermark;
    }

    public function getWatermarkFont()
    {
        return $this->watermark_font;
    }

    public function setWatermarkFont(array $font)
    {
        $this->watermark_font = $font;
    }

    public function getWatermarkColor()
    {
        return $this->watermark_color;
    }

    public function setWatermarkColor(array $color)
    {
        $this->watermark_color = $color;
    }

    /**
     * If not false or null, returns the format for the pagination in the page header.
     */
    public function getPaginationInHeader()
    {
        return $this->pagination_in_header;
    }

    /**
     * Sets the format for the pagination in the header, or null/false to cancel pagination in the header.
     */
    public function setPaginationInHeader($format = null)
    {
        $this->pagination_in_header = $format;
    }

    /**
     * If not false or null, returns the format for the pagination in the page footer.
     */
    public function getPaginationInFooter()
    {
        return $this->pagination_in_footer;
    }

    /**
     * Sets the format for the pagination in the footer, or null/false to cancel pagination in the footer.
     */
    public function setPaginationInFooter($format = null)
    {
        $this->pagination_in_footer = $format;
    }

    public function getPaginationFont()
    {
        return $this->pagination_font;
    }

    public function setPaginationFont(array $font)
    {
        $this->pagination_font = $font;
    }

    public function getDocumentTitleFont()
    {
        return $this->document_title_font;
    }

    public function setDocumentTitleFont(array $font)
    {
        $this->document_title_font = $font;
    }

    public function getDocumentTitleBorder()
    {
        return $this->document_title_border;
    }

    public function setDocumentTitleBorder($border)
    {
        $this->document_title_border = $border;
    }

    public function getDocumentTitleAlign()
    {
        return $this->document_title_align;
    }

    public function setDocumentTitleAlign($align)
    {
        $this->document_title_align = $align;
    }

    public function getDocumentTitleCellHeight()
    {
        return $this->document_title_cell_height;
    }

    public function setDocumentTitleCellHeight($height)
    {
        $this->document_title_cell_height = $height;
    }
    /**
     * Do not override. Override 'generate' method instead.
     *
     */
    public function Output($name = null, $dest = 'D')
    {
        $this->init();

        $this->AddPage();

        $this->generate();

        parent::Output($name ? $name : $this->getName(), $dest);

        header_remove('Content-Length');
    }

    /**
     * Generates the header.
     *
     *
     */
    public function Header()
    {
        $this->addLetterhead();
        $this->addWatermark();

        $this->SetY(max($this->GetY(), $this->getHeaderMargin()));
        $this->printPageHeader();

        if ($this->getPaginationInHeader()) {
            $this->paginate();
        }
    }

    public function Footer()
    {
        $cur_y = $this->y;

        $this->SetTextColorArray($this->footer_text_color);

        $this->printPageFooter();

        if ($this->getPaginationInFooter()) {
            $this->paginate();
        }

        $this->SetY($cur_y);
    }

    public function resetFont()
    {
        $this->SetFont($this->FontFamily, $this->FontStyle, $this->FontSizePt);
    }

    /*
    public function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false)
    {
        $old_cell_padding = $this->GetCellPaddings();
        $this->setCellPaddings($this->default_cell_padding['L'],
                                                     $this->default_cell_padding['T'],
                                                     $this->default_cell_padding['R'],
                                                     $this->default_cell_padding['B']);
        parent::MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
        $this->SetCellPaddings($old_cell_padding['L'], $old_cell_padding['T'], $old_cell_padding['R'], $old_cell_padding['B']);
    }
    */

    protected function init()
    {
        $this->SetAutoPageBreak(true, $this->bottom_margin);
    }

    protected function generate() {}

    /**
     * Adds the document;s letterhead.
     */
    protected function addLetterhead()
    {
        $letterhead = $this->getLetterhead();

        if (!$letterhead || !file_exists($letterhead) || is_dir($letterhead)) {
            return;
        }

        if (is_null($this->letterhead_idx)) {
            $this->setSourceFile($letterhead);
            $this->letterhead_idx = $this->importPage(1);
        }

        $this->useTemplate($this->letterhead_idx);
    }

    /**
     * Prints the document's watermark.
     * TODO [OP 2013-12-29] Add rtl support
     * TODO [OP 2013-12-29] Use XObject template
     */
    protected function addWatermark()
    {
        $watermark = $this->getWatermark();

        if (!is_array($watermark) || empty($watermark)) {
            return;
        }

        $color = $this->TextColor;
        $x = $this->GetX();
        $y = $this->getY();

        $font = $this->getWatermarkFont();

        $this->SetFont($font[0], $font[1], $font[2]);

        $this->SetTextColorArray($this->getWatermarkColor());

        $this->setXY($this->lMargin, $this->h - $this->bMargin);

        $this->StartTransform();

        if ($this->CurOrientation == 'P') {
            $this->Rotate(60);
            $w = $this->w * 1.5;
        } else {
            $this->Rotate(30);
            $w = $this->h * 1.5;
        }

        $this->MultiCell(
            $w,
            $this->watermark_cell,
            $watermark,
            0, 'C', 0, 0
        );

        $this->StopTransform();

        $this->SetXY($x, $y);
        $this->resetFont();
        $this->TextColor = $color;
    }

    protected function printPageHeader()
    {

    }

    protected function printPageFooter()
    {

    }

    protected function paginate()
    {
        // save current graphic settings
        $gvars = $this->getGraphicVars();

        $font = $this->getPaginationFont();

        $this->SetFont($font[0], $font[1], $font[2]);

        $this->SetX(
            $this->pagination_x
            ? $this->pagination_x
            : ($this->getRTL() ? $this->original_rMargin : $this->original_lMargin)
        );

        if ($this->pagination_y) {

            $this->SetY($this->pagination_y);

        } elseif ($this->InHeader) {

            $this->SetY(max($this->getHeaderMargin(), $this->GetY()));

        } else if ($this->InFooter) {

            $this->SetY($this->h - $this->footer_margin);

        }

        if ($this->getRTL()) {
            $this->Cell(0, 0, $this->getPagination(), 0, 0, 'L');
        } else {
            $this->Cell(0, 0, $this->getAliasRightShift() . $this->getPagination(), 0, 0, 'R');
        }

        // restore graphic settings
        $this->setGraphicVars($gvars);
    }

    protected function getPagination()
    {
        $params = empty($this->pagegroups)
            ? array(
                '%page%' => $this->getAliasNumPage(),
                '%pages%' => $this->getAliasNbPages()
            )
            : array(
                '%page%' => $this->getPageNumGroupAlias(),
                '%pages%' => $this->getPageGroupAlias()
            );

        return strtr(
            $this->InHeader
                ? $this->getPaginationInHeader()
                : $this->getPaginationInFooter(),
            $params
        );
    }

    protected function _cell($s, $link = '')
    {
        $this->Cell($this->getStringWidth($s), 0, $s, 0, 0, 'L', 0, $link);
    }

    //public function getCellPadding() { return $this->cMargin; }

    public function printDocumentTitle()
    {
        $title = $this->getDocumentTitle();

        if (empty($title)) {
            return;
        }

        // save current graphic settings
        $gvars = $this->getGraphicVars();

        $font = $this->getDocumentTitleFont();

        $this->SetFont($font[0], $font[1], $font[2]);

        $this->MultiCell(
            0,
            $this->getDocumentTitleCellHeight(),
            $title,
            $this->getDocumentTitleBorder(),
            $this->getDocumentTitleAlign(),
            0,
            1
        );

        // restore graphic settings
        $this->setGraphicVars($gvars);
    }

    public function printTitle($title)
    {
        $this->SetFont($this->title_font, $this->title_style, $this->title_size);
        $this->MultiCell(0, $this->title_cell_height, $title, 0, $this->title_align, 0, 1);
        $this->resetFont();
    }

    public function printText($text, $multiline = false)
    {
        $this->resetFont();
        if (!$multiline) {
            $this->Cell(0, 0, $text, 0, 1, 'L');
        } else {
            $this->MultiCell(0, 0, $text, '', 'L', 0, 1);
        }
    }

    // MultiCell with bullet
    public function MultiCellBlt($w, $h, $blt, $txt, $border = 0, $align = 'J', $fill = 0, $ln = 1)
    {
    //  $font = $this->FontFamily;
    //  $style = $this->FontStyle;
    //  $size = $this->getFontSizePt();
        // $this->setFont('zapfdingbats', '', $size);

        //Get bullet width including margins
        $paddings = $this->getCellPaddings();
        $blt_width = $this->GetStringWidth($blt) + $paddings['L'] + $paddings['R'];

        //Save x
        $bak_x = $this->x;

        //Output bullet
//    $this->Cell($blt_width, $h, $this->unichr(0x0076) , 0, '', $fill);
        $this->Cell($blt_width, $h, $this->unichr(0x2022) , 0, '', $fill);
//    $this->SetFont($font, $style, $size);
        //Output text
        $this->MultiCell($w - $blt_width, $h, $txt, $border, $align, $fill, 1);

        //Restore x
        $this->x = $bak_x;
    }

    public function unichr($c)
    {
        if ($c <= 0x7F) { return chr($c); } elseif ($c <= 0x7FF) { return chr(0xC0 | $c >> 6) . chr(0x80 | $c & 0x3F); } elseif ($c <= 0xFFFF) {
                return chr(0xE0 | $c >> 12) . chr(0x80 | $c >> 6 & 0x3F)
                                                                        . chr(0x80 | $c & 0x3F);
        } elseif ($c <= 0x10FFFF) {
                return chr(0xF0 | $c >> 18) . chr(0x80 | $c >> 12 & 0x3F)
                                                                        . chr(0x80 | $c >> 6 & 0x3F)
                                                                        . chr(0x80 | $c & 0x3F);
        } else { return false; }
    }

    /**
     * TODO [OP 2010-06-24] Refactor method as per docs below.
     * @param Array $data           a multi-dimensional array containing the cell values. Required.
     * @param mixed $width          either an integer representing the width of all columns,
     *                              or an array containing the widths of each column. Required.
     * @param mixed $align          either a single value representing the alignment applicable
     *                              to the cells of all columns, or an array containing the
     *                              alignment for each column. Allowed values: 'L', 'R', 'C'
     *                              default 'L'.
     * @param mixed $border         either a single value defining the borders applicable to
     *                              the cells of all columns, or an array containg the borders
     *                              for each column. Required.
     * @param mixed $font           either a single value indicating the font of the cells of
     *                              all columns or an array defining the fonts for each column.
     *                              Required.
     * @param mixed $font_style     either a single value indicating the font style of
     *                              the cells of all columns or an array defining the font
     *                              styles for each column. Required.
     * @param mixed $font_size      either a single value indicating the font size of the
     *                              cells of all columns or an array defining the font size
     *                              for each column. Required.
     * @param int   $cell_height.   Required.
     * @param int   $min_row_height minimum row height (default 0)
     * @param Array $headers        an array of arrays containing the header information. Each array
     *                              defines a header row starting from the top (0 => first header row, etc.). Each header
     *                              row array is an associative array defining the following keys:
     *                              'data' => the content of the header cells. Required.
     *                              'width => widths of the header cells. Required.
     *                              'align' => alignments of header cells. Default 'C'.
     *                              'border' => borders of header cells. Required.
     *                              'font' => font family of header cells. Required.
     *                              'font_style' => font style of header cells. Required.
     *                              'font_size' => font size of header cells. Required.
     *                              'cell_height' => cell height. Required.
     *                              'min_row_height' => minimum row height of header row (default 0)
     *                              'fill' => fill values
     *                              All values except for keys 'data' and 'min_row_height' are either single values
     *                              (applying to all header cells) or an array containing values for each cell.
     */
    public function generateTable($data, $width, $align = 'L', $border, $font, $font_style, $font_size,
                                                                $cell_height, $min_row_height = 0,
                                                                $headers = array(), $footers = array(),
                                                                $fill = 0) {

        // print headers
        foreach ($headers as $h) {
            $this->__row($h['data'], $h['width'], $h['align'], $h['border'],
                                     $h['font'], $h['font_style'], $h['font_size'],
                                     $h['cell_height'], $h['min_row_height'], 0,
                                     isset($h['fill']) ? $h['fill'] : 0);
        }

        foreach ($data as $row) {
            // add a new page if necessary
            $height = $this->__row_height($row, $width, $align, $border, $font, $font_style, $font_size, $cell_height, $min_row_height);
            $dimensions = $this->getPageDimensions();
            if ($this->getY() + $height + $dimensions['bm'] >= $dimensions['hk']) {
                $x0 = $this->GetX();
                $this->addPage();
                $this->SetX($x0);
                // reprint the headers
                foreach ($headers as $h) {
                    $this->__row($h['data'], $h['width'], $h['align'], $h['border'],
                                             $h['font'], $h['font_style'], $h['font_size'],
                                             $h['cell_height'], $h['min_row_height'], 0,
                                             isset($h['fill']) ? $h['fill'] : 0);
                }
            }

            $this->__row($row, $width, $align, $border,
                                     $font, $font_style, $font_size,
                                     $cell_height, $min_row_height, $height, $fill);
        }

        // print footers
        foreach ($footers as $f) {
            $this->__row($f['data'], $f['width'], $f['align'], $f['border'],
                                     $f['font'], $f['font_style'], $f['font_size'],
                                     $f['cell_height'], $f['min_row_height'],
                                     isset($f['fill']) ? $f['fill'] : 0);
        }
    }

    /**
     * This function calculate table height
     *
     * @param  array $data
     * @param  mixed $width
     * @param  mixed $font
     * @param  mixed $font_style
     * @param  mixed $font_size
     * @param  int   $cell_height
     * @param  array $headers
     * @param  array $footers
     * @return int
     */
    public function getTableHeight($data, $width, $border, $font, $font_style, $font_size,
                                                                 $cell_height, $min_row_height = 0, $headers = array(), $footers = array()) {

        $height = 0;
        foreach ($headers as $h) {
            $height += $this->__row_height($h['data'], $h['width'], $h['align'],
                                                                         $h['border'], $h['font'],
                                                                         $h['font_style'], $h['font_size'],
                                                                         $h['cell_height'], $h['min_row_height']);
        }

        foreach ($data as $row) {
            $height += $this->__row_height($row, $width, '',
                                                                         $border, $font,
                                                                         $font_style, $font_size,
                                                                         $cell_height, $min_row_height);
        }

        foreach ($footers as $f) {
            $height += $this->__row_height($f['data'], $f['width'], $f['align'],
                                                                         $f['border'], $f['font'],
                                                                         $f['font_style'], $f['font_size'],
                                                                         $f['cell_height'], $f['min_row_height']);
        }

        return $height;
    }

    protected function __row($data, $width, $align = 'C', $border, $font, $font_style, $font_size, $cell_height, $min_height = 0, $height = 0, $fill = 0)
    {
        $x0 = $this->GetX();
        $y0 = $this->GetY();

        $x1 = $x0;

        if ($height == 0) {
            $height = $this->__row_height($data, $width, $align, $border, $font, $font_style, $font_size, $cell_height, $min_height);
        }

        $n = count($data);

        for ($i = 0; $i < $n; $i++) {
            $this->SetXY($x1, $y0);
            $this->SetFont(is_array($font) ? $font[$i] : $font,
                                         is_array($font_style) ? $font_style[$i] : $font_style,
                                         is_array($font_size) ? $font_size[$i] : $font_size);
            $this->MultiCell(is_array($width) ? $width[$i] : $width,
                                             $height,
                                             $data[$i],
                                             is_array($border) ? $border[$i] : $border,
                                             is_array($align) ? $align[$i] : $align,
                                             is_array($fill) ? $fill[$i] : $fill,
                                             1);

                $x1 += is_array($width) ? $width[$i] : $width;
                $height = max($height, $this->getY() - $y0);
        }

        $this->SetXY($x0, $y0 + $height);
    }

/**
     * This function calculate row height
     *
     * @param  array $data
     * @param  mixed $width
     * @param  mixed $font
     * @param  mixed $font_style
     * @param  mixed $font_size
     * @param  int   $cell_height
     * @return int
     */
    protected function __row_height($data, $width, $align, $border, $font, $font_style, $font_size, $cell_height, $min_height = 0)
    {
        $lines = array();
        $height = array($cell_height, $min_height);
        $n = count($data);
        $w = is_array($width) ? null : $width;
        for ($i = 0; $i < $n; $i++) {
            $this->SetFont(is_array($font) ? $font[$i] : $font,
                                         is_array($font_style) ? $font_style[$i] : $font_style,
                                         is_array($font_size) ? $font_size[$i] : $font_size);
            $height[] = $this->getStringHeight($w ? $w : $width[$i], $data[$i], false, true, $this->cell_padding, $border);
        }

        return max($height);
    }

    public function sanitizeName($string = '')
    {
        $string = strtr(utf8_decode($string),
                                        utf8_decode("()!$'?: ,&+-/.ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ"),
                                        "--------------SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
/*
        htmlentities($string);
        $string = preg_replace("/&([a-z])[a-z]+;/i", "$1", $string);
*/
        // Replace all weird characters with dashes
//    $string = preg_replace('/[^\w\-]+/u', '-', $string);
        // Only allow one dash separator at a time (and make string lowercase)
        return mb_strtolower(preg_replace('/--+/u', '-', $string), 'UTF-8');
    }
}

error_reporting($error_reporting);
