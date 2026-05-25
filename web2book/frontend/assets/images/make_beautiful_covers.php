<?php
/**
 * Beautiful Book Cover Generator v4
 * Minimalist style with text
 */

$books = [
    ['id'=>1, 'file'=>'harry_potter_1.jpg', 'title'=>'Harry Potter', 'author'=>'J.K. Rowling', 'cat'=>'Fantasy',
     'bg1'=>'8B0000', 'bg2'=>'DC143C', 'accent'=>'FFD700', 'light'=>'FFFACD'],
    ['id'=>2, 'file'=>'harry_potter_2.jpg', 'title'=>'Harry Potter 2', 'author'=>'J.K. Rowling', 'cat'=>'Fantasy',
     'bg1'=>'1a237e', 'bg2'=>'283593', 'accent'=>'FFD700', 'light'=>'c5cae9'],
    ['id'=>3, 'file'=>'dac_nhan_tam.jpg', 'title'=>'DAC NHAN TAM', 'author'=>'Dale Carnegie', 'cat'=>'Self Help',
     'bg1'=>'1b5e20', 'bg2'=>'2e7d32', 'accent'=>'FFD700', 'light'=>'c8e6c9'],
    ['id'=>4, 'file'=>'nha_gia_kim.jpg', 'title'=>'THE ALCHEMIST', 'author'=>'Paulo Coelho', 'cat'=>'Novel',
     'bg1'=>'e65100', 'bg2'=>'f57c00', 'accent'=>'FFD700', 'light'=>'ffe0b2'],
    ['id'=>5, 'file'=>'cho_toi_xin.jpg', 'title'=>'CHO TOI XIN', 'author'=>'Nguyen Nhat Anh', 'cat'=>'Novel',
     'bg1'=>'6a1b9a', 'bg2'=>'7b1fa2', 'accent'=>'FF69B4', 'light'=>'e1bee7'],
    ['id'=>6, 'file'=>'sapiens.jpg', 'title'=>'SAPIENS', 'author'=>'Yuval Noah Harari', 'cat'=>'History',
     'bg1'=>'263238', 'bg2'=>'37474f', 'accent'=>'FFFFFF', 'light'=>'eceff1'],
    ['id'=>7, 'file'=>'universe.jpg', 'title'=>'COSMOS', 'author'=>'Carl Sagan', 'cat'=>'Science',
     'bg1'=>'000814', 'bg2'=>'001d3d', 'accent'=>'FFD700', 'light'=>'003566'],
    ['id'=>8, 'file'=>'khong_sinh.jpg', 'title'=>'KHONG SINH TU', 'author'=>'Tung La Nghệ', 'cat'=>'Novel',
     'bg1'=>'004d40', 'bg2'=>'00695c', 'accent'=>'00BCD4', 'light'=>'b2dfdb'],
    ['id'=>9, 'file'=>'think_grow.jpg', 'title'=>'THINK & GROW', 'author'=>'Napoleon Hill', 'cat'=>'Business',
     'bg1'=>'2c3e50', 'bg2'=>'34495e', 'accent'=>'FFD700', 'light'=>'ecf0f1'],
    ['id'=>10, 'file'=>'7_habits.jpg', 'title'=>'7 HABITS', 'author'=>'Stephen Covey', 'cat'=>'Self Help',
     'bg1'=>'1565c0', 'bg2'=>'1976d2', 'accent'=>'FFFFFF', 'light'=>'bbdefb'],
    ['id'=>11, 'file'=>'hoa_vang.jpg', 'title'=>'HOA VANG', 'author'=>'Nguyen Nhat Anh', 'cat'=>'Novel',
     'bg1'=>'f39c12', 'bg2'=>'e67e22', 'accent'=>'FFFFFF', 'light'=>'fef9e7'],
    ['id'=>12, 'file'=>'mat_biec.jpg', 'title'=>'MAT BIEC', 'author'=>'Nguyen Nhat Anh', 'cat'=>'Novel',
     'bg1'=>'9b59b6', 'bg2'=>'8e44ad', 'accent'=>'FFFFFF', 'light'=>'f5eef8'],
    ['id'=>13, 'file'=>'tristan.jpg', 'title'=>'TRISTAN', 'author'=>'Isolde', 'cat'=>'Romance',
     'bg1'=>'c0392b', 'bg2'=>'e74c3c', 'accent'=>'FFD700', 'light'=>'fadbd8'],
    ['id'=>14, 'file'=>'cuon_theo.jpg', 'title'=>'WILD', 'author'=>'Cheryl Strayed', 'cat'=>'Memoir',
     'bg1'=>'27ae60', 'bg2'=>'2ecc71', 'accent'=>'FFFFFF', 'light'=>'e8f8f5'],
    ['id'=>15, 'file'=>'thien_tu.jpg', 'title'=>'OUTLIERS', 'author'=>'Malcolm Gladwell', 'cat'=>'Business',
     'bg1'=>'2980b9', 'bg2'=>'3498db', 'accent'=>'FFD700', 'light'=>'ebf5fb'],
];

function h2r($hex) {
    $hex = str_replace('#', '', $hex);
    return [
        'r' => hexdec(substr($hex, 0, 2)),
        'g' => hexdec(substr($hex, 2, 2)),
        'b' => hexdec(substr($hex, 4, 2))
    ];
}

function drawStar($img, $cx, $cy, $o, $i, $c) {
    $pts = [];
    for ($k = 0; $k < 5; $k++) {
        $pts[] = $cx + $o * cos(deg2rad(90 + $k * 72));
        $pts[] = $cy - $o * sin(deg2rad(90 + $k * 72));
        $pts[] = $cx + $i * cos(deg2rad(90 + $k * 72 + 36));
        $pts[] = $cy - $i * sin(deg2rad(90 + $k * 72 + 36));
    }
    imagefilledpolygon($img, $pts, 10, $c);
}

function makeCover($f, $title, $author, $cat, $bg1, $bg2, $accent, $light) {
    $w = 400; $h = 560;
    $img = imagecreatetruecolor($w, $h);
    
    // BG Gradient
    $c1 = h2r($bg1); $c2 = h2r($bg2);
    for ($y = 0; $y < $h; $y++) {
        $ratio = $y / $h;
        $r = intval($c1['r'] + ($c2['r'] - $c1['r']) * $ratio);
        $g = intval($c1['g'] + ($c2['g'] - $c1['g']) * $ratio);
        $b = intval($c1['b'] + ($c2['b'] - $c1['b']) * $ratio);
        imageline($img, 0, $y, $w, $y, imagecolorallocate($img, $r, $g, $b));
    }
    
    // Colors
    $accentC = imagecolorallocate($img, ...array_values(h2r($accent)));
    $lightC = imagecolorallocate($img, ...array_values(h2r($light)));
    $darkC = imagecolorallocate($img, 0, 0, 0);
    
    // === TOP SECTION - Category badge ===
    $badgeY = 40;
    $badgeH = 30;
    
    // Badge background
    $badgeW = strlen($cat) * 10 + 30;
    $badgeX = ($w - $badgeW) / 2;
    imagefilledrectangle($img, $badgeX, $badgeY, $badgeX + $badgeW, $badgeY + $badgeH, $accentC);
    
    // === CENTER - Large decorative star ===
    $starY = 150;
    drawStar($img, $w/2, $starY, 80, 35, $accentC);
    drawStar($img, $w/2, $starY, 55, 25, $lightC);
    
    // === DECORATIVE LINES ===
    $lineY = 280;
    // Left line
    imageline($img, 40, $lineY, 140, $lineY, $accentC);
    // Right line  
    imageline($img, 260, $lineY, 360, $lineY, $accentC);
    
    // === TITLE ===
    $titleY = 330;
    // Split title if long
    $words = explode(' ', $title);
    if (count($words) > 2) {
        $line1 = implode(' ', array_slice($words, 0, ceil(count($words)/2)));
        $line2 = implode(' ', array_slice($words, ceil(count($words)/2)));
        imagestringup($img, 5, 170, $titleY + 60, $line1, $lightC);
        imagestringup($img, 5, 230, $titleY + 60, $line2, $lightC);
    } else {
        imagestringup($img, 5, 195, $titleY + 40, $title, $lightC);
    }
    
    // === BOTTOM SECTION - Author ===
    $authorY = 450;
    
    // Decorative small stars
    drawStar($img, 100, $authorY, 15, 7, $accentC);
    drawStar($img, 300, $authorY, 15, 7, $accentC);
    
    // Horizontal line
    imageline($img, 130, $authorY, 270, $authorY, $accentC);
    
    // Author name
    imagestring($img, 4, 130, $authorY + 20, strtoupper($author), $lightC);
    
    // === CORNER DECORATIONS ===
    // Top left
    imageline($img, 20, 20, 80, 20, $accentC);
    imageline($img, 20, 20, 20, 80, $accentC);
    // Top right
    imageline($img, 320, 20, 380, 20, $accentC);
    imageline($img, 380, 20, 380, 80, $accentC);
    // Bottom left
    imageline($img, 20, 540, 80, 540, $accentC);
    imageline($img, 20, 480, 20, 540, $accentC);
    // Bottom right
    imageline($img, 320, 540, 380, 540, $accentC);
    imageline($img, 380, 480, 380, 540, $accentC);
    
    // === BORDER ===
    imagerectangle($img, 10, 10, $w-10, $h-10, $accentC);
    
    imagejpeg($img, $f, 95);
    imagedestroy($img);
}

$dir = __DIR__;
foreach ($books as $b) {
    // Skip if real cover already exists and is valid size
    $existing = $dir.'/'.$b['file'];
    if (file_exists($existing) && filesize($existing) > 10000) {
        echo "✓ {$b['file']} (existing)\n";
        continue;
    }
    makeCover($existing, $b['title'], $b['author'], $b['cat'], $b['bg1'], $b['bg2'], $b['accent'], $b['light']);
    echo "✓ {$b['file']}\n";
}
echo "\nDone!";
