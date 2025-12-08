<?php

namespace App\Libraries\PDF;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;

/**
 * PDF Generator Service
 * 
 * Handles PDF generation using mPDF library
 */
class PDFGenerator
{
    protected $mpdf;
    protected $config;
    protected $defaultConfig = [
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 16,
        'margin_bottom' => 16,
        'margin_header' => 9,
        'margin_footer' => 9,
    ];

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->defaultConfig, $config);
        $this->initialize();
    }

    /**
     * Initialize mPDF instance
     */
    protected function initialize()
    {
        $this->mpdf = new Mpdf($this->config);
        
        // Set document properties
        $this->mpdf->SetTitle('Document');
        $this->mpdf->SetAuthor('Aplikasi Perjadin');
        $this->mpdf->SetCreator('Aplikasi Perjadin System');
        $this->mpdf->SetSubject('Generated Document');
    }

    /**
     * Generate PDF from HTML
     * 
     * @param string $html
     * @return self
     */
    public function generate(string $html): self
    {
        $this->mpdf->WriteHTML($html);
        return $this;
    }

    /**
     * Set custom header
     * 
     * @param string $html
     * @param string $side 'O' = odd pages, 'E' = even pages, '' = all
     * @return self
     */
    public function setHeader(string $html, string $side = ''): self
    {
        $this->mpdf->SetHTMLHeader($html, $side);
        return $this;
    }

    /**
     * Set custom footer
     * 
     * @param string $html
     * @param string $side 'O' = odd pages, 'E' = even pages, '' = all
     * @return self
     */
    public function setFooter(string $html, string $side = ''): self
    {
        $this->mpdf->SetHTMLFooter($html, $side);
        return $this;
    }

    /**
     * Add watermark to document
     * 
     * @param string $text
     * @param float $alpha Transparency (0-1)
     * @param int $angle Rotation angle
     * @return self
     */
    public function setWatermark(string $text, float $alpha = 0.2, int $angle = 45): self
    {
        $this->mpdf->SetWatermarkText($text, $alpha);
        $this->mpdf->showWatermarkText = true;
        $this->mpdf->watermarkTextAlpha = $alpha;
        $this->mpdf->watermark_font = 'DejaVuSansCondensed';
        $this->mpdf->watermarkAngle = $angle;
        
        return $this;
    }

    /**
     * Add watermark image
     * 
     * @param string $imagePath
     * @param float $alpha Transparency (0-1)
     * @param string $size 'D' = default, 'P' = print size, 'F' = fullpage
     * @return self
     */
    public function setWatermarkImage(string $imagePath, float $alpha = 0.2, string $size = 'D'): self
    {
        if (file_exists($imagePath)) {
            $this->mpdf->SetWatermarkImage($imagePath, $alpha, $size);
            $this->mpdf->showWatermarkImage = true;
        }
        
        return $this;
    }

    /**
     * Output PDF
     * 
     * @param string $filename
     * @param string $mode 'I' = inline, 'D' = download, 'F' = save to file, 'S' = return as string
     * @return mixed
     */
    public function output(string $filename, string $mode = 'I')
    {
        $destination = match($mode) {
            'I' => Destination::INLINE,
            'D' => Destination::DOWNLOAD,
            'F' => Destination::FILE,
            'S' => Destination::STRING_RETURN,
            default => Destination::INLINE
        };

        return $this->mpdf->Output($filename, $destination);
    }

    /**
     * Add new page
     * 
     * @param string $orientation 'P' = portrait, 'L' = landscape
     * @return self
     */
    public function addPage(string $orientation = ''): self
    {
        if ($orientation) {
            $this->mpdf->AddPageByArray([
                'orientation' => $orientation
            ]);
        } else {
            $this->mpdf->AddPage();
        }
        
        return $this;
    }

    /**
     * Set page size
     * 
     * @param string $size A4, A3, Letter, Legal, etc
     * @return self
     */
    public function setPageSize(string $size): self
    {
        $this->mpdf->AddPageByArray([
            'sheet-size' => $size
        ]);
        
        return $this;
    }

    /**
     * Set page orientation
     * 
     * @param string $orientation 'P' = portrait, 'L' = landscape
     * @return self
     */
    public function setOrientation(string $orientation): self
    {
        $this->mpdf->AddPageByArray([
            'orientation' => $orientation
        ]);
        
        return $this;
    }

    /**
     * Set page margins
     * 
     * @param int $top
     * @param int $right
     * @param int $bottom
     * @param int $left
     * @return self
     */
    public function setMargins(int $top, int $right, int $bottom, int $left): self
    {
        $this->mpdf->AddPageByArray([
            'margin-top' => $top,
            'margin-right' => $right,
            'margin-bottom' => $bottom,
            'margin-left' => $left
        ]);
        
        return $this;
    }

    /**
     * Set document title
     * 
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->mpdf->SetTitle($title);
        return $this;
    }

    /**
     * Set document author
     * 
     * @param string $author
     * @return self
     */
    public function setAuthor(string $author): self
    {
        $this->mpdf->SetAuthor($author);
        return $this;
    }

    /**
     * Set document subject
     * 
     * @param string $subject
     * @return self
     */
    public function setSubject(string $subject): self
    {
        $this->mpdf->SetSubject($subject);
        return $this;
    }

    /**
     * Set document keywords
     * 
     * @param string $keywords
     * @return self
     */
    public function setKeywords(string $keywords): self
    {
        $this->mpdf->SetKeywords($keywords);
        return $this;
    }

    /**
     * Add CSS stylesheet
     * 
     * @param string $css
     * @return self
     */
    public function addStylesheet(string $css): self
    {
        $this->mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        return $this;
    }

    /**
     * Load CSS from file
     * 
     * @param string $filepath
     * @return self
     */
    public function loadStylesheet(string $filepath): self
    {
        if (file_exists($filepath)) {
            $css = file_get_contents($filepath);
            $this->addStylesheet($css);
        }
        
        return $this;
    }

    /**
     * Enable/disable TOC (Table of Contents)
     * 
     * @param bool $enable
     * @return self
     */
    public function enableTOC(bool $enable = true): self
    {
        if ($enable) {
            $this->mpdf->h2toc = ['H1' => 0, 'H2' => 1, 'H3' => 2];
        }
        
        return $this;
    }

    /**
     * Add bookmark
     * 
     * @param string $text
     * @param int $level 0-9
     * @return self
     */
    public function addBookmark(string $text, int $level = 0): self
    {
        $this->mpdf->Bookmark($text, $level);
        return $this;
    }

    /**
     * Set protection
     * 
     * @param array $permissions ['copy', 'print', 'modify', 'annot-forms']
     * @param string|null $userPassword
     * @param string|null $ownerPassword
     * @return self
     */
    public function setProtection(array $permissions = [], ?string $userPassword = null, ?string $ownerPassword = null): self
    {
        $this->mpdf->SetProtection($permissions, $userPassword, $ownerPassword);
        return $this;
    }

    /**
     * Get page count
     * 
     * @return int
     */
    public function getPageCount(): int
    {
        return $this->mpdf->page;
    }

    /**
     * Reset instance (create new PDF)
     * 
     * @return self
     */
    public function reset(): self
    {
        $this->initialize();
        return $this;
    }

    /**
     * Get mPDF instance (for advanced usage)
     * 
     * @return Mpdf
     */
    public function getMpdf(): Mpdf
    {
        return $this->mpdf;
    }

    /**
     * Create instance with fluent interface
     * 
     * @param array $config
     * @return self
     */
    public static function create(array $config = []): self
    {
        return new self($config);
    }
}