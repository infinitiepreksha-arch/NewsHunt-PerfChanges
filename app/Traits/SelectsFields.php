<?php

namespace App\Traits;

use App\Constants\DatabaseFields;

trait SelectsFields
{
    /******Post Description Fields************/
    protected function selectPostDescriptionFields($additionalFields = [])
    {
        $fields = [
            DatabaseFields::POST_ID,
            DatabaseFields::CHANNEL_NAME,
            DatabaseFields::CHANNEL_LOGO,
            DatabaseFields::CHANNEL_SLUG,
            DatabaseFields::TOPIC_NAME,
            'topics.id as topic_id',
            DatabaseFields::POST_TITLE,
            'posts.type',
            'posts.audio',
            DatabaseFields::POST_SLUG,
            DatabaseFields::POST_IMAGE,
            'posts.video_thumb',
            'posts.video',
            DatabaseFields::POST_DESCRIPTION,
            DatabaseFields::POST_STATUS,
            DatabaseFields::POST_PUBLISH_DATE,
            DatabaseFields::POST_VIEW_COUNT,
            DatabaseFields::POST_REACTION,
            DatabaseFields::POST_SHERE,
            DatabaseFields::POST_COMMENT,
            DatabaseFields::POST_FAVORITE,
            DatabaseFields::POST_RESOURCE,
            'posts.pubdate'
        ];

        return array_merge($fields, $additionalFields);
    }


    /******Banner Post Fields************/
    protected function selectBannerPosts($additionalFields = [])
    {
        $fields = [
            DatabaseFields::POST_ID,
            DatabaseFields::CHANNEL_NAME,
            DatabaseFields::CHANNEL_SLUG,
            DatabaseFields::TOPIC_NAME,
            DatabaseFields::POST_TITLE,
            DatabaseFields::POST_SLUG,
            DatabaseFields::POST_IMAGE,
            DatabaseFields::POST_DESCRIPTION,
            DatabaseFields::POST_STATUS,
            DatabaseFields::POST_COMMENT,
            DatabaseFields::POST_FAVORITE,
            DatabaseFields::POST_VIEW_COUNT,
            DatabaseFields::POST_PUBLISH_DATE
        ];

        return array_merge($fields, $additionalFields);
    }

    /*****Popular Post Fields***********/
    protected function selectPopularPostFields($additionalFields = [])
    {
        $fields = [
            DatabaseFields::POST_ID,
            DatabaseFields::CHANNEL_NAME,
            DatabaseFields::CHANNEL_SLUG,
            DatabaseFields::CHANNEL_LOGO,
            DatabaseFields::TOPIC_NAME,
            DatabaseFields::POST_SLUG,
            DatabaseFields::POST_TYPE,
            DatabaseFields::POST_TITLE,
            DatabaseFields::POST_THUMB,
            DatabaseFields::POST_VIDEO,
            DatabaseFields::POST_IMAGE,
            DatabaseFields::POST_COMMENT,
            DatabaseFields::POST_FAVORITE,
            DatabaseFields::POST_VIEW_COUNT,
            DatabaseFields::POST_PUBLISH_DATE,
            // "posts.publish_date"
        ];

        return array_merge($fields, $additionalFields);
    }

    /*****Recommanded Post & Topics  Fields***********/
    protected function recommandedfetchPosts($additionalFields = [])
    {
        $fields = [
            DatabaseFields::POST_ID,
            DatabaseFields::CHANNEL_NAME,
            DatabaseFields::CHANNEL_SLUG,
            DatabaseFields::CHANNEL_LOGO,
            DatabaseFields::TOPIC_NAME,
            DatabaseFields::POST_SLUG,
            DatabaseFields::POST_TYPE,
            DatabaseFields::POST_TITLE,
            DatabaseFields::POST_THUMB,
            DatabaseFields::POST_VIDEO,
            DatabaseFields::POST_IMAGE,
            DatabaseFields::POST_COMMENT,
            DatabaseFields::POST_FAVORITE,
            DatabaseFields::POST_VIEW_COUNT,
            DatabaseFields::POST_PUBLISH_DATE
        ];

        return array_merge($fields, $additionalFields);
    }
}

