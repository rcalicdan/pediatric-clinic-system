<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Http;
use Rcalicdan\FiberAsync\Api\Task;
use Rcalicdan\FiberAsync\Api\Http as FiberHttp;

// Test URLs
$testUrls = [
    'https://jsonplaceholder.typicode.com/posts/1',
    'https://jsonplaceholder.typicode.com/posts/2', 
    'https://jsonplaceholder.typicode.com/posts/3',
    'https://jsonplaceholder.typicode.com/posts/4',
    'https://jsonplaceholder.typicode.com/posts/5',
];

// Helper functions
function formatBytes(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

function displayMetrics(array $metrics): void
{
    echo sprintf("   ⏱️  Time: %.2f ms\n", $metrics['time']);
    echo sprintf("   💾 Memory Used: %s\n", formatBytes($metrics['memory_used']));
    echo sprintf("   📈 Peak Memory: %s\n", formatBytes($metrics['peak_memory']));
    echo sprintf("   ✅ Success: %d/%d requests\n", $metrics['success_count'], $metrics['total_requests']);
    
    if (isset($metrics['error'])) {
        echo sprintf("   ❌ Error: %s\n", $metrics['error']);
    }
    echo "\n";
}

function calculateAverages(array $results): array
{
    $validResults = array_filter($results, fn($r) => !isset($r['error']) && $r['time'] < 9000);
    
    if (empty($validResults)) {
        return [
            'time' => 0,
            'memory_used' => 0, 
            'peak_memory' => 0,
            'success_count' => 0,
            'total_requests' => 5,
            'type' => 'FAILED'
        ];
    }

    return [
        'time' => array_sum(array_column($validResults, 'time')) / count($validResults),
        'memory_used' => array_sum(array_column($validResults, 'memory_used')) / count($validResults),
        'peak_memory' => array_sum(array_column($validResults, 'peak_memory')) / count($validResults),
        'success_count' => array_sum(array_column($validResults, 'success_count')) / count($validResults),
        'total_requests' => 5,
        'type' => $validResults[0]['type'] . ' (Average)'
    ];
}

// Benchmark Laravel Concurrency
function benchmarkLaravelConcurrency(array $testUrls): array
{
    echo "🔸 Testing Laravel Concurrency Facade...\n";
    
    $startTime = microtime(true);
    $startMemory = memory_get_usage(true);
    $peakMemoryBefore = memory_get_peak_usage(true);

    try {
        // Use simple closures without any class dependencies
        $results = Concurrency::run([
            function () use ($testUrls) {
                return Http::timeout(30)->get($testUrls[0]);
            },
            function () use ($testUrls) {
                return Http::timeout(30)->get($testUrls[1]);
            },
            function () use ($testUrls) {
                return Http::timeout(30)->get($testUrls[2]);
            },
            function () use ($testUrls) {
                return Http::timeout(30)->get($testUrls[3]);
            },
            function () use ($testUrls) {
                return Http::timeout(30)->get($testUrls[4]);
            },
        ]);

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        $peakMemoryAfter = memory_get_peak_usage(true);

        $successCount = 0;
        foreach ($results as $result) {
            if ($result !== null && method_exists($result, 'successful') && $result->successful()) {
                $successCount++;
            }
        }

        $metrics = [
            'time' => ($endTime - $startTime) * 1000,
            'memory_used' => $endMemory - $startMemory,
            'peak_memory' => $peakMemoryAfter - $peakMemoryBefore,
            'success_count' => $successCount,
            'total_requests' => count($testUrls),
            'type' => 'Laravel Concurrency'
        ];

        displayMetrics($metrics);
        return $metrics;

    } catch (Exception $e) {
        echo "❌ Laravel Concurrency failed: " . $e->getMessage() . "\n";
        return [
            'time' => 9999,
            'memory_used' => 0,
            'peak_memory' => 0,
            'success_count' => 0,
            'total_requests' => count($testUrls),
            'type' => 'Laravel Concurrency (FAILED)',
            'error' => $e->getMessage()
        ];
    }
}

// Benchmark FiberAsync
function benchmarkFiberAsync(array $testUrls): array
{
    echo "🔹 Testing FiberAsync...\n";
    
    $startTime = microtime(true);
    $startMemory = memory_get_usage(true);
    $peakMemoryBefore = memory_get_peak_usage(true);

    try {
        $results = Task::run(function () use ($testUrls) {
            return await(all([
                FiberHttp::timeout(30)->get($testUrls[0]),
                FiberHttp::timeout(30)->get($testUrls[1]),
                FiberHttp::timeout(30)->get($testUrls[2]),
                FiberHttp::timeout(30)->get($testUrls[3]),
                FiberHttp::timeout(30)->get($testUrls[4]),
            ]));
        });

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        $peakMemoryAfter = memory_get_peak_usage(true);

        $successCount = 0;
        foreach ($results as $result) {
            if ($result !== null && $result->ok()) {
                $successCount++;
            }
        }

        $metrics = [
            'time' => ($endTime - $startTime) * 1000,
            'memory_used' => $endMemory - $startMemory,
            'peak_memory' => $peakMemoryAfter - $peakMemoryBefore,
            'success_count' => $successCount,
            'total_requests' => count($testUrls),
            'type' => 'FiberAsync'
        ];

        displayMetrics($metrics);
        return $metrics;

    } catch (Exception $e) {
        echo "❌ FiberAsync failed: " . $e->getMessage() . "\n";
        return [
            'time' => 9999,
            'memory_used' => 0,
            'peak_memory' => 0,
            'success_count' => 0,
            'total_requests' => count($testUrls),
            'type' => 'FiberAsync (FAILED)',
            'error' => $e->getMessage()
        ];
    }
}

// Main benchmark execution
echo "🧪 Starting Pure Procedural Benchmark...\n\n";
echo "🚀 Benchmarking Laravel Concurrency vs FiberAsync\n";
echo str_repeat("=", 60) . "\n\n";

// Warm up
echo "🔥 Warming up...\n";
try {
    Http::get($testUrls[0]);
    Task::run(fn() => FiberHttp::get($testUrls[0]));
} catch (Exception $e) {
    echo "⚠️  Warm-up had issues: " . $e->getMessage() . "\n";
}
echo "\n";

// Run benchmarks
$iterations = 3;
$laravelResults = [];
$fiberResults = [];

for ($i = 1; $i <= $iterations; $i++) {
    echo "📊 Run #{$i}\n";
    echo str_repeat("-", 30) . "\n";

    // Test Laravel Concurrency
    $laravelResults[] = benchmarkLaravelConcurrency($testUrls);
    
    // Small delay between tests
    sleep(1);
    
    // Test FiberAsync
    $fiberResults[] = benchmarkFiberAsync($testUrls);
    
    echo "\n";
}

// Display final results
echo str_repeat("=", 60) . "\n";
echo "📊 BENCHMARK RESULTS\n";
echo str_repeat("=", 60) . "\n\n";

// Calculate averages
$laravelAvg = calculateAverages($laravelResults);
$fiberAvg = calculateAverages($fiberResults);

echo "🔸 Laravel Concurrency Facade (Average):\n";
displayMetrics($laravelAvg);

echo "🔹 FiberAsync Library (Average):\n";
displayMetrics($fiberAvg);

// Performance comparison
echo "⚡ PERFORMANCE COMPARISON:\n";
echo str_repeat("-", 40) . "\n";

if ($laravelAvg['time'] > 0 && $fiberAvg['time'] > 0 && 
    $laravelAvg['time'] < 9000 && $fiberAvg['time'] < 9000) {
    
    $speedDiff = (($laravelAvg['time'] - $fiberAvg['time']) / $laravelAvg['time']) * 100;
    $memoryDiff = $laravelAvg['memory_used'] - $fiberAvg['memory_used'];

    echo sprintf("⏱️  Speed: FiberAsync is %.1f%% %s\n", 
        abs($speedDiff), 
        $speedDiff > 0 ? 'FASTER' : 'SLOWER'
    );
    
    echo sprintf("💾 Memory: FiberAsync uses %s %s memory\n",
        formatBytes(abs($memoryDiff)),
        $memoryDiff > 0 ? 'LESS' : 'MORE'
    );
} else {
    echo "⚠️  Cannot compare performance due to failures\n";
    if ($laravelAvg['time'] >= 9000) {
        echo "   🔸 Laravel Concurrency failed all tests\n";
    }
    if ($fiberAvg['time'] >= 9000) {
        echo "   🔹 FiberAsync failed all tests\n";
    }
}

echo "\n📋 Key Architecture Differences:\n";
echo "   🔸 Laravel: Multi-process forking + IPC serialization\n";
echo "   🔹 FiberAsync: Single-process cooperative multitasking\n";
echo "   🔸 Laravel: Process creation overhead (~10-50ms per fork)\n";  
echo "   🔹 FiberAsync: Lightweight fiber context switching (~0.1ms)\n";
echo "   🔸 Laravel: Memory isolation per process\n";
echo "   🔹 FiberAsync: Shared memory space with event loop\n";
echo "   🔸 Laravel: Requires serialization for data passing\n";
echo "   🔹 FiberAsync: Direct memory access between fibers\n\n";

echo "📝 Notes:\n";
echo "   • If Laravel fails, it may be due to process forking limitations\n";
echo "   • FiberAsync should consistently outperform in single-machine scenarios\n";
echo "   • Laravel Concurrency is better for CPU-intensive tasks across cores\n\n";

echo "✅ Benchmark completed!\n";