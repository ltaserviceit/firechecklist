<?php
// report_info.php - shared by view_report.php and export_pdf.php
// Returns the "report information" values, using admin edits (tasks.report_info JSON)
// where they exist and falling back to the original task data otherwise.

function report_info_fields(): array {
    return [
        'task_id'         => 'Task ID',
        'company_name'    => 'Company Name',
        'building_name'   => 'Building Name',
        'date_created'    => 'Date Created',
        'status'          => 'Status',
        'inspected_by'    => 'Inspected By',
        'inspection_date' => 'Date Inspected',
    ];
}

function get_report_info(array $task): array {
    $info = [
        'task_id'         => '#' . str_pad($task['task_id'], 5, '0', STR_PAD_LEFT),
        'company_name'    => $task['company_name'] ?? 'N/A',
        'building_name'   => $task['building_name'] ?? 'N/A',
        'date_created'    => !empty($task['created_at'])
                                ? date('d M Y, h:i A', strtotime($task['created_at'])) : 'N/A',
        'status'          => $task['status'] ?? '',
        'inspected_by'    => !empty($task['tech_sign_name'])
                                ? strtoupper($task['tech_sign_name'])
                                : 'Technician (ID: ' . ($task['tech_id'] ?? 'Unknown') . ')',
        'inspection_date' => !empty($task['inspection_date'])
                                ? date('d M Y, h:i A', strtotime($task['inspection_date'])) : 'N/A',
    ];

    // Overlay the admin's saved edits (if any)
    $saved = json_decode($task['report_info'] ?? '', true);
    if (is_array($saved)) {
        foreach ($info as $key => $default) {
            if (array_key_exists($key, $saved)) {
                $info[$key] = (string)$saved[$key];
            }
        }
    }
    return $info;
}
