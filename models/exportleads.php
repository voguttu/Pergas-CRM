<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== TRUE) {
    header('Location: ../index.html');
    exit;
}

include "../database/db.php";

$marketer = isset($_POST['marketer']) ? trim($_POST['marketer']) : '';
$leadIds = isset($_POST['lead_ids']) ? $_POST['lead_ids'] : [];
$pipelineExport = isset($_POST['pipeline_export']) && $_POST['pipeline_export'] === '1';

if ((!$pipelineExport && $marketer === '') || empty($leadIds) || !is_array($leadIds)) {
    http_response_code(400);
    exit('Please select at least one lead to export.');
}

$isAdmin = strtolower(trim((string) ($_SESSION['designation'] ?? ''))) === 'admin';
$sessionName = isset($_SESSION['name']) ? $_SESSION['name'] : '';

if (!$pipelineExport && !$isAdmin && $sessionName !== $marketer) {
    http_response_code(403);
    exit('You are not allowed to export leads for this marketer.');
}

$leadIds = array_values(array_unique(array_filter(array_map('intval', $leadIds), function ($id) {
    return $id > 0;
})));

if (empty($leadIds)) {
    http_response_code(400);
    exit('Invalid lead selection.');
}

$placeholders = implode(',', array_fill(0, count($leadIds), '?'));
$types = ($pipelineExport ? '' : 's') . str_repeat('i', count($leadIds));

$sql = $pipelineExport
    ? "SELECT * FROM leads WHERE status IN ('quoted', 'partialy') AND id IN ($placeholders) ORDER BY updated_at DESC"
    : "SELECT * FROM leads WHERE marketer = ? AND id IN ($placeholders) ORDER BY updated_at DESC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    exit('Unable to prepare export query.');
}

$bindParams = $pipelineExport ? $leadIds : array_merge([$marketer], $leadIds);
$bindRefs = [];
foreach ($bindParams as $key => $value) {
    $bindRefs[$key] = &$bindParams[$key];
}
array_unshift($bindRefs, $types);
call_user_func_array([$stmt, 'bind_param'], $bindRefs);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    if ($pipelineExport && !$isAdmin && $row['marketer'] !== $sessionName) {
        $stmt->close();
        $conn->close();
        http_response_code(403);
        exit('You are not allowed to export leads for another marketer.');
    }
    $data[] = $row;
}

$stmt->close();
$conn->close();

if (empty($data)) {
    http_response_code(404);
    exit('No matching leads found for export.');
}

$safeMarketer = preg_replace('/[^a-zA-Z0-9_-]/', '_', $marketer);
$filename = $pipelineExport
    ? 'pipeline_leads_' . date('Y-m-d') . '.csv'
    : 'leads_' . $safeMarketer . '_' . date('Y-m-d') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputcsv($output, array_keys($data[0]));

foreach ($data as $lead) {
    fputcsv($output, $lead);
}

fclose($output);
exit;

?>
