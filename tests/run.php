<?php

require(dirname(__DIR__) . "/vendor/autoload.php");

use MattyG\FBPrivacyAuth\AuthChecker;

$groups = json_decode(file_get_contents("groups.json"), true);
$resources = json_decode(file_get_contents("resources.json"), true);

function outputVal($val)
{
    if (is_bool($val)) {
        return ($val ? "true" : "false");
    } elseif (is_array($val)) {
        $return = [];
        foreach ($val as $v) { $return[] = outputVal($v); }
        return implode(", ", $return);
    } else {
        return $val;
    }
}

$checker = new AuthChecker($groups, array_column_maintain_keys($resources, "auth"));
$successCount = 0;
$failureCount = 0;

$run = function($method, $expected) use ($checker, &$successCount, &$failureCount) {
    $args = array_slice(func_get_args(), 2);
    $actual = call_user_func_array(array($checker, $method), $args);
    $actual === $expected ? $successCount++ : $failureCount++;
    echo "(" . outputVal($args) . ") " . ($actual === $expected ? "SUCC" : "FAIL") . ": Expected: " . outputVal($expected) . "; Actual: " . outputVal($actual) . "\n";
};

$run("check", true, "res1", "user1");
$run("check", false, "res1", "user2");
$run("check", false, "res1", "user3");
$run("check", true, "res2", "user1");
$run("check", false, "res2", "user2");
$run("check", false, "res2", "user3");
$run("check", true, "res3", "user1");
$run("check", false, "res3", "user2");
$run("check", true, "res3", "user3");
$run("check", true, "res4", "user1");
$run("check", true, "res4", "user2");
$run("check", true, "res4", "user3");
$run("check", false, "res5", "user1");
$run("check", true, "res5", "user2");
$run("check", true, "res5", "user3");

$run("getAllowedResourceIds", ["res1", "res2", "res3", "res4"], "user1");
$run("getAllowedResourceIds", ["res4", "res5"], "user2");
$run("getAllowedResourceIds", ["res3", "res4", "res5"], "user3");

echo "\n";
echo "Number of successes: $successCount\n";
echo "Number of failures: $failureCount\n";
