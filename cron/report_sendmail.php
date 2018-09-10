<?php
$opts = getopt("", array('env:'));
if (isset($opts['env']) && $opts['env']) {
    define('APPLICATION_ENV', $opts['env']);
}

$_SERVER = [
    'HTTPS' => 'on',
    'HTTP_HOST' => 'whistlerapp.org',
    'REQUEST_METHOD' => 'GET'
];

include_once __DIR__ . '/../application/init.php';

session_start();
$maxExecutionTime = 280;
$sw = new \ufw\utils\stopwatch();

$dbc = \db\db::instance();
$pass = 1;
do {
/*** send notification mails ***/
    $values = [
        'controller' => 'reports',
        'action' => 'item'
    ];
    
    $query = "
        SELECT *
        FROM " . \model\record\report::getTableName() . "
        WHERE 1
        AND mailsent = 0
        AND status IN (" . join(', ', $dbc->quote([\model\report::STATUS_APPROVED, \model\report::STATUS_UNREVIEWED])) . ")
        LIMIT 10
    ";
    $reports= $dbc->fetch_all($query, '\model\record\report', 'default', null, false, __FILE__, __LINE__);
    foreach ($reports as $report) {
        $params = ['id' => $report->uid];
        $request = new \ufw\request($values, $params, \application::getDispatchDefaults());
        ob_start();
        application::getInstance()->run($request);
        ob_end_clean();
        if ($sw->elapsed() >= $maxExecutionTime) break;
    }
    echo "\n" . $sw->elapsed_hr() . ", no of reports: " . count($reports);
    echo "\n--- $pass ----\n\n";
    
    if ($sw->elapsed() >= $maxExecutionTime) break;
    
/*** any pending reports  ***/
    $values = [
        'controller' => 'reports',
        'action' => 'admin-notification'
    ];
    
    $query = "
        SELECT *
        FROM " . \model\record\report::getTableName() . "
        WHERE 1
        AND admin_notification_sent = 0
        AND status = " . $dbc->quote(\model\report::STATUS_UNREVIEWED) . "
        AND public = 1
        LIMIT 10
    ";
    $reports= $dbc->fetch_all($query, '\model\record\report', 'default', null, false, __FILE__, __LINE__);
    foreach ($reports as $report) {
        $params = ['id' => $report->uid];
        $request = new \ufw\request($values, $params, \application::getDispatchDefaults());
        ob_start();
        application::getInstance()->run($request);
        ob_end_clean();
        if ($sw->elapsed() >= $maxExecutionTime) break;
    }
    
    echo "\n" . $sw->elapsed_hr() . ", no of reports notified to admin: " . count($reports);
    echo "\n--- $pass ----\n\n";
    
    if ($sw->elapsed() >= $maxExecutionTime) break;
    
    sleep(15);
    $pass++;
} while(1);
