<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL to Barcode Generator</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 to-gray-100 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden w-full max-w-md">
        <!-- Header dengan gradien -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-center">
            <h1 class="text-2xl font-bold text-white">🔗 URL to Barcode Generator</h1>
            <p class="text-blue-100 mt-1 text-sm">Ubah URL menjadi barcode</p>
        </div>

        <div class="p-6 space-y-6">
            <form action="{{ route('generate') }}" method="POST" class="space-y-4">
                @csrf

                @if (old('type') === 'EAN13')
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                        <p class="text-yellow-700 text-sm">⚠️ EAN13 requires 13 numeric digits (example: 8991234567890)
                        </p>
                    </div>
                @endif

                <div class="space-y-2">
                    <label for="url" class="block text-sm font-medium text-gray-700">Enter URL</label>
                    <input type="url" name="url" placeholder="https://example.com" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        value="{{ old('url') }}">
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 transform hover:-translate-y-0.5 shadow-md">
                    Generate
                </button>
            </form>

            @isset($barcode)
                <div class="mt-2 space-y-2">
                    <p class="text-xs text-gray-500 text-center">Generated Barcode:</p>

                    <!-- Barcode container dengan padding simetris -->
                    <div class="flex justify-center p-1 bg-white rounded border border-gray-200 mx-auto"
                        style="max-width: 100%; overflow: hidden;">
                        <div class="flex items-center justify-center w-full">
                            {!! $barcode !!}
                        </div>
                    </div>

                    <!-- URL text -->
                    <p class="text-xs text-center text-blue-600 break-all px-1">{{ $url }}</p>

                    @if ($downloadable)
                        <div class="pt-1 text-center">
                            <a href="{{ route('download-barcode', ['type' => $type, 'url' => urlencode($url)]) }}"
                                class="inline-flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white text-xs py-1 px-2 rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                Download
                            </a>
                        </div>
                    @endif
                </div>
            @endisset
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 text-center border-t border-gray-200">
            <p class="text-xs text-gray-500">© {{ date('Y') }} Url to Barcode by Ari Zainal Fauziah</p>
        </div>
    </div>
</body>

</html>
