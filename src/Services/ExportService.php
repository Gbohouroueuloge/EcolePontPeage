<?php

namespace App\Services;

class ExportService
{
  public function downloadCsv(string $filename, array $headers, array $rows): never
  {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $this->normalizeFilename($filename, 'csv') . '"');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($output, $headers, ';');

    foreach ($rows as $row) {
      fputcsv($output, $row, ';');
    }

    fclose($output);
    exit;
  }

  public function downloadJson(string $filename, array $payload): never
  {
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $this->normalizeFilename($filename, 'json') . '"');

    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
  }

  public function downloadExcel(string $filename, array $headers, array $rows): never
  {
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $this->normalizeFilename($filename, 'xls') . '"');

    echo "\xEF\xBB\xBF";
    echo '<table border="1">';
    echo '<thead><tr>';
    foreach ($headers as $header) {
      echo '<th>' . htmlspecialchars((string) $header, ENT_QUOTES, 'UTF-8') . '</th>';
    }
    echo '</tr></thead><tbody>';

    foreach ($rows as $row) {
      echo '<tr>';
      foreach ($row as $value) {
        echo '<td>' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '</td>';
      }
      echo '</tr>';
    }

    echo '</tbody></table>';
    exit;
  }

  private function normalizeFilename(string $filename, string $extension): string
  {
    $filename = preg_replace('/[^a-zA-Z0-9_-]+/', '_', strtolower($filename)) ?: 'export';
    return $filename . '.' . $extension;
  }
}
