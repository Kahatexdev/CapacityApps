<?php
// partial: hanya menghasilkan <tr>...</tr> rows
// dibutuhkan: $layout (array)

$leftCap = 17;
$leftMaxRows = 3;
$centerCap = 15;
$centerMaxRows = 13;
$rightCap = 13;
$rightMaxRows = 13;
$totalRows = max($centerMaxRows, $rightMaxRows);

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

$colspanMapRaw = [
    '1' => 2,
    '15' => 2,
    '25' => 2,
    '41' => 2,
    '55' => 2,
    '81' => 2,
    '95' => 2,
    '25' => 2,
    '26' => 2,
    '27' => 2,
    '65' => 2,
    '66' => 2,
    '67' => 2,
    '105' => 2,
    '106' => 2,
    '107' => 2,
    '188' => 2,
    '189' => 2,
    '214' => 2,
    '215' => 2,
    '216' => 2
];
// GANTI seluruh blok build $colspanMap dengan ini:
$colspanMap = [];
foreach ($colspanMapRaw as $key => $val) {
    // support key bertipe "25" atau "25,26,27" (opsional)
    foreach (preg_split('/\s*,\s*/', (string)$key) as $idStr) {
        if ($idStr === '') continue;
        $idMC = (int)$idStr;
        if ($idMC > 0) {
            $colspanMap[(string)$idMC] = (int)$val;
        }
    }
}

// getSpan tetap sama
$getSpan = function ($cell) use ($makeKey, $colspanMap) {
    $k = $makeKey($cell);
    return max(1, ($colspanMap[$k] ?? 1));
};

$leftRows = array_fill(0, $leftMaxRows, []);
$centerRows = array_fill(0, $totalRows, []);
$rightRows  = array_fill(0, $totalRows, []);

// Pastikan $layout array agar array_values() aman
$items = array_values(is_array($layout) ? $layout : []);
for ($r = 0; $r < $totalRows && !empty($items); $r++) {
    if ($r < $leftMaxRows) {
        $used = 0;
        while (!empty($items)) {
            $span = $getSpan($items[0]);
            if ($span > $leftCap) $span = $leftCap;
            if ($used + $span <= $leftCap) {
                $leftRows[$r][] = ['data' => array_shift($items), 'span' => $span];
                $used += $span;
            } else break;
        }
    }

    $used = 0;
    while (!empty($items)) {
        $span = $getSpan($items[0]);
        if ($span > $centerCap) $span = $centerCap;
        if ($used + $span <= $centerCap) {
            $centerRows[$r][] = ['data' => array_shift($items), 'span' => $span];
            $used += $span;
        } else break;
    }

    $used = 0;
    while (!empty($items)) {
        $span = $getSpan($items[0]);
        if ($span > $rightCap) $span = $rightCap;
        if ($used + $span <= $rightCap) {
            $rightRows[$r][] = ['data' => array_shift($items), 'span' => $span];
            $used += $span;
        } else break;
    }
}

// helper renderCell
$renderCellHtml = function ($item) {
    $c = $item['data'];
    $spn = (int)($item['span'] ?? 1);
    $cls = 'gray-cell';
    if (isset($c['status'])) {
        if ($c['status'] === 'running') {
            $cls = 'bg-success text-white';
        } elseif ($c['status'] === 'idle') {
            $cls = 'bg-info text-white';
        } elseif ($c['status'] === 'sample') {
            $cls = 'bg-warning text-white';
        } elseif ($c['status'] === 'breakdown') {
            $cls = 'bg-danger text-white';
        }
    }

    $idMC = esc($c['id'] ?? '-');
    $no = esc($c['no_mc'] ?? '-');
    $jar = esc($c['jarum'] ?? '-');
    $master = esc($c['mastermodel'] ?? '-');
    $inisial = esc($c['inisial'] ?? '-');
    $idprod = esc($c['id_produksi'] ?? '-');
    $idaps = esc($c['idapsperstyle'] ?? '-');

    $colspanAttr = $spn > 1 ? 'colspan="' . esc($spn) . '"' : '';

    // return string
    return "<td class=\"p-1 text-center\" {$colspanAttr}>
                <button class=\"cell {$cls}\" data-idprod=\"{$idprod}\" data-idaps=\"{$idaps}\">
                    <div class=\"m-no\">{$no}</div>
                    <div class=\"m-code\">{$jar}</div>
                    <div class=\"m-mastermodel\">{$master}</div>
                    <div class=\"m-inisial\">{$inisial}</div>
                </button>
            </td>";
};

// render rows → echo only <tr>...</tr>
for ($r = 0; $r < $totalRows; $r++) {
    echo '<tr>';

    if ($r < $leftMaxRows) {
        $used = 0;
        foreach ($leftRows[$r] as $it) {
            echo $renderCellHtml($it);
            $used += (int)($it['span'] ?? 1);
        }
        if ($used < $leftCap) {
            echo '<td class="left-pad" colspan="' . esc($leftCap - $used) . '"></td>';
        }
    } elseif ($r === $leftMaxRows) {
        $rowspanLeft = $totalRows - $leftMaxRows;
        echo '<td class="left-pad" colspan="' . esc($leftCap) . '" rowspan="' . esc($rowspanLeft) . '"></td>';
    }

    if ($r === 0) {
        echo '<td class="empty-cell" rowspan="' . esc($totalRows) . '"></td>';
    }

    // CENTER
    $used = 0;
    foreach ($centerRows[$r] as $it) {
        echo $renderCellHtml($it);
        $used += (int)($it['span'] ?? 1);
    }
    if ($used < $centerCap) {
        echo '<td class="left-pad" colspan="' . esc($centerCap - $used) . '"></td>';
    }

    if ($r === 0) {
        echo '<td class="empty-cell" rowspan="' . esc($totalRows) . '"></td>';
    }

    // RIGHT
    $used = 0;
    foreach ($rightRows[$r] as $it) {
        echo $renderCellHtml($it);
        $used += (int)($it['span'] ?? 1);
    }
    if ($used < $rightCap) {
        echo '<td class="left-pad" colspan="' . esc($rightCap - $used) . '"></td>';
    }

    echo '</tr>';
}
