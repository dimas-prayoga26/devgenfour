<?php

namespace App\Support;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * Draws QR codes as SVG files, which stay sharp at any size on a printed certificate.
 */
final class QrCode
{
    /**
     * Render the content as an SVG document of the given size in pixels.
     *
     * The four-module white margin is the quiet zone scanners need, and error correction level M keeps
     * a printed code readable when it is slightly smudged or creased.
     */
    public static function svg(string $content, int $size = 600): string
    {
        $writer = new Writer(new ImageRenderer(new RendererStyle($size, 4), new SvgImageBackEnd));

        return $writer->writeString($content, Encoder::DEFAULT_BYTE_MODE_ENCODING, ErrorCorrectionLevel::M());
    }
}
