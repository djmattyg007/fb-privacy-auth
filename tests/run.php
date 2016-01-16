<?php

require(dirname(__DIR__) . "/vendor/autoload.php");

use MattyG\FBPrivacyAuth\AuthChecker;

$groups = json_decode(file_get_contents("groups.json"), true);
$resources = json_decode(file_get_contents("resources.json"), true);

$checker = new AuthChecker($groups, array_column_maintain_keys($resources, "auth"));
$successCount = 0;
$failureCount = 0;
$check = function($resourceId, $username, $expected) use ($checker, &$successCount, &$failureCount) {
    $result = $checker->check($resourceId, $username);
    $result === $expected ? $successCount++ : $failureCount++;
    return $result;
};

$run = function($resourceId, $username, $expected) use ($check) {
    $result = $check($resourceId, $username, $expected);
    $bool = function($val) { return $val ? "true" : "false"; };
    echo "($resourceId, $username): Expected: " . $bool($expected) . ",\tActual: " . $bool($result) . "\n";
};

$run("res1", "user1", true);
$run("res1", "user2", false);
$run("res1", "user3", false);
$run("res2", "user1", true);
$run("res2", "user2", false);
$run("res2", "user3", false);
$run("res3", "user1", true);
$run("res3", "user2", false);
$run("res3", "user3", true);
$run("res4", "user1", true);
$run("res4", "user2", true);
$run("res4", "user3", true);
$run("res5", "user1", false);
$run("res5", "user2", true);
$run("res5", "user3", true);

echo "\n";
echo "Number of successes: $successCount\n";
echo "Number of failures: $failureCount\n";
