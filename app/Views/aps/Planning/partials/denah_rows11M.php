<?php
// ======================== SETTINGS ========================
$leftCap       = 10;  // 15 kolom mesin per baris
$leftMaxRows   = 10;   // 10 baris untuk KK7K

$centerCap     = 0;   // nonaktif
$centerMaxRows = 10;

$rightCap      = 10;   // nonaktif
$rightMaxRows  = 10;

$totalRows     = $leftMaxRows;

// Baris pemisah (TR) setelah index baris ke-â€¦ (0-based)
$sectionRows = [
    // misal: 4 => 'LORONG'
    // 0 => 'LORONG 5',
    // 2 => 'LORONG 4',
    // 4 => 'LORONG 3',
    6 => 'PINTU MASUK KK11M'
    // 8 => 'LORONG 1'
];

// ======================== UTIL ============================
$normUpper = function ($s) {
    $s = (string)$s;
    $s = trim($s);
    $s = preg_replace('/\s+/', ' ', $s);
    return strtoupper($s);
};
$makeKey = function ($cell) use ($normUpper) {
    $idMC = (int)($cell['id'] ?? 0);
    return "{$idMC}";
};

// (opsional) atur colspan per item id
$colspanMapRaw = [
    // '25' => 2,
];
$colspanMap = [];
foreach ($colspanMapRaw as $key => $val) {
    foreach (preg_split('/\s*,\s*/', (string)$key) as $idStr) {
        if ($idStr === '') continue;
        $idMC = (int)$idStr;
        if ($idMC > 0) $colspanMap[(string)$idMC] = (int)$val;
    }
}
$getSpan = function ($cell) use ($makeKey, $colspanMap) {
    $k = $makeKey($cell);
    return max(1, ($colspanMap[$k] ?? 1));
};

// ================== DATA BUFFER PER AREA ==================
$leftRows   = array_fill(0, $leftMaxRows,   []);
$centerRows = array_fill(0, $totalRows,     []);
$rightRows  = array_fill(0, $totalRows,     []);

$items = array_values(is_array($layout) ? $layout : []);

// ====== Aturan colspan kosong per row (after_cols) =======
$rowColspanLeft = [
    // 8 => ['span' => 29, 'class' => 'empty-span', 'after_cols' => 0],
];
$rowColspanCenter = [];
$rowColspanRight  = [];

// ============= PEMBAGIAN ITEM KE BARIS ===================
for ($r = 0; $r < $totalRows && !empty($items); $r++) {

    // LEFT
    if ($r < $leftMaxRows) {
        $leftReserve = max(0, min((int)($rowColspanLeft[$r]['span'] ?? 0), $leftCap));
        $leftCapRow  = max(0, $leftCap - $leftReserve);

        $used = 0;
        while (!empty($items)) {
            $span = $getSpan($items[0]);
            if ($span > $leftCapRow) $span = $leftCapRow;
            if ($used + $span <= $leftCapRow) {
                $leftRows[$r][] = ['data' => array_shift($items), 'span' => $span];
                $used += $span;
            } else break;
        }
    }

    // CENTER (aktif hanya jika $centerCap > 0)
    if ($centerCap > 0) {
        $centerReserve = max(0, min((int)($rowColspanCenter[$r]['span'] ?? 0), $centerCap));
        $centerCapRow  = max(0, $centerCap - $centerReserve);

        $used = 0;
        while (!empty($items)) {
            $span = $getSpan($items[0]);
            if ($span > $centerCapRow) $span = $centerCapRow;
            if ($used + $span <= $centerCapRow) {
                $centerRows[$r][] = ['data' => array_shift($items), 'span' => $span];
                $used += $span;
            } else break;
        }
    }

    // RIGHT (aktif hanya jika $rightCap > 0)
    if ($rightCap > 0) {
        $rightReserve = max(0, min((int)($rowColspanRight[$r]['span'] ?? 0), $rightCap));
        $rightCapRow  = max(0, $rightCap - $rightReserve);

        $used = 0;
        while (!empty($items)) {
            $span = $getSpan($items[0]);
            if ($span > $rightCapRow) $span = $rightCapRow;
            if ($used + $span <= $rightCapRow) {
                $rightRows[$r][] = ['data' => array_shift($items), 'span' => $span];
                $used += $span;
            } else break;
        }
    }
}

// ==================== RENDER ==============================
$renderCellHtml = function ($item) {
    $c   = $item['data'];
    $spn = (int)($item['span'] ?? 1);
    $cls = 'gray-cell';
    if (isset($c['status'])) {
        if ($c['status'] === 'running')        $cls = 'bg-success text-white';
        elseif ($c['status'] === 'idle')       $cls = 'bg-info text-white';
        elseif ($c['status'] === 'sample')     $cls = 'bg-warning text-white';
        elseif ($c['status'] === 'breakdown')  $cls = 'bg-danger text-white';
    }
    $no      = esc($c['no_mc'] ?? '-');
    $jar     = esc($c['jarum'] ?? '-');
    $master  = esc($c['mastermodel'] ?? '-');
    $inisial = esc($c['inisial'] ?? '-');
    $idprod  = esc($c['id_produksi'] ?? '-');
    $idaps   = esc($c['idapsperstyle'] ?? '-');

    $colspanAttr = $spn > 1 ? 'colspan="' . esc($spn) . '"' : '';

    return '<td class="p-1 text-center" ' . $colspanAttr . '>
            <button class="cell ' . $cls . '" data-idprod="' . $idprod . '" data-idaps="' . $idaps . '">
              <div class="m-no">' . $no . '</div>
              <div class="m-code">' . $jar . '</div>
              <div class="m-mastermodel">' . $master . '</div>
              <div class="m-inisial">' . $inisial . '</div>
            </button>
          </td>';
};

// ---------- FIX: hitung gutter secara kondisional ----------
$hasCenter = ($centerCap > 0);
$hasRight  = ($rightCap > 0);
$gutters   = ($hasCenter && $hasRight) ? 2 : (($hasCenter || $hasRight) ? 1 : 0);

$totalColsPerRow =
    $leftCap +
    ($hasCenter ? $centerCap : 0) +
    ($hasRight  ? $rightCap  : 0) +
    $gutters;
echo '<tr class="top-title">
        <td colspan="' . esc($totalColsPerRow) . '" class="text-center fw-bold py-2">
            SUPERMARKET
        </td>
      </tr>';
// --------------- loop render ---------------
for ($r = 0; $r < $totalRows; $r++) {
    echo '<tr>';

    // ===== LEFT =====
    if ($r < $leftMaxRows) {
        $leftReserve = max(0, min((int)($rowColspanLeft[$r]['span'] ?? 0), $leftCap));
        $leftClass   = trim((string)($rowColspanLeft[$r]['class'] ?? 'empty-span'));
        $leftAfter   = max(0, (int)($rowColspanLeft[$r]['after_cols'] ?? 0));

        $usedCols = 0;
        $inserted = false;

        foreach ($leftRows[$r] as $it) {
            if (!$inserted && $leftReserve > 0 && $usedCols >= $leftAfter) {
                echo '<td class="' . esc($leftClass) . '" colspan="' . esc($leftReserve) . '"></td>';
                $inserted = true;
            }
            echo $renderCellHtml($it);
            $usedCols += (int)($it['span'] ?? 1);
        }
        if (!$inserted && $leftReserve > 0) {
            echo '<td class="' . esc($leftClass) . '" colspan="' . esc($leftReserve) . '"></td>';
            $inserted = true;
        }
        $pad = $leftCap - $leftReserve - $usedCols;
        if ($pad > 0) echo '<td class="left-pad" colspan="' . esc($pad) . '"></td>';
    } else {
        if ($leftCap > 0) echo '<td class="left-pad" colspan="' . esc($leftCap) . '"></td>';
    }

    // ===== GUTTER A (antara LEFT dan (CENTER/RIGHT))
    // ---------- FIX: hanya tampil jika CENTER atau RIGHT aktif ----------
    if ($hasCenter || $hasRight) {
        echo '<td class="empty-cell"></td>';
    }

    // ===== CENTER =====
    if ($hasCenter) {
        $centerReserve = max(0, min((int)($rowColspanCenter[$r]['span'] ?? 0), $centerCap));
        $centerClass   = trim((string)($rowColspanCenter[$r]['class'] ?? 'empty-span'));
        $centerAfter   = max(0, (int)($rowColspanCenter[$r]['after_cols'] ?? 0));

        $usedCols = 0;
        $inserted = false;

        foreach ($centerRows[$r] as $it) {
            if (!$inserted && $centerReserve > 0 && $usedCols >= $centerAfter) {
                echo '<td class="' . esc($centerClass) . '" colspan="' . esc($centerReserve) . '"></td>';
                $inserted = true;
            }
            echo $renderCellHtml($it);
            $usedCols += (int)($it['span'] ?? 1);
        }
        if (!$inserted && $centerReserve > 0) {
            echo '<td class="' . esc($centerClass) . '" colspan="' . esc($centerReserve) . '"></td>';
            $inserted = true;
        }
        $pad = $centerCap - $centerReserve - $usedCols;
        if ($pad > 0) echo '<td class="left-pad" colspan="' . esc($pad) . '"></td>';
    }

    // ===== GUTTER B (antara CENTER dan RIGHT)
    if ($hasCenter && $hasRight) {
        echo '<td class="empty-cell"></td>';
    }

    // ===== RIGHT =====
    // ---------- FIX: render hanya jika $rightCap > 0 ----------
    if ($hasRight) {
        $rightReserve = max(0, min((int)($rowColspanRight[$r]['span'] ?? 0), $rightCap));
        $rightClass   = trim((string)($rowColspanRight[$r]['class'] ?? 'empty-span'));
        $rightAfter   = max(0, (int)($rowColspanRight[$r]['after_cols'] ?? 0));

        $usedCols = 0;
        $inserted = false;

        foreach ($rightRows[$r] as $it) {
            if (!$inserted && $rightReserve > 0 && $usedCols >= $rightAfter) {
                echo '<td class="' . esc($rightClass) . '" colspan="' . esc($rightReserve) . '"></td>';
                $inserted = true;
            }
            echo $renderCellHtml($it);
            $usedCols += (int)($it['span'] ?? 1);
        }
        if (!$inserted && $rightReserve > 0) {
            echo '<td class="' . esc($rightClass) . '" colspan="' . esc($rightReserve) . '"></td>';
            $inserted = true;
        }
        $pad = $rightCap - $rightReserve - $usedCols;
        if ($pad > 0) echo '<td class="left-pad" colspan="' . esc($pad) . '"></td>';
    }

    echo '</tr>';

    // ===== TR PEMISAH (LORONG â€¦) =====
    if (isset($sectionRows[$r])) {
        $label = $sectionRows[$r];
        echo '<tr class="lorong-sep">
                <td colspan="' . esc($totalColsPerRow) . '" class="text-center fw-bold">'
            . esc($label) .
            '</td>
              </tr>';
    }
}
