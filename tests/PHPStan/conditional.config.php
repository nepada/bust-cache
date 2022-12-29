<?php
declare(strict_types = 1);

use Latte\Engine;

$config = [];

if (Engine::VERSION_ID < 3_00_05) {
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Call to an undefined method Latte\\\Compiler\\\TagParser\:\:tryConsumeSeparatedToken\(\)\.$#',
        'count' => 1,
        'path' => '../../src/Bridges/BustCacheLatte/Nodes/BustCacheNode.php',
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Comparison operation "\<" between [0-9]+ and 30005 is always true\.$#',
        'count' => 1,
        'path' => '../../src/Bridges/BustCacheLatte/Nodes/BustCacheNode.php',
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Else branch is unreachable because ternary operator condition is always true\.$#',
        'count' => 1,
        'path' => '../../src/Bridges/BustCacheLatte/Nodes/BustCacheNode.php',
    ];
} else {
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Call to an undefined method Latte\\\Compiler\\\TagParser\:\:tryConsumeModifier\(\)\.$#',
        'count' => 1,
        'path' => '../../src/Bridges/BustCacheLatte/Nodes/BustCacheNode.php',
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Comparison operation "\<" between [0-9]+ and 30005 is always false\.$#',
        'count' => 1,
        'path' => '../../src/Bridges/BustCacheLatte/Nodes/BustCacheNode.php',
    ];
}

return $config;
