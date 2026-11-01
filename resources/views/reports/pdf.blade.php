<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan {{ $awardCategory->name }} - {{ $festival->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
            background: #fff;
        }
        
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            background: #fff;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }
        
        .organizer-name {
            font-size: 14pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }
        
        .festival-name {
            font-size: 18pt;
            font-weight: bold;
            color: #111;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 14pt;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }
        
        .report-subtitle {
            font-size: 12pt;
            color: #666;
            margin-bottom: 10px;
        }
        
        .print-date {
            font-size: 10pt;
            color: #888;
        }
        
        .content {
            margin-top: 20px;
        }
        
        .category-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .category-info h2 {
            font-size: 14pt;
            color: #1e40af;
            margin-bottom: 10px;
        }
        
        .category-info p {
            font-size: 11pt;
            color: #555;
            margin: 5px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        thead {
            background: #1e40af;
            color: #fff;
        }
        
        th {
            padding: 10px 8px;
            text-align: left;
            font-size: 10pt;
            font-weight: 600;
            border: 1px solid #1e40af;
        }
        
        th:last-child {
            text-align: right;
        }
        
        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        tbody tr.winner {
            background: #fffbeb !important;
            border-left: 3px solid #f59e0b;
        }
        
        td {
            padding: 8px;
            font-size: 10pt;
            border: 1px solid #dee2e6;
        }
        
        td:last-child {
            text-align: right;
            font-weight: 600;
        }
        
        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            font-weight: bold;
            font-size: 10pt;
        }
        
        .rank-1 {
            background: #fbbf24;
            color: #fff;
        }
        
        .rank-2 {
            background: #9ca3af;
            color: #1f2937;
        }
        
        .rank-3 {
            background: #b45309;
            color: #fff;
        }
        
        .rank-other {
            background: #e5e7eb;
            color: #4b5563;
        }
        
        .winner-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: 600;
            margin-left: 5px;
        }
        
        .winner-1 {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fbbf24;
        }
        
        .winner-2 {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #9ca3af;
        }
        
        .winner-3 {
            background: #fef2cc;
            color: #78350f;
            border: 1px solid #d97706;
        }
        
        .winner-other {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #60a5fa;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 10pt;
            color: #666;
        }
        
        .footer p {
            margin: 3px 0;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .page {
                width: 100%;
                padding: 15mm;
                margin: 0;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            thead {
                display: table-header-group;
            }
            
            tfoot {
                display: table-footer-group;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            @if($festival->settings && $festival->settings->organizer_logo_path)
                <img src="{{ storage_path('app/public/' . $festival->settings->organizer_logo_path) }}" 
                     alt="Logo" class="logo" onerror="this.style.display='none'">
            @endif
            
            @if($festival->settings)
                <div class="organizer-name">{{ $festival->settings->organizer_name }}</div>
            @endif
            
            <div class="festival-name">{{ $festival->name }}</div>
            
            @if($festival->settings && $festival->settings->report_title)
                <div class="report-title">{{ $festival->settings->report_title }}</div>
            @else
                <div class="report-title">Laporan Hasil Penilaian</div>
            @endif
            
            <div class="report-subtitle">
                {{ $awardCategory->festivalCategory->name }} - {{ $awardCategory->name }}
            </div>
            
            <div class="print-date">
                Dicetak pada: {{ now()->format('d F Y H:i') }}
            </div>
        </div>
        
        <!-- Category Info -->
        <div class="category-info">
            <h2>Informasi Subkategori</h2>
            <p><strong>Nama Subkategori:</strong> {{ $awardCategory->name }}</p>
            <p><strong>Kategori Utama:</strong> {{ $awardCategory->festivalCategory->name }}</p>
            <p><strong>Tipe Penilaian:</strong> {{ $awardCategory->festivalCategory->assessment_type->label() }}</p>
            <p><strong>Jumlah Pemenang:</strong> {{ $awardCategory->winner_count }}</p>
            @if($awardCategory->festivalCategory->assessment_type->value === 'innovation')
                <p><strong>Jumlah Peserta:</strong> {{ $awardCategory->innovations()->count() }} Inovasi</p>
            @else
                <p><strong>Jumlah Peserta:</strong> {{ $awardCategory->institutions()->count() }} Instansi</p>
            @endif
        </div>
        
        <!-- Rankings Table -->
        <div class="content">
            @if($rankings->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">Peringkat</th>
                            <th>
                                @if($awardCategory->festivalCategory->assessment_type->value === 'innovation')
                                    Nama Inovasi
                                @else
                                    Nama Instansi
                                @endif
                            </th>
                            @if($awardCategory->festivalCategory->assessment_type->value === 'innovation')
                                <th>Inovator</th>
                            @endif
                            <th style="width: 100px;">Nilai Rata-rata</th>
                            <th style="width: 120px; text-align: center;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $isInnovation = $awardCategory->festivalCategory->assessment_type->value === 'innovation';
                            $winners = $isInnovation ? $awardCategory->innovationWinners : $awardCategory->institutionWinners;
                        @endphp
                        
                        @foreach($rankings as $ranking)
                            @php
                                $winner = $winners->firstWhere('innovation_id', $ranking->id) ?? $winners->firstWhere('institution_id', $ranking->id);
                            @endphp
                            <tr class="{{ $winner ? 'winner' : '' }}">
                                <td style="text-align: center;">
                                    @if($ranking->rank <= 3)
                                        <span class="rank-badge rank-{{ $ranking->rank }}">{{ $ranking->rank }}</span>
                                    @else
                                        <span class="rank-badge rank-other">{{ $ranking->rank }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $ranking->name }}
                                    @if($winner)
                                        <span class="winner-badge winner-{{ $winner->position <= 3 ? $winner->position : 'other' }}">
                                            Juara {{ $winner->position }}
                                        </span>
                                    @endif
                                </td>
                                @if($isInnovation)
                                    <td>{{ $ranking->innovator_name ?? '-' }}</td>
                                @endif
                                <td>{{ number_format($ranking->average_score, 2) }}</td>
                                <td style="text-align: center;">
                                    @if($winner)
                                        <strong>Juara {{ $winner->position }}</strong>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">
                    Tidak ada data peringkat untuk ditampilkan.
                </div>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="footer">
            @if($festival->settings && $festival->settings->report_footer)
                <p>{{ $festival->settings->report_footer }}</p>
            @else
                <p>Laporan ini dicetak secara otomatis dari sistem Festival Inovasi.</p>
            @endif
            <p>&copy; {{ $festival->year }} {{ $festival->settings->organizer_name ?? 'Festival Inovasi' }}. Hak cipta dilindungi.</p>
        </div>
    </div>
</body>
</html>
