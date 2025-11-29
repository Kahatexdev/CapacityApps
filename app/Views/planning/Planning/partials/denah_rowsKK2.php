<?php

// ======================== KONFIGURASI SECTION ========================
// B2 (sesuai contohmu)
$B2_rows = 8;
$B2_caps = ['left' => 15, 'center' => 5, 'right' => 0];

// A2 (sesuai contohmu)
$A2_rows = 11; // jumlah baris B2; kalau mau 10, ubah ke 10
$A2_caps = ['left' => 15, 'center' => 15, 'right' => 15];

// urutan render blok
$sections = [
    ['key' => 'A2', 'label' => 'Mesin A2', 'rows' => $A2_rows, 'caps' => $A2_caps],
    ['key' => 'B2', 'label' => 'Mesin B2', 'rows' => $B2_rows, 'caps' => $B2_caps],
];

// ambang id untuk mulai B2
$idStartB2 = 1838;

// ======================== UTIL & SPAN MAP ============================
$normUpper = function ($s) {
    $s = (string)$s;
    $s = trim($s);
    $s = preg_replace('/\s+/', ' ', $s);
    return strtoupper($s);
};
$makeKey = function ($cell) {
    $idMC = (int)($cell['id'] ?? 0);
    return "{$idMC}";
};

// (opsional) atur colspan spesifik id mesin
$colspanMapRaw = [
    // '1838,1839' => 2,
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

// ======================== PISAH & URUTKAN DATA =======================
// Ambil semua item
$allItems = array_values(is_array($layout) ? $layout : []);

// Urutkan ascending berdasarkan id agar pembagian A2/B2 konsisten dari id kecil -> besar
usort($allItems, function ($a, $b) {
    return ((int)($a['id'] ?? 0)) <=> ((int)($b['id'] ?? 0));
});

// Partisi berdasar aturan: id >= 1838 -> B2, selain itu -> A2
$itemsBySection = ['A2' => [], 'B2' => []];
foreach ($allItems as $c) {
    $idMC = (int)($c['id'] ?? 0);
    if ($idMC >= $idStartB2) {
        $itemsBySection['B2'][] = $c;
    } else {
        $itemsBySection['A2'][] = $c;
    }
}

// ======================== OPSIONAL: placeholder per baris ============
// Mapping per section, index baris LOKAL (0..rows-1)
// Format: rowIndex => ['span'=>INT, 'class'=>'empty-span', 'after_cols'=>INT]
$rowColspanLeftBySec = [
    'A2' => [
        // 0 => ['span'=>2,'class'=>'empty-span','after_cols'=>0],
    ],
    'B2' => [
        // 3 => ['span'=>1,'class'=>'empty-span','after_cols'=>5],
    ],
];
$rowColspanCenterBySec = [
    'A2' => [],
    'B2' => [],
];
$rowColspanRightBySec  = [
    'A2' => [],
    'B2' => [],
];

// ======================== RENDER CELL ================================
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

    $colspanAttr = $spn > 1 ? ' colspan="' . esc($spn) . '"' : '';

    return '<td class="p-1 text-center"' . $colspanAttr . '>
        <button class="cell ' . $cls . '" data-idprod="' . $idprod . '" data-idaps="' . $idaps . '">
            <div class="m-no">' . $no . '</div>
            <div class="m-code">' . $jar . '</div>
            <div class="m-mastermodel">' . $master . '</div>
            <div class="m-inisial">' . $inisial . '</div>
        </button>
    </td>';
};

// ======================== MULAI RENDER ===============================
?>
        <?php foreach ($sections as $sec): ?>
            <?php
            $secKey   = $sec['key'];         // 'A2' / 'B2'
            $secLabel = $sec['label'];       // 'LORONG A2' / 'LORONG B2'
            $rows     = (int)$sec['rows'];

            $leftCap   = (int)($sec['caps']['left']   ?? 0);
            $centerCap = (int)($sec['caps']['center'] ?? 0);
            $rightCap  = (int)($sec['caps']['right']  ?? 0);

            $hasCenter = ($centerCap > 0);
            $hasRight  = ($rightCap  > 0);
            $gutters   = ($hasCenter && $hasRight) ? 2 : (($hasCenter || $hasRight) ? 1 : 0);
            $totalColsPerRow =
                $leftCap +
                ($hasCenter ? $centerCap : 0) +
                ($hasRight  ? $rightCap  : 0) +
                $gutters;

            // ambil referensi item untuk blok ini (sesuai aturan id >= 1838 → B2)
            $items = &$itemsBySection[$secKey];
            ?>

            <!-- TR pemisah lorong -->
            <tr class="section-sep">
                <td colspan="<?= esc($totalColsPerRow) ?>" class="text-center fw-bold">
                    <?= esc($secLabel) ?>
                </td>
            </tr>

            <?php for ($r = 0; $r < $rows; $r++): ?>
                <?php
                // keranjang baris lokal
                $leftRow   = [];
                $centerRow = [];
                $rightRow  = [];

                // --------- LEFT (alokasi item) ----------
                if ($leftCap > 0) {
                    $leftCfg     = $rowColspanLeftBySec[$secKey][$r] ?? null;
                    $leftReserve = max(0, min((int)($leftCfg['span'] ?? 0), $leftCap));
                    $leftCapRow  = max(0, $leftCap - $leftReserve);

                    $used = 0;
                    while (!empty($items)) {
                        $span = $getSpan($items[0]);
                        if ($span > $leftCapRow) $span = $leftCapRow;
                        if ($used + $span <= $leftCapRow) {
                            $leftRow[] = ['data' => array_shift($items), 'span' => $span];
                            $used += $span;
                        } else break;
                    }
                }

                // --------- CENTER (alokasi item) ----------
                if ($hasCenter) {
                    $centerCfg     = $rowColspanCenterBySec[$secKey][$r] ?? null;
                    $centerReserve = max(0, min((int)($centerCfg['span'] ?? 0), $centerCap));
                    $centerCapRow  = max(0, $centerCap - $centerReserve);

                    $used = 0;
                    while (!empty($items)) {
                        $span = $getSpan($items[0]);
                        if ($span > $centerCapRow) $span = $centerCapRow;
                        if ($used + $span <= $centerCapRow) {
                            $centerRow[] = ['data' => array_shift($items), 'span' => $span];
                            $used += $span;
                        } else break;
                    }
                }

                // --------- RIGHT (alokasi item) ----------
                if ($hasRight) {
                    $rightCfg     = $rowColspanRightBySec[$secKey][$r] ?? null;
                    $rightReserve = max(0, min((int)($rightCfg['span'] ?? 0), $rightCap));
                    $rightCapRow  = max(0, $rightCap - $rightReserve);

                    $used = 0;
                    while (!empty($items)) {
                        $span = $getSpan($items[0]);
                        if ($span > $rightCapRow) $span = $rightCapRow;
                        if ($used + $span <= $rightCapRow) {
                            $rightRow[] = ['data' => array_shift($items), 'span' => $span];
                            $used += $span;
                        } else break;
                    }
                }
                ?>

                <tr>
                    <?php
                    // ---------- RENDER LEFT ----------
                    if ($leftCap > 0) {
                        $leftCfg     = $rowColspanLeftBySec[$secKey][$r] ?? null;
                        $leftReserve = max(0, min((int)($leftCfg['span'] ?? 0), $leftCap));
                        $leftClass   = trim((string)($leftCfg['class'] ?? 'empty-span'));
                        $leftAfter   = max(0, (int)($leftCfg['after_cols'] ?? 0));

                        $usedCols = 0;
                        $inserted = false;

                        foreach ($leftRow as $it) {
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
                    }

                    // ---------- GUTTER A ----------
                    if ($hasCenter || $hasRight) {
                        echo '<td class="empty-cell"></td>';
                    }

                    // ---------- RENDER CENTER ----------
                    if ($hasCenter) {
                        $centerCfg     = $rowColspanCenterBySec[$secKey][$r] ?? null;
                        $centerReserve = max(0, min((int)($centerCfg['span'] ?? 0), $centerCap));
                        $centerClass   = trim((string)($centerCfg['class'] ?? 'empty-span'));
                        $centerAfter   = max(0, (int)($centerCfg['after_cols'] ?? 0));

                        $usedCols = 0;
                        $inserted = false;

                        foreach ($centerRow as $it) {
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

                    // ---------- GUTTER B ----------
                    if ($hasCenter && $hasRight) {
                        echo '<td class="empty-cell"></td>';
                    }

                    // ---------- RENDER RIGHT ----------
                    if ($hasRight) {
                        $rightCfg     = $rowColspanRightBySec[$secKey][$r] ?? null;
                        $rightReserve = max(0, min((int)($rightCfg['span'] ?? 0), $rightCap));
                        $rightClass   = trim((string)($rightCfg['class'] ?? 'empty-span'));
                        $rightAfter   = max(0, (int)($rightCfg['after_cols'] ?? 0));

                        $usedCols = 0;
                        $inserted = false;

                        foreach ($rightRow as $it) {
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
                    ?>
                </tr>
            <?php endfor; ?>

            <?php unset($items); // putus referensi section ini 
            ?>
        <?php endforeach; ?>