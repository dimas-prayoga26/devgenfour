<?php

namespace App\Support;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\HtmlString;

/**
 * Draws QR codes as inline SVG, which stays sharp when a certificate is printed.
 */
final class QrCode
{
    /**
     * Render the content as an SVG QR code of the given size in pixels, without the XML declaration so it can be inlined.
     *
     * Error correction level M keeps a printed code readable when it is slightly smudged or creased.
     */
    public static function svg(string $content, int $size = 160): HtmlString
    {
        $writer = new Writer(new ImageRenderer(new RendererStyle($size, 0), new SvgImageBackEnd));
        $svg = $writer->writeString($content, Encoder::DEFAULT_BYTE_MODE_ENCODING, ErrorCorrectionLevel::M());

        return new HtmlString(trim(substr($svg, strpos($svg, '<svg'))));
    }
}
