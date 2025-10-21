<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrintExportService
{
    /**
     * Generate Print View for any module
     */
    public function generatePrintView($data, $template, $title, $module = null)
    {
        $user = Auth::user();
        $printData = [
            'data' => $data,
            'title' => $title,
            'module' => $module,
            'printed_by' => $user->name,
            'printed_by_id' => $user->id,
            'print_date' => now()->format('Y-m-d H:i:s'),
            'created_by' => $data->created_by ?? $user->id,
        ];

        return view($template, $printData);
    }

    /**
     * Generate CSV export
     */
    public function generateCSV($data, $title, $columns, $filename = null)
    {
        $user = Auth::user();
        $filename = $filename ?: $title . '_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $tempFile = tempnam(sys_get_temp_dir(), 'export_');
        $file = fopen($tempFile, 'w');
        
        // Add BOM for UTF-8
        fwrite($file, "\xEF\xBB\xBF");
        
        // Add title and print info
        fputcsv($file, [$title]);
        fputcsv($file, ['Printed by: ' . $user->name . ' (ID: ' . $user->id . ')']);
        fputcsv($file, ['Print Date: ' . now()->format('Y-m-d H:i:s')]);
        fputcsv($file, []); // Empty line
        
        // Add headers
        fputcsv($file, array_keys($columns));
        
        // Add data
        foreach ($data as $item) {
            $row = [];
            foreach ($columns as $key => $column) {
                $value = is_array($item) ? ($item[$key] ?? '') : ($item->$key ?? '');
                $row[] = $value;
            }
            fputcsv($file, $row);
        }
        
        fclose($file);
        
        return [
            'file' => $tempFile,
            'filename' => $filename,
            'mime' => 'text/csv'
        ];
    }

    /**
     * Generate HTML export
     */
    public function generateHTML($data, $title, $columns, $filename = null)
    {
        $user = Auth::user();
        $filename = $filename ?: $title . '_' . now()->format('Y-m-d_H-i-s') . '.html';
        
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $title . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .info { background: #f9f9f9; padding: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>' . $title . '</h1>
    </div>
    
    <div class="info">
        <p><strong>Printed by:</strong> ' . $user->name . ' (ID: ' . $user->id . ')</p>
        <p><strong>Print Date:</strong> ' . now()->format('Y-m-d H:i:s') . '</p>
    </div>
    
    <table>
        <thead>
            <tr>';
        
        foreach ($columns as $column) {
            $html .= '<th>' . $column . '</th>';
        }
        
        $html .= '</tr>
        </thead>
        <tbody>';
        
        foreach ($data as $item) {
            $html .= '<tr>';
            foreach ($columns as $key => $column) {
                $value = is_array($item) ? ($item[$key] ?? '') : ($item->$key ?? '');
                $html .= '<td>' . htmlspecialchars($value) . '</td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</tbody>
    </table>
</body>
</html>';
        
        $tempFile = tempnam(sys_get_temp_dir(), 'export_');
        file_put_contents($tempFile, $html);
        
        return [
            'file' => $tempFile,
            'filename' => $filename,
            'mime' => 'text/html'
        ];
    }

    /**
     * Get print info for any record
     */
    public function getPrintInfo($record)
    {
        $user = Auth::user();
        return [
            'printed_by' => $user->name,
            'printed_by_id' => $user->id,
            'print_date' => now()->format('Y-m-d H:i:s'),
            'created_by' => $record->created_by ?? 'System',
            'created_date' => $record->created_at->format('Y-m-d H:i:s') ?? 'N/A',
        ];
    }
}
