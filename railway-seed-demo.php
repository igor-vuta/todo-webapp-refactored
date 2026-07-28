<?php
// Public demo seed for the deployed app.
// Idempotent: skips if the demo user already exists.
// All demo accounts share the password: Demo1234!

$host = getenv('DB_HOST') ?: 'db';
$port = getenv('DB_PORT') ?: '3306';
$name = getenv('DB_NAME') ?: 'todo_app';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

$pdo = new PDO(
    "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
    $user,
    $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// Already seeded?
$exists = $pdo->prepare("SELECT id FROM users WHERE username = 'demo'");
$exists->execute();
if ($exists->fetchColumn()) {
    echo "Demo data already present - skipping seed.\n";
    exit(0);
}

echo "Seeding public demo data...\n";
$pdo->beginTransaction();

// Remove leftover smoke-test account, if any
$pdo->exec("DELETE FROM users WHERE username = 'deploycheck'");

/* ---------------- Users ---------------- */
$password = password_hash('Demo1234!', PASSWORD_BCRYPT);
$users = [
    'demo' => 'demo@todoapp.dev',
    'ava'  => 'ava@todoapp.dev',
    'liam' => 'liam@todoapp.dev',
    'maya' => 'maya@todoapp.dev',
    'noah' => 'noah@todoapp.dev',
];
$uid = [];
$insUser = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
foreach ($users as $u => $email) {
    $insUser->execute([$u, $email, $password]);
    $uid[$u] = (int) $pdo->lastInsertId();
}

/* ---------------- Groups ---------------- */
$groups = [
    'Product Launch 🚀'      => ['owner' => 'demo', 'members' => ['demo', 'ava', 'liam']],
    'Family Household 🏡'    => ['owner' => 'ava',  'members' => ['ava', 'demo', 'maya']],
    'Weekend Hiking Crew ⛰️' => ['owner' => 'noah', 'members' => ['noah', 'demo', 'liam', 'maya']],
];
$gid = [];
$insGroup  = $pdo->prepare('INSERT INTO `groups` (name, owner_id) VALUES (?, ?)');
$insMember = $pdo->prepare('INSERT INTO group_users (group_id, user_id) VALUES (?, ?)');
foreach ($groups as $gname => $g) {
    $insGroup->execute([$gname, $uid[$g['owner']]]);
    $gid[$gname] = (int) $pdo->lastInsertId();
    foreach ($g['members'] as $m) {
        $insMember->execute([$gid[$gname], $uid[$m]]);
    }
}

/* ---------------- Lists ---------------- */
$lists = [
    '🌅 Morning Routine'   => ['user' => 'demo', 'group' => null],
    '💼 Work — Q3 Sprint'  => ['user' => 'demo', 'group' => 'Product Launch 🚀'],
    '🛒 Groceries'         => ['user' => 'ava',  'group' => 'Family Household 🏡'],
    '🏋️ Fitness & Health'  => ['user' => 'demo', 'group' => null],
    '📚 Reading List'      => ['user' => 'demo', 'group' => null],
    '⛰️ Trail Plans'       => ['user' => 'noah', 'group' => 'Weekend Hiking Crew ⛰️'],
    '🎨 Side Projects'     => ['user' => 'demo', 'group' => null],
];
$lid = [];
$insList = $pdo->prepare('INSERT INTO lists (name, user_id, group_id) VALUES (?, ?, ?)');
foreach ($lists as $lname => $l) {
    $insList->execute([$lname, $uid[$l['user']], $l['group'] ? $gid[$l['group']] : null]);
    $lid[$lname] = (int) $pdo->lastInsertId();
}

/* ---------------- Tasks ---------------- */
// [title, days-from-today, status, priority, user, list, start, end]
$tasks = [
    // 🌅 Morning Routine
    ['Meditate for 10 minutes',                    0,  'completed',   1, 'demo', '🌅 Morning Routine', '06:30', '06:40'],
    ["Journal — 3 things I'm grateful for",        0,  'completed',   1, 'demo', '🌅 Morning Routine', '06:45', '07:00'],
    ['30-minute run along the river',              0,  'in_progress', 2, 'demo', '🌅 Morning Routine', '07:15', '07:45'],
    ['Prep overnight oats for tomorrow',           0,  'pending',     1, 'demo', '🌅 Morning Routine', '21:00', '21:15'],

    // 💼 Work — Q3 Sprint
    ['Ship v2.0 landing page',                     2,  'in_progress', 3, 'demo', '💼 Work — Q3 Sprint', null, null],
    ["Review Ava's API pull request",              1,  'pending',     3, 'demo', '💼 Work — Q3 Sprint', null, null],
    ['Write release notes for the changelog',      3,  'pending',     2, 'ava',  '💼 Work — Q3 Sprint', null, null],
    ['Fix flaky signup e2e test',                 -1,  'completed',   2, 'liam', '💼 Work — Q3 Sprint', null, null],
    ['Team retro — prepare talking points',        5,  'pending',     1, 'demo', '💼 Work — Q3 Sprint', '14:00', '15:00'],
    ['Update onboarding docs',                    -3,  'completed',   1, 'ava',  '💼 Work — Q3 Sprint', null, null],

    // 🛒 Groceries
    ['Farmers market — tomatoes & basil',          1,  'pending',     1, 'ava',  '🛒 Groceries', '09:00', '10:00'],
    ['Restock coffee beans ☕',                     0,  'completed',   2, 'demo', '🛒 Groceries', null, null],
    ['Birthday cake ingredients 🎂',               4,  'pending',     2, 'maya', '🛒 Groceries', null, null],

    // 🏋️ Fitness & Health
    ['Leg day at the gym',                        -1,  'completed',   2, 'demo', '🏋️ Fitness & Health', '18:00', '19:00'],
    ['Book dentist appointment',                   7,  'pending',     2, 'demo', '🏋️ Fitness & Health', null, null],
    ['Swim 1 km at the pool',                      2,  'pending',     1, 'demo', '🏋️ Fitness & Health', '07:00', '08:00'],
    ['Meal prep for the week',                    -2,  'completed',   1, 'demo', '🏋️ Fitness & Health', null, null],

    // 📚 Reading List
    ["Finish 'The Pragmatic Programmer'",         10,  'in_progress', 1, 'demo', '📚 Reading List', null, null],
    ["Start 'Deep Work' by Cal Newport",          14,  'pending',     1, 'demo', '📚 Reading List', null, null],
    ["Read paper: 'Attention Is All You Need'",   -5,  'completed',   2, 'demo', '📚 Reading List', null, null],

    // ⛰️ Trail Plans
    ['Scout the Cascade Falls loop',               6,  'pending',     2, 'noah', '⛰️ Trail Plans', '08:00', '14:00'],
    ["Check everyone's gear checklist",            4,  'pending',     1, 'liam', '⛰️ Trail Plans', null, null],
    ['Reserve campsite for the August trip',       2,  'in_progress', 3, 'noah', '⛰️ Trail Plans', null, null],
    ['Wash the hiking boots 🥾',                  -2,  'completed',   1, 'maya', '⛰️ Trail Plans', null, null],

    // 🎨 Side Projects
    ['Deploy todo app to Railway 🚂',              0,  'completed',   3, 'demo', '🎨 Side Projects', null, null],
    ['Design personal portfolio v3',               8,  'in_progress', 2, 'demo', '🎨 Side Projects', null, null],
    ['Learn Caddy server configuration',          -1,  'completed',   1, 'demo', '🎨 Side Projects', null, null],
    ['Sketch mobile app wireframes',              12,  'pending',     1, 'demo', '🎨 Side Projects', null, null],
];

$insTask = $pdo->prepare(
    'INSERT INTO tasks (title, due_date, status, priority, user_id, list_id, group_id, start_time, end_time)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
foreach ($tasks as [$title, $offset, $status, $prio, $u, $l, $start, $end]) {
    $insTask->execute([
        $title,
        date('Y-m-d', strtotime("{$offset} days")),
        $status,
        $prio,
        $uid[$u],
        $lid[$l],
        $lists[$l]['group'] ? $gid[$lists[$l]['group']] : null,
        $start ? "{$start}:00" : null,
        $end ? "{$end}:00" : null,
    ]);
}

$pdo->commit();

echo 'Seeded: ' . count($users) . ' users, ' . count($groups) . ' groups, '
   . count($lists) . " lists, " . count($tasks) . " tasks.\n";
echo "Demo login: demo@todoapp.dev / Demo1234!\n";
