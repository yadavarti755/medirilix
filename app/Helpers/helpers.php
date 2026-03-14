<?php

use App\Models\SiteSetting;

if (!function_exists('formatBytes')) {
    function formatBytes(int $bytes, int $precision = 2): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        $size = $bytes / pow(1024, $i);

        return round($size, $precision) . ' ' . $units[$i];
    }
}

if (!function_exists('getFileMeta')) {
    function getFileMeta($filePath)
    {
        $publicPrefix = asset('storage') . '/';
        $absolutePath = str_replace($publicPrefix, storage_path('app/public/'), $filePath);

        $exists = file_exists($absolutePath);

        $sizeBytes = $exists ? filesize($absolutePath) : 0;
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        //  Format size into human-readable string
        $formattedSize = '0 Bytes';
        if ($sizeBytes > 0) {
            $units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            $power = floor(log($sizeBytes, 1024));
            $formattedSize = number_format($sizeBytes / pow(1024, $power), 2) . ' ' . $units[$power];
        }

        return [
            'size' => $sizeBytes > 0 ? number_format($sizeBytes, 2) : '0.00',
            'formatted_size' => $formattedSize,
            'extension' => strtoupper($extension),
            'exists' => $exists,
        ];
    }
}

// Convert date and Make human readable date
function convertDate($date)
{
    if ($date) {
        return \Carbon\Carbon::parse($date)->translatedFormat('d F, Y');
    }
    return '';
}

function buildMenuTree($menus, $parentId = null)
{
    $branch = [];

    foreach ($menus as $menu) {
        if ($menu->parent_id == $parentId) {
            $children = buildMenuTree($menus, $menu->id);
            if ($children) {
                $menu->children = $children;
            }
            $branch[] = $menu;
        }
    }

    return $branch;
}

function renderMenuOptions($menus, $level = 0)
{
    foreach ($menus as $menu) {
        echo '<option value="' . $menu->id . '">' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $level) . e($menu->title) . '</option>';
        if (!empty($menu->children)) {
            renderMenuOptions($menu->children, $level + 1);
        }
    }
}

function renderMenuOptionsForEdit($menus, $selectedId = null, $level = 0)
{
    foreach ($menus as $menu) {
        $selected = $menu->id == $selectedId ? 'selected' : '';
        echo '<option value="' . $menu->id . '" ' . $selected . '>' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $level) . e($menu->title) . '</option>';
        if (!empty($menu->children)) {
            renderMenuOptionsForEdit($menu->children, $selectedId, $level + 1);
        }
    }
}

// Encrypt helper function
function customEncrypt($data)
{
    return urlencode(base64_encode(encrypt($data)));
}

// Decrypt helper function
function customDecrypt($data)
{
    return decrypt(base64_decode(urldecode($data)));
}


function convertNumberToIndianWords($number)
{
    $words = array(
        '0' => '',
        '1' => 'one',
        '2' => 'two',
        '3' => 'three',
        '4' => 'four',
        '5' => 'five',
        '6' => 'six',
        '7' => 'seven',
        '8' => 'eight',
        '9' => 'nine',
        '10' => 'ten',
        '11' => 'eleven',
        '12' => 'twelve',
        '13' => 'thirteen',
        '14' => 'fourteen',
        '15' => 'fifteen',
        '16' => 'sixteen',
        '17' => 'seventeen',
        '18' => 'eighteen',
        '19' => 'nineteen',
        '20' => 'twenty',
        '30' => 'thirty',
        '40' => 'forty',
        '50' => 'fifty',
        '60' => 'sixty',
        '70' => 'seventy',
        '80' => 'eighty',
        '90' => 'ninety'
    );

    $digits = ['', 'hundred', 'thousand', 'lakh', 'crore'];

    if ($number == 0) {
        return 'zero only';
    }

    $result = '';
    $number = (int)$number;
    $no = (string) $number;
    $len = strlen($no);
    $i = 0;

    while ($i < $len) {
        $divider = ($i == 2) ? 10 : 100;
        $pos = $len - $i;
        $chunk = substr($no, 0, $pos);
        $chunk = (int)$chunk;

        if ($chunk > 0) {
            $value = '';
            if ($chunk < 21) {
                $value = $words[$chunk];
            } elseif ($chunk < 100) {
                $value = $words[10 * floor($chunk / 10)] . ' ' . $words[$chunk % 10];
            } else {
                $value = $words[floor($chunk / 100)] . ' hundred';
                if ($chunk % 100 > 0) {
                    $value .= ' ' . convertNumberToIndianWords($chunk % 100);
                }
                $result .= $value . ' ';
                break;
            }

            $index = floor($i / 2) + 1;
            $result .= $value . ' ' . $digits[$index] . ' ';
        }

        $i += ($i == 2) ? 1 : 2;
        $no = substr($no, 0, $len - $i);
        $len = strlen($no);
    }

    return ucfirst(trim($result)) . ' only';
}


function obfuscateEmail($email)
{
    return str_replace(
        ['@', '.'],
        ['[at]', '[dot]'],
        $email
    );
}

function obfuscateEmailsInHtml($html)
{
    return preg_replace_callback(
        '/([a-zA-Z0-9._%+-]+)@([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/',
        function ($matches) {
            $obfuscated = str_replace(['@', '.'], ['[at]', '[dot]'], $matches[0]);
            return $obfuscated;
        },
        $html
    );
}


/**
 * Decrypt password using the same algorithm as frontend
 */
function decryptPassword($cipherTextBase64)
{
    $key = hex2bin(session('encryption_key'));
    $cipherText = base64_decode($cipherTextBase64);

    $iv = substr($cipherText, 0, 16); // First 16 bytes = IV
    $cipher = substr($cipherText, 16); // Remaining is the encrypted password

    $decrypted = openssl_decrypt(
        $cipher,
        'AES-128-CBC',
        $key,
        OPENSSL_RAW_DATA,
        $iv
    );

    return $decrypted;
}

function setEncryptionKey()
{
    $key = bin2hex(random_bytes(16)); // 128-bit AES key
    session(['encryption_key' => $key]);
    return $key;
}


function customURIEncode($data)
{
    return urldecode(base64_encode($data));
}

function customURIDecode($data)
{
    return base64_decode(urldecode($data));
}

function generate_file_view_path_for_public(string $path): string
{
    return route('public.file.view', [
        'code' => customURIEncode($path),
    ]);
}

function generate_file_view_path_for_backend(string $path): string
{
    return route('backend.file.view', [
        'code' => customURIEncode($path),
    ]);
}

function showFileToUser($filePath)
{
    // -------- 3. Convert it into an actual file-system path -----
    //  * Replace the app URL with the public_path()
    //  * Normalise the slashes if we’re on Windows
    $filePath = str_replace(
        url('/'),
        public_path(),      // = FCPATH in CodeIgniter
        $filePath
    );

    if (PHP_OS_FAMILY === 'Windows') {
        $filePath = str_replace('/', '\\', $filePath);
    }

    // -------- 4. Return or 404 ---------------------------------
    if (! file_exists($filePath)) {
        return abort(404);
    }

    // Laravel’s helper sets the MIME type automatically
    return response()->file($filePath);
}


// helpers.php or directly in your view composer
function findMenuPath($items, $currentUrl, $parents = [])
{
    foreach ($items as $item) {
        $itemUrl = url($item->url);

        if ($itemUrl === $currentUrl) {
            return array_merge($parents, [$item]);
        }

        if (!empty($item->children)) {
            $found = findMenuPath($item->children, $currentUrl, array_merge($parents, [$item]));
            if ($found) {
                return $found;
            }
        }
    }
    return null;
}

function maskEmail($email)
{
    $parts = explode('@', $email);
    $username = $parts[0];
    $domain = $parts[1];

    if (strlen($username) <= 2) {
        return $email;
    }

    $maskedUsername = substr($username, 0, 2) . str_repeat('*', strlen($username) - 2);
    return $maskedUsername . '@' . $domain;
}

function maskMobile($mobile)
{
    if (strlen($mobile) <= 4) {
        return $mobile;
    }

    return substr($mobile, 0, 2) . str_repeat('*', strlen($mobile) - 4) . substr($mobile, -2);
}

function renderCategoryOptions($categories, $level = 0)
{
    foreach ($categories as $category) {
        echo '<option value="' . $category->id . '">' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $level) . e($category->name) . '</option>';
        if (!empty($category->children)) {
            renderCategoryOptions($category->children, $level + 1);
        }
    }
}

function renderCategoryOptionsForEdit($categories, $selectedId = null, $level = 0)
{
    foreach ($categories as $category) {
        $selected = $category->id == $selectedId ? 'selected' : '';
        echo '<option value="' . $category->id . '" ' . $selected . '>' . str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $level) . e($category->name) . '</option>';
        if (!empty($category->children)) {
            renderCategoryOptionsForEdit($category->children, $selectedId, $level + 1);
        }
    }
}

function buildCategoryTree($categories, $parentId = null)
{
    $branch = [];

    foreach ($categories as $category) {
        if ($category->parent_id == $parentId) {
            $children = buildCategoryTree($categories, $category->id);
            if ($children) {
                $category->children = $children;
            }
            $branch[] = $category;
        }
    }

    return $branch;
}

// ==============================================================

function IND_money_format($number)
{
    $decimal = (string)($number - floor($number));
    $money = floor($number);
    $length = strlen($money);
    $delimiter = '';
    $money = strrev($money);

    for ($i = 0; $i < $length; $i++) {
        if (($i == 3 || ($i > 3 && ($i - 1) % 2 == 0)) && $i != $length) {
            $delimiter .= ',';
        }
        $delimiter .= $money[$i];
    }

    $result = strrev($delimiter);
    $decimal = preg_replace("/0\./i", ".", $decimal);
    $decimal = substr($decimal, 0, 3);

    if ($decimal != '0') {
        $result = $result . $decimal;
    }

    return $result;
}

function formatAmountInNumber($amount)
{
    $amount =  str_replace(',', '', $amount);
    return IND_money_format($amount);
}

function readableFormatPrice($price)
{
    static $currencySymbol = null;
    if ($currencySymbol === null) {
        $currencySymbol = SiteSetting::with('currency')->first()->currency->symbol ?? '₹';
    }
    return $currencySymbol . ' ' . $price;
}

function formatPriceWithReadableFormat($amount)
{
    return readableFormatPrice(formatAmountInNumber($amount));
}

function cartSubTotal()
{
    $cart = session()->get('cart', []);
    $subtotal = 0;

    foreach ($cart as $item) {
        $subtotal += ($item['price'] * $item['qty']);
    }

    return formatAmountInNumber($subtotal);
}


function cartDiscount()
{
    $cart = session()->get('cart', []);
    $subtotal = 0;

    foreach ($cart as $item) {
        $subtotal += ($item['price'] * $item['qty']);
    }

    $discount = ($subtotal >= 2000) ? 100 : 0;

    return formatAmountInNumber($discount);
}


function cartTax()
{
    $cart = session()->get('cart', []);
    $subtotal = 0;

    foreach ($cart as $item) {
        $subtotal += ($item['price'] * $item['qty']);
    }

    $discount = ($subtotal >= 2000) ? 100 : 0;
    $taxRate  = config('constants.tax_percentage');

    $tax = ($subtotal - $discount) * ($taxRate / 100);

    return formatAmountInNumber($tax);
}

function calculateShippingCharges()
{
    $cart = session()->get('cart', []);
    $subtotal = 0;

    foreach ($cart as $item) {
        $subtotal += ($item['price'] * $item['qty']);
    }

    if ($subtotal < config('constants.shipping_charges_limit')) {
        return formatAmountInNumber(config('constants.shipping_charges'));
    }

    return formatAmountInNumber(0);
}

function calculateCartTotalAmount()
{
    $cart = session()->get('cart', []);
    $subtotal = 0;

    foreach ($cart as $item) {
        $subtotal += ($item['price'] * $item['qty']);
    }

    $discount = 0;

    // GST 18%
    $tax = ($subtotal - $discount) * (config('constants.tax_percentage') / 100);

    // Shipping
    $shipping = ($subtotal < config('constants.shipping_charges_limit'))
        ? config('constants.shipping_charges')
        : 0;

    $total = $subtotal - $discount + $tax + $shipping;

    return formatAmountInNumber($total);
}

function readableFormatDateTime($date)
{
    return date('d-M-Y, h:i:s A', strtotime($date));
}

function deliveryReadableFormatDateTime($date)
{
    return date('D, d-M-Y', strtotime($date));
}

function calculateDiscountPercentage($mrp, $sellingPrice)
{
    if ($mrp > $sellingPrice) {
        return ceil((($mrp - $sellingPrice) / $mrp) * 100);
    }

    return 0;
}

function activeMenu($currentRoute)
{
    if (in_array(request()->path(), [request()->is($currentRoute)])) {
        return 'active';
    }

    return '';
}

function formatAmountForPayment($amount)
{
    return str_replace(',', '', $amount);
}

// function cartSubTotalForPayment()
// {
//     return formatAmountForPayment(\Binafy\LaravelCart\Facades\Cart::subtotal());
// }

// function calculateShippingChargesForPayment()
// {
//     if (\Binafy\LaravelCart\Facades\Cart::total() < Config::get('constants.shipping_charges_limit')) {
//         return formatAmountForPayment(Config::get('constants.shipping_charges'));
//     }
//     return formatAmountForPayment(number_format(0, 2));
// }

// function calculateCartTotalAmountForPayment()
// {
//     if (\Binafy\LaravelCart\Facades\Cart::total() < Config::get('constants.shipping_charges_limit')) {
//         $totalAmount = \Binafy\LaravelCart\Facades\Cart::total() + Config::get('constants.shipping_charges');
//         return formatAmountForPayment($totalAmount);
//     }
//     $totalAmount = \Binafy\LaravelCart\Facades\Cart::total() + 0;
//     return formatAmountForPayment($totalAmount);
// }

function customUrlEncode($string)
{
    return urlencode(base64_encode($string));
}

function customUrlDecode($string)
{
    return base64_decode(urldecode($string));
}

function generateReturnCode()
{
    return 'AG-RC-' . time() . rand(99, 999);
}

function check_return_date_validity($returnLastDate)
{
    $returnLastDate = Carbon\Carbon::createFromFormat(
        'Y-m-d H:i:s',
        $returnLastDate
    )->addDays(2);

    $currentDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now());

    if ($currentDate > $returnLastDate) {
        return true;
    }
    return false;
}


if (!function_exists('generateVerificationCode')) {
    /**
     * Generate a random 6-digit verification code
     *
     * @return string
     */
    function generateVerificationCode()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('customUrlDecode')) {
    /**
     * Custom URL decode function
     *
     * @param string $string
     * @return string
     */
    function customUrlDecode($string)
    {
        return urldecode($string);
    }
}

if (!function_exists('getCartTotals')) {
    function getCartTotals()
    {
        return app(\App\Http\Controllers\Website\CartController::class)->calculateTotals();
    }
}
