<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $bookFormats = $this->whenLoaded('bookFormats') ?? collect();

        // Get viewable formats with priority: HTML > PDF > TXT
        $viewableUrl = $this->getViewableUrl($bookFormats);

        // Get cover image URL
        $coverImageUrl = $this->getCoverImageUrl($bookFormats);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'download_count' => $this->download_count ?? 0,
            'media_type' => $this->media_type,
            'viewable_url' => $viewableUrl,
            'cover_image_url' => $coverImageUrl,
            'authors' => $this->whenLoaded('authors'),
            'languages' => $this->whenLoaded('languages'),
            'subjects' => $this->whenLoaded('subjects'),
            'bookshelves' => $this->whenLoaded('bookshelves'),
            'book_formats' => $bookFormats,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function getViewableUrl($bookFormats): ?string
    {
        if (!$bookFormats) return null;

        // Priority order: HTML, PDF, TXT
        $priorityOrder = [
            'text/html',
            'application/pdf',
            'text/plain'
        ];

        foreach ($priorityOrder as $mimeType) {
            $format = $bookFormats->first(function ($format) use ($mimeType) {
                return str_contains($format->mime_type, $mimeType) && !str_contains($format->url, '.zip');
            });

            if ($format) {
                return $format->url;
            }
        }

        return null;
    }

    private function getCoverImageUrl($bookFormats): ?string
    {
        if (!$bookFormats) return null;

        // Look for image formats, preferring medium or large sizes
        $imageFormats = $bookFormats->filter(function ($format) {
            return str_starts_with($format->mime_type, 'image/');
        });


        // Fallback to any image
        $anyImage = $imageFormats->first();
        return $anyImage ? $anyImage->url : null;
    }
}
