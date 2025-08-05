<?php

/**
 * A standalone PHP script for static code analysis.
 *
 * This script analyzes files in a given directory against a dynamically loaded ruleset.
 */

// Autoload Composer dependencies (if composer is used)
require __DIR__ . '/vendor/autoload.php';

// --- CONFIGURATION ---
const RULES_PATH = __DIR__ . '/rules';
const EXCLUDED_DIRECTORIES = [
    'vendor',
    'node_modules', // Still exclude if present in PHP projects
    'storage',
    'bootstrap',
    'public',
    'config',
    'database',
];

// --- ARGUMENT PARSING ---
$options = getopt('', ['path:', 'ruleset:']);

$path = $options['path'] ?? null;
$rulesetName = $options['ruleset'] ?? 'php/generic';

if (!$path) {
    echo "Error: Missing required argument '--path'.\n";
    echo "Usage: php code-reviewer.php --path=<path_to_code> [--ruleset=<ruleset_name>]\n";
    exit(1);
}

if (!is_dir($path) && !is_file($path)) {
    echo "Error: The provided path '{$path}' does not exist or is not a valid file/directory.\n";
    exit(1);
}

// --- MAIN EXECUTION ---
echo "Starting code review...\n";
echo "Path to review: {$path}\n";
echo "Ruleset to use: {$rulesetName}\n\n";

try {
    // Load ruleset
    $rules = loadRuleset($rulesetName);
    echo "Successfully loaded " . count($rules) . " rules.\n";

    // Perform review
    $findings = reviewCode($path, $rules);

    if (empty($findings)) {
        echo "\n✅ No issues found in the codebase.\n";
        exit(0);
    }

    echo "\nFound " . count($findings) . " issues:\n";
    displayFindings($findings);
    exit(1);

} catch (\Exception $e) {
    echo "An error occurred: " . $e->getMessage() . "\n";
    exit(1);
}

// --- FUNCTIONS ---

/**
 * Loads a ruleset from a JSON file.
 */
function loadRuleset(string $rulesetName): array
{
    $filePath = RULES_PATH . "/{$rulesetName}.json";
    if (!file_exists($filePath)) {
        throw new \InvalidArgumentException("Ruleset file '{$rulesetName}.json' not found.");
    }

    $contents = file_get_contents($filePath);
    $rules = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

    if (!is_array($rules)) {
        throw new \InvalidArgumentException("Ruleset file '{$rulesetName}.json' is not a valid JSON array.");
    }
    return $rules;
}

/**
 * Traverses files and applies rules.
 */
function reviewCode(string $path, array $rules): array
{
    $findings = [];
    // Use RecursiveDirectoryIterator for directories, or just process the file if it's a single file
    if (is_file($path)) {
        $filesToReview = [$path];
    } else {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $filesToReview = [];
        foreach ($iterator as $fileInfo) {
            // Only include .php files
            if ($fileInfo->isDir() || $fileInfo->getExtension() !== 'php') {
                continue;
            }

            // Skip excluded directories
            $filePath = $fileInfo->getPathname();
            $skip = false;
            foreach (EXCLUDED_DIRECTORIES as $excludedDir) {
                // Use DIRECTORY_SEPARATOR for cross-platform compatibility
                if (str_contains($filePath, DIRECTORY_SEPARATOR . $excludedDir . DIRECTORY_SEPARATOR)) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) {
                continue;
            }
            $filesToReview[] = $filePath;
        }
    }


    foreach ($filesToReview as $filePath) {
        $fileContents = file_get_contents($filePath);
        foreach ($rules as $rule) {
            // Ensure pattern is correctly escaped for regex and JSON
            $pattern = '/' . str_replace('/', '\\/', $rule['pattern']) . '/m';

            if (preg_match_all($pattern, $fileContents, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    $matchText = $match[0];
                    $matchOffset = $match[1];
                    $lineNumber = substr_count($fileContents, "\n", 0, $matchOffset) + 1;

                    $findings[] = [
                        'file' => $filePath,
                        'line' => $lineNumber,
                        'ruleName' => $rule['name'],
                        'description' => $rule['description'],
                        'foundCode' => $matchText
                    ];
                }
            }
        }
    }
    return $findings;
}

/**
 * Displays findings in a readable format.
 */
function displayFindings(array $findings): void
{
    foreach ($findings as $finding) {
        echo "--- \n";
        echo "File: {$finding['file']}\n";
        echo "Line: {$finding['line']}\n";
        echo "Rule: {$finding['ruleName']}\n";
        echo "Description: {$finding['description']}\n";
        echo "Found Code: " . trim($finding['foundCode']) . "\n";
    }
    echo "--- \n";
}
