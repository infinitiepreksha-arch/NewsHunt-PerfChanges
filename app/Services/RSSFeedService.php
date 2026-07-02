<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleXMLElement;


class RSSFeedService
{

    public function fetchFeed($url, $dataFormat)
    {
        $response = Http::get($url);
        if ($response->successful()) {
            return $this->parseFeed($response->body(), $dataFormat);
        }
        return [];
    }

    private function parseFeed($feedData, $dataFormat)
    {
        if ($dataFormat == 'xml') {
            return $this->parseXMLFeed($feedData);
        } elseif ($dataFormat == 'json') {
            return $this->parseJSONFeed($feedData);
        }
        Log::info("hey there...!");
        return [];
    }

    private function parseXMLFeed($xmlData)
    {
        $xml = new SimpleXMLElement($xmlData);
        $items = [];
        foreach ($xml->channel->item as $item) {
            $items[] = [
                'title' => (string)$item->title,
                'slug' => Str::slug((string)$item->title),
                'image' => (string)$item->enclosure['url'] ?? null,
                'description' => (string)$item->description,
                'publish_date' => (string)$item->publish_date,
            ];
        }
        return $items;
    }

    private function parseJSONFeed($jsonData)
    {
        $json = json_decode($jsonData, true);
        $items = [];
        foreach ($json['items'] as $item) {
            $items[] = [
                'title' => $item['title'],
                'slug' => Str::slug($item['title']),
                'image' => $item['image'] ?? null,
                'description' => $item['description'],
                'publish_date' => $item['publish_date'],
            ];
        }
        return $items;
    }
}
