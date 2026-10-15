<?php
// section_editor.php - used by view_report.php
// Renders an "Edit" button and a modal form for one section table of the report.
// The modal is built from the same rows the report table shows, so the report's
// own table templates do not need to change.

function section_edit_fields(string $table, string $section): array {
    if ($section === 'PANEL PROFILE') {
        if (strpos($table, 'riser') !== false) {
            return ['location_floor' => 'Location', 'panel_type' => 'Stack', 'panel_qty' => 'No. of Stack'];
        }
        if (strpos($table, 'suppression') !== false || $table === 'wet_chemical_system') {
            return ['location_floor' => 'Location/Room', 'panel_type' => 'Cylinder Capacity (KG)', 'panel_qty' => 'Qty of Cylinder'];
        }
        return [
            'description'    => 'Panel Profile',
            'panel_type'     => 'Type of Panel',
            'panel_brand'    => 'Brand',
            'panel_model'    => 'Model',
            'panel_qty'      => 'Qty/Zone',
            'location_floor' => 'Location',
        ];
    }
    if (in_array($section, ['SYSTEM INFO', 'PUMP INFO', 'PUMP PRESSURE'], true)) {
        return ['checklist' => 'Status / Value', 'remarks' => 'Remarks'];
    }
    if ($section === 'DEVICES') {
        return [
            'location_floor' => 'Location',
            'zone_loop'      => 'Zone/Loop',
            'checklist'      => 'Checklist',
            'item_condition' => 'Condition',
            'remarks'        => 'Remarks',
        ];
    }
    return ['checklist' => 'Checklist', 'item_condition' => 'Condition', 'remarks' => 'Remarks'];
}

function section_edit_input(string $col, string $name, string $val): string {
    $e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES);

    // Done / N/A dropdown (other values such as pump readings stay as text)
    if ($col === 'checklist' && in_array($val, ['', 'Done', 'N/A'], true)) {
        $opts = ['' => '-', 'Done' => 'Done (tick)', 'N/A' => 'N/A (cross)'];
        $h = '<select name="' . $e($name) . '">';
        foreach ($opts as $v => $label) {
            $h .= '<option value="' . $e($v) . '"' . ($v === $val ? ' selected' : '') . '>' . $e($label) . '</option>';
        }
        return $h . '</select>';
    }

    if ($col === 'item_condition') {
        $opts = ['', 'Normal', 'Faulty', 'Not Applicable'];
        if (!in_array($val, $opts, true)) $opts[] = $val;
        $h = '<select name="' . $e($name) . '">';
        foreach ($opts as $v) {
            $h .= '<option value="' . $e($v) . '"' . ($v === $val ? ' selected' : '') . '>' . ($v === '' ? '-' : $e($v)) . '</option>';
        }
        return $h . '</select>';
    }

    // Remarks: hide the [IMG: ...] tag so the photo is preserved untouched
    if ($col === 'remarks') {
        $val = trim(preg_replace('/\[IMG: .*?\]/', '', $val));
    }

    return '<input type="text" name="' . $e($name) . '" value="' . $e($val) . '" maxlength="500">';
}

function render_section_editor($task_id, string $table, string $system_name, string $section_name, array $rows, string $anchor): void {
    $e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES);
    $fields = section_edit_fields($table, $section_name);
    $dialog_id = 'dlg-' . $anchor;
    $show_desc = !isset($fields['description']);
    ?>
    <div class="section-edit-bar" id="<?= $e($anchor) ?>">
        <button type="button" class="btn-edit-section"
                onclick="document.getElementById('<?= $e($dialog_id) ?>').showModal()">&#9998; Edit <?= $e($section_name) ?></button>
    </div>

    <dialog class="sec-dialog" id="<?= $e($dialog_id) ?>">
        <form method="POST" action="save_section.php">
            <input type="hidden" name="task_id" value="<?= (int)$task_id ?>">
            <input type="hidden" name="table" value="<?= $e($table) ?>">
            <input type="hidden" name="anchor" value="<?= $e($anchor) ?>">

            <h3 class="sec-dialog-title"><?= $e($system_name . ' - ' . $section_name) ?></h3>

            <div class="sec-dialog-scroll">
                <table>
                    <thead>
                        <tr>
                            <th width="5%">BIL</th>
                            <?php if ($show_desc): ?><th>DESCRIPTION</th><?php endif; ?>
                            <?php foreach ($fields as $label): ?><th><?= $e(strtoupper($label)) ?></th><?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td class="center bold"><?= $e($r['item_bil']) ?></td>
                            <?php if ($show_desc): ?>
                                <td><?= $e(mb_strimwidth((string)($r['description'] ?? ''), 0, 90, '...')) ?></td>
                            <?php endif; ?>
                            <?php foreach ($fields as $col => $label): ?>
                                <td><?= section_edit_input($col, 'rows[' . (int)$r['id'] . '][' . $col . ']', (string)($r[$col] ?? '')) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="sec-dialog-actions">
                <button type="button" class="btn btn-back" onclick="this.closest('dialog').close()">Cancel</button>
                <button type="submit" class="btn btn-print">Save Section</button>
            </div>
        </form>
    </dialog>
    <?php
}
