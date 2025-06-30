<?php

namespace App\Helpers;

class DocumentHelper
{
    public static function getDocumentPreviewHtml(?string $documentPath, $record): string
    {
        if (!$documentPath) {
            return '<span class="text-gray-500 text-sm">Tidak ada dokumen</span>';
        }

        $fileName = basename($documentPath);
        $fileExtension = strtolower(pathinfo($documentPath, PATHINFO_EXTENSION));
        $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        $isPdf = $fileExtension === 'pdf';

        $previewUrl = route('izin.document.preview', $record);
        $downloadUrl = route('izin.document.download', $record);

        $html = '<div class="document-preview-wrapper">';

        // File type badge
        if ($isImage) {
            $html .= '<span class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full mr-2">IMG</span>';
        } elseif ($isPdf) {
            $html .= '<span class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full mr-2">PDF</span>';
        } else {
            $html .= '<span class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-full mr-2">DOC</span>';
        }

        // Action buttons
        $html .= '<div class="inline-flex items-center space-x-1">';
        $html .= '<a href="' . $previewUrl . '" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium" title="Preview ' . $fileName . '">';
        $html .= '<svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
        $html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>';
        $html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
        $html .= '</svg>';
        $html .= '</a>';

        $html .= '<a href="' . $downloadUrl . '" class="text-green-600 hover:text-green-800 text-sm font-medium ml-1" title="Download ' . $fileName . '">';
        $html .= '<svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
        $html .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>';
        $html .= '</svg>';
        $html .= '</a>';
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }

    public static function getFileTypeIcon(string $extension): string
    {
        return match(strtolower($extension)) {
            'pdf' => '📄',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => '🖼️',
            'doc', 'docx' => '📝',
            'xls', 'xlsx' => '📊',
            'ppt', 'pptx' => '📊',
            'zip', 'rar' => '📦',
            default => '📋'
        };
    }

    public static function getFileTypeBadgeColor(string $extension): string
    {
        return match(strtolower($extension)) {
            'pdf' => 'red',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'blue',
            'doc', 'docx' => 'indigo',
            'xls', 'xlsx' => 'green',
            'ppt', 'pptx' => 'orange',
            'zip', 'rar' => 'purple',
            default => 'gray'
        };
    }
}
