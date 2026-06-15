<?php
// Professional Book Cover Generator v3

$books = [
    ['id'=>1, 'file'=>'harry_potter_1.jpg', 'bg1'=>'1a237e', 'bg2'=>'283593', 'accent'=>'5c6bc0', 'light'=>'c5cae9', 'dark'=>'0d47a1'],
    ['id'=>2, 'file'=>'harry_potter_2.jpg', 'bg1'=>'37474f', 'bg2'=>'455a64', 'accent'=>'78909c', 'light'=>'cfd8dc', 'dark'=>'263238'],
    ['id'=>3, 'file'=>'dac_nhan_tam.jpg', 'bg1'=>'1b5e20', 'bg2'=>'2e7d32', 'accent'=>'66bb6a', 'light'=>'c8e6c9', 'dark'=>'0a3d0a'],
    ['id'=>4, 'file'=>'nha_gia_kim.jpg', 'bg1'=>'e65100', 'bg2'=>'f57c00', 'accent'=>'ffb74d', 'light'=>'ffe0b2', 'dark'=>'bf360c'],
    ['id'=>5, 'file'=>'cho_toi_xin.jpg', 'bg1'=>'6a1b9a', 'bg2'=>'7b1fa2', 'accent'=>'ba68c8', 'light'=>'e1bee7', 'dark'=>'4a148c'],
    ['id'=>6, 'file'=>'sapiens.jpg', 'bg1'=>'263238', 'bg2'=>'37474f', 'accent'=>'ff7043', 'light'=>'ffccbc', 'dark'=>'0d1b2a'],
    ['id'=>7, 'file'=>'universe.jpg', 'bg1'=>'0d1b2a', 'bg2'=>'1b263b', 'accent'=>'778da9', 'light'=>'e0e1dd', 'dark'=>'000814'],
    ['id'=>8, 'file'=>'khong_sinh.jpg', 'bg1'=>'004d40', 'bg2'=>'00695c', 'accent'=>'4db6ac', 'light'=>'b2dfdb', 'dark'=>'00251a'],
    ['id'=>9, 'file'=>'think_grow.jpg', 'bg1'=>'bf360c', 'bg2'=>'d84315', 'accent'=>'ff8a65', 'light'=>'ffccbc', 'dark'=>'7f0000'],
    ['id'=>10, 'file'=>'7_habits.jpg', 'bg1'=>'1565c0', 'bg2'=>'1976d2', 'accent'=>'64b5f6', 'light'=>'bbdefb', 'dark'=>'0d47a1'],
    ['id'=>11, 'file'=>'hoa_vang.jpg', 'bg1'=>'bf360c', 'bg2'=>'d84315', 'accent'=>'ff7043', 'light'=>'ffccbc', 'dark'=>'8d2500'],
    ['id'=>12, 'file'=>'mat_biec.jpg', 'bg1'=>'311b92', 'bg2'=>'4527a0', 'accent'=>'9575cd', 'light'=>'d1c4e9', 'dark'=>'1a0a5c'],
    ['id'=>13, 'file'=>'tristan.jpg', 'bg1'=>'b71c1c', 'bg2'=>'c62828', 'accent'=>'ef5350', 'light'=>'ffcdd2', 'dark'=>'7f0000'],
    ['id'=>14, 'file'=>'cuon_theo.jpg', 'bg1'=>'37474f', 'bg2'=>'546e7a', 'accent'=>'90a4ae', 'light'=>'eceff1', 'dark'=>'1c313a'],
    ['id'=>15, 'file'=>'thien_tu.jpg', 'bg1'=>'1a237e', 'bg2'=>'303f9f', 'accent'=>'7c4dff', 'light'=>'d1c4e9', 'dark'=>'0d1b5c'],
];

function h2r($hex) {
    return [
        'r' => hexdec(substr($hex, 0, 2)),
        'g' => hexdec(substr($hex, 2, 2)),
        'b' => hexdec(substr($hex, 4, 2))
    ];
}

function star($img, $cx, $cy, $o, $i, $c) {
    $p = [];
    for ($k = 0; $k < 5; $k++) {
        $p[] = $cx + $o * cos(deg2rad(90 + $k * 72));
        $p[] = $cy - $o * sin(deg2rad(90 + $k * 72));
        $p[] = $cx + $i * cos(deg2rad(90 + $k * 72 + 36));
        $p[] = $cy - $i * sin(deg2rad(90 + $k * 72 + 36));
    }
    imagefilledpolygon($img, $p, 10, $c);
}

function makeCover($f, $bg1, $bg2, $accent, $light, $dark) {
    $w = 400; $h = 560;
    $img = imagecreatetruecolor($w, $h);
    
    // BG Gradient
    $c1 = h2r($bg1); $c2 = h2r($bg2);
    for ($y = 0; $y < $h; $y++) {
        $r = $c1['r'] + ($c2['r'] - $c1['r']) * $y / $h;
        $g = $c1['g'] + ($c2['g'] - $c1['g']) * $y / $h;
        $b = $c1['b'] + ($c2['b'] - $c1['b']) * $y / $h;
        imageline($img, 0, $y, $w, $y, imagecolorallocate($img, $r, $g, $b));
    }
    
    // Colors
    $accC = imagecolorallocate($img, ...array_values(h2r($accent)));
    $liteC = imagecolorallocate($img, ...array_values(h2r($light)));
    $darkC = imagecolorallocate($img, ...array_values(h2r($dark)));
    $bg2C = imagecolorallocate($img, ...array_values(h2r($bg2)));
    
    // Top diamonds
    for ($k = 0; $k < 8; $k++) {
        $x = 15 + $k * 50; $y = 25 + $k * 3;
        $pts = [$x, $y, $x+20, $y+15, $x, $y+30, $x-20, $y+15];
        imagefilledpolygon($img, $pts, 4, $liteC);
    }
    
    // Book shadow
    $bx = ($w-160)/2; $by = 80; $bw = 160; $bh = 220;
    imagefilledrectangle($img, $bx+10, $by+10, $bx+$bw+25, $by+$bh+5, imagecolorallocate($img, 0,0,0));
    
    // Book cover
    imagefilledrectangle($img, $bx, $by, $bx+$bw, $by+$bh, $darkC);
    
    // Spine
    imagefilledrectangle($img, $bx, $by, $bx+20, $by+$bh, $bg2C);
    
    // Pages
    for ($k = 0; $k < 18; $k++) {
        $pg = imagecolorallocate($img, max(0, h2r($bg2)['r']-50+$k*3), max(0, h2r($bg2)['g']-50+$k*3), max(0, h2r($bg2)['b']-50+$k*3));
        imageline($img, $bx+3, $by+15+$k*11, $bx+17, $by+15+$k*11, $pg);
    }
    
    // Cover lines
    for ($k = 0; $k < 6; $k++) {
        imageline($img, $bx+35, $by+25+$k*10, $bx+$bw-20, $by+25+$k*10, $accC);
    }
    
    // Stars
    star($img, $bx+$bw/2, $by+$bh/2-5, 40, 20, $accC);
    star($img, $bx+$bw/2, $by+$bh/2-5, 22, 11, $liteC);
    
    // Circles
    imageellipse($img, $bx+$bw-35, $by+45, 45, 45, $liteC);
    imageellipse($img, $bx+55, $by+$bh-45, 40, 40, $liteC);
    
    // Category badge
    $badgeX = ($w-140)/2; $badgeY = $by+$bh+25;
    imagefilledrectangle($img, $badgeX, $badgeY, $badgeX+140, $badgeY+35, $liteC);
    
    // Bottom diamonds
    for ($k = 0; $k < 8; $k++) {
        $x = 15 + $k * 50; $y = $h-55 + $k * 3;
        $pts = [$x, $y, $x+20, $y+15, $x, $y+30, $x-20, $y+15];
        imagefilledpolygon($img, $pts, 4, $liteC);
    }
    
    // Small book
    $sx = ($w-80)/2; $sy = $h-100;
    imagefilledrectangle($img, $sx, $sy, $sx+80, $sy+60, $darkC);
    imagefilledrectangle($img, $sx+80, $sy, $sx+95, $sy+60, $bg2C);
    
    // Borders
    imagerectangle($img, 8, 8, $w-8, $h-8, $accC);
    imagerectangle($img, 12, 12, $w-12, $h-12, $liteC);
    
    // Corners
    $cs = 25;
    imageline($img, 12, 12, 12+$cs, 12, $liteC); imageline($img, 12, 12, 12, 12+$cs, $liteC);
    imageline($img, $w-12, 12, $w-12-$cs, 12, $liteC); imageline($img, $w-12, 12, $w-12, 12+$cs, $liteC);
    imageline($img, 12, $h-12, 12+$cs, $h-12, $liteC); imageline($img, 12, $h-12, 12, $h-12-$cs, $liteC);
    imageline($img, $w-12, $h-12, $w-12-$cs, $h-12, $liteC); imageline($img, $w-12, $h-12, $w-12, $h-12-$cs, $liteC);
    
    imagejpeg($img, $f, 98);
    imagedestroy($img);
}

$dir = __DIR__;
foreach ($books as $b) {
    makeCover($dir.'/'.$b['file'], $b['bg1'], $b['bg2'], $b['accent'], $b['light'], $b['dark']);
    echo "✓ {$b['file']}\n";
}
echo "\nDone!";
