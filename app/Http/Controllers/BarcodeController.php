<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BarcodeController extends Controller
{
    public function index()
    {
        return view('barcode');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'url' => 'required',
        ]);

        $url  = urldecode($request->url);
        $type = 'qrcode';

        $barcode = match ($type) {
            'qrcode' => QrCode::format('svg')
                ->size(400)
                ->margin(4)
                ->generate($url),
            'C128', 'EAN13' => DNS1D::getBarcodeHTML($url, $type),
        };

        return view('barcode', [
            'barcode'      => $barcode,
            'url'          => $url,
            'type'         => $type,
            'downloadable' => true,
        ]);
    }

    public function download(Request $request)
    {
        $type = 'qrcode';
        $url  = urldecode($request->query('url'));

        // Create barcode image
        if ($type === 'qrcode') {
            $barcodeImage = QrCode::format('png')
                ->size(400) // Ukuran sedikit dikurangi untuk ruang teks
                ->margin(4)
                ->generate($url);
        } elseif (in_array($type, ['C128', 'EAN13'])) {
            $generator    = new \Picqer\Barcode\BarcodeGeneratorPNG();
            $barcodeImage = $generator->getBarcode($url, $type === 'EAN13' ? $generator::TYPE_EAN_13 : $generator::TYPE_CODE_128);
        } else {
            abort(400, 'Unsupported barcode type');
        }

        // Create final image with text
        $barcode = imagecreatefromstring($barcodeImage);
        $width   = imagesx($barcode);
        $height  = imagesy($barcode);

        // Additional space for text (50px)
        $textHeight = 30;
        $finalImage = imagecreatetruecolor($width, $height + $textHeight);
        $white      = imagecolorallocate($finalImage, 255, 255, 255);
        $black      = imagecolorallocate($finalImage, 0, 0, 0);

        // Fill background
        imagefill($finalImage, 0, 0, $white);

        // Copy barcode to final image
        imagecopy($finalImage, $barcode, 0, 0, 0, 0, $width, $height);

                        // Add text
        $font      = 4; // Built-in GD font (1-5)
        $textWidth = imagefontwidth($font) * strlen($url);
        $x         = ($width - $textWidth) / 2;
        imagestring($finalImage, $font, $x, $height + 5, $url, $black);

        // Output image
        ob_start();
        imagepng($finalImage);
        $image = ob_get_clean();

        // Clean up
        imagedestroy($barcode);
        imagedestroy($finalImage);

        return Response::make($image, 200, [
            'Content-Type'        => 'image/png',
            'Content-Disposition' => 'attachment; filename="barcode-with-url.png"',
        ]);
    }
}
