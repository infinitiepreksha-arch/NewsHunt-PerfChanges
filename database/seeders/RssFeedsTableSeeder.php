<?php

namespace Database\Seeders;

use App\Models\RssFeed;
use Illuminate\Database\Seeder;

class RssFeedsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RssFeed::create([
            'channel_id' => 1,
            'topic_id' => 11,
            'feed_url' => 'https://moxie.foxnews.com/google-publisher/world.xml',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ],
        [
            'channel_id' => 1,
            'topic_id' => 6,
            'feed_url' => 'https://moxie.foxnews.com/google-publisher/politics.xml',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ],
        [
            'channel_id' => 1,
            'topic_id' => 8,
            'feed_url' => 'https://moxie.foxnews.com/google-publisher/science.xml',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ],
        [
            'channel_id' => 1,
            'topic_id' => 5,
            'feed_url' => 'https://moxie.foxnews.com/google-publisher/health.xml',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ],
        [
            'channel_id' => 1,
            'topic_id' => 1,
            'feed_url' => 'https://moxie.foxnews.com/google-publisher/sports.xml',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ],
        [
            'channel_id' => 3,
            'topic_id' => 7,
            'feed_url' => 'https://www.indiatvnews.com/rssnews/topstory-lifestyle.xml',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ],
        [
            'channel_id' => 3,
            'topic_id' => 2,
            'feed_url' => 'https://www.indiatvnews.com/rssnews/topstory-business.xml',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ],
        [
            'channel_id' => 3,
            'topic_id' => 11,
            'feed_url' => 'https://www.indiatvnews.com/rssnews/topstory-world.xml',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ],
        [
            'channel_id' => 3,
            'topic_id' => 1,
            'feed_url' => 'https://www.indiatvnews.com/rssnews/topstory-sports.xml',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ],
        [
            'channel_id' => 3,
            'topic_id' => 4,
            'feed_url' => 'https://www.indiatvnews.com/rssnews/topstory-entertainment.xml',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ],
        [
            'channel_id' => 2,
            'topic_id' => 11,
            'feed_url' => 'https://timesofindia.indiatimes.com/rssfeeds/296589292.cms',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ],
        [
            'channel_id' => 2,
            'topic_id' => 2,
            'feed_url' => 'https://timesofindia.indiatimes.com/rssfeeds/1898055.cms',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ],
        [
            'channel_id' => 2,
            'topic_id' => 1,
            'feed_url' => 'https://timesofindia.indiatimes.com/rssfeeds/4719148.cms',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ],
        [
            'channel_id' => 2,
            'topic_id' => 4,
            'feed_url' => 'https://timesofindia.indiatimes.com/rssfeeds/1081479906.cms',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ],
        [
            'channel_id' => 2,
            'topic_id' => 8,
            'feed_url' => 'https://timesofindia.indiatimes.com/rssfeeds/1081479906.cms',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ],
        [
            'channel_id' => 2,
            'topic_id' => 7,
            'feed_url' => 'https://timesofindia.indiatimes.com/rssfeeds/2886704.cms',
            'data_format' => 'XML',
            'sync_interval' => 60,
            'status' => 'active',
        ]);
    }
}
