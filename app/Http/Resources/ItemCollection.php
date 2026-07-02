<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use JsonSerializable;
use Throwable;

class ItemCollection extends ResourceCollection {
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     * @throws Throwable
     */
    public function toArray(Request $request) {
        try {
            $response = [];
            foreach ($this->collection as $key => $collection) {
                $response[$key] = $this->formatCollection($collection);
            }
    
            $response = $this->sortFeaturedRows($response);
    
            if ($this->resource instanceof AbstractPaginator) {
                return [
                    ...$this->resource->toArray(),
                    'data' => $response
                ];
            }
    
            return $response;
    
        } catch (Throwable $th) {
            return $th;
        }
    }
    
    private function formatCollection($collection) {
        $response = $collection->toArray();
    
        $response['is_feature'] = $this->isFeatureLoaded($collection);
        $response['total_likes'] = $this->totalLikes($collection);
        $response['is_liked'] = $this->isLiked($collection);
        $response['custom_fields'] = $this->customFields($collection);
        $response['is_already_offered'] = $this->isAlreadyOffered($collection);
        $response['is_already_reported'] = $this->isAlreadyReported($collection);
    
        return $response;
    }
    
    private function isFeatureLoaded($collection) {
        return $collection->relationLoaded('featured_items') && count($collection->featured_items) > 0;
    }
    
    private function totalLikes($collection) {
        return $collection->relationLoaded('favourites') ? $collection->favourites->count() : 0;
    }
    
    private function isLiked($collection) {
        if (Auth::check()) {
            return $collection->relationLoaded('favourites') &&
                   $collection->favourites->where('item_id', $collection->id)
                                          ->where('user_id', Auth::user()->id)
                                          ->count() > 0;
        }
        return false;
    }
    
    private function customFields($collection) {
        if (!$collection->relationLoaded('item_custom_field_values')) {
            return [];
        }
    
        $customFields = [];
        foreach ($collection->item_custom_field_values as $key => $customFieldValue) {
            if ($customFieldValue->relationLoaded('custom_field') && !empty($customFieldValue->custom_field)) {
                $customFields[$key] = $this->formatCustomField($customFieldValue);
            }
        }
    
        return $customFields;
    }
    
    private function formatCustomField($customFieldValue) {
        $tempRow = $customFieldValue->custom_field->toArray();
    
        if ($customFieldValue->custom_field->type == "fileinput") {
            $tempRow['value'] = !empty($customFieldValue->value) ? [url(Storage::url($customFieldValue->value))] : [];
        } else {
            $tempRow['value'] = $customFieldValue->value ?? [];
        }
    
        $tempRow['custom_field_value'] = !empty($customFieldValue) ? $customFieldValue->toArray() : (object)[];
        unset($tempRow['custom_field_value']['custom_field']);
    
        return $tempRow;
    }
    
    private function isAlreadyOffered($collection) {
        if (Auth::check()) {
            return $collection->relationLoaded('item_offers') &&
                   $collection->item_offers->where('item_id', $collection->id)
                                           ->where('buyer_id', Auth::user()->id)
                                           ->count() > 0;
        }
        return false;
    }
    
    private function isAlreadyReported($collection) {
        if (Auth::check()) {
            return $collection->relationLoaded('user_reports') &&
                   $collection->user_reports->where('user_id', Auth::user()->id)
                                            ->count() > 0;
        }
        return false;
    }
    
    private function sortFeaturedRows($response) {
        $featuredRows = [];
        $normalRows = [];
    
        foreach ($response as $value) {
            if ($value['is_feature']) {
                $featuredRows[] = $value;
            } else {
                $normalRows[] = $value;
            }
        }
    
        return array_merge($featuredRows, $normalRows);
    }
    
}
