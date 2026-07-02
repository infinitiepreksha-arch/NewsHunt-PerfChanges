<?php

/*
    Points to Note:
    1.  No need to put svg in any children.
    2.  Any nav that does not have Ability key will be available for all users.
    3.  if any child does not have any ability key then that child and its parent will be available for all roles.
    4.  if you want to explicitly give ability to parent for all the roles then put ability in parent as ["*"].
    5.  Name is a label that will be translated in view.
    6.  All the routes in the nav should be named.
    7.  Add ability to
        - Main category only when it has no child and has route in it.
        - if child has route the add ability to the child not to the parent.
    8.  If no svg is given then default svg is Home svg.
    9.  Refer /config/rolepermission.php for all the available abilities.
*/

$nav = [
    [
        "name"  => "message.DASHBOARD",
        "route" => 'dashboard',
        "svg"   => "home",
    ],

    // [
    //     "name"  => "page.CREATE_POSTS",
    //     "route" => 'list-of-posts',
    //     "svg"   => "post",
    // ],

    [
        "name"     => "page.POSTS",
        "children" => [
            [
                "name"  => "page.IMAGE_POSTS",
                "route" => 'posts.index',
                "svg"   => "post",
            ],
            [
                "name"  => "page.VIDEO_POSTS",
                "route" => "videos.index",
                "svg"   => "videos",
            ],
            [
                "name"  => "page.AUDIO_POSTS",
                "route" => "audios.index",
                "svg"   => "audios",
            ],
        ],
        "svg"      => "post",
    ],

    [
        "name"  => "page.WEB_STORIES",
        "route" => "stories.publicIndex",
        "svg"   => "web_story",
    ],

    [
        "name"  => "page.NEWS_LANGUAGES",
        "route" => 'news-languages.index',
        "svg"   => "news languages",
    ],
    [
        "name"    => "page.CHANNELS",
        "route"   => 'channels.index',
        "ability" => ['list-channel', 'create-channel', 'update-channel', 'delete-channel', 'update-status-channel'],
        "svg"     => "channels",
    ],
    [
        "name"     => "page.TOPICS",
        "children" => [
            [
                "name"  => "page.ALL_TOPICS",
                "route" => 'topics.index',
            ],
            [
                "name"  => "page.TOPICS_ORDER",
                "route" => 'topics.order',
            ],
        ],
        "svg"      => "topic",
    ],

    [
        "name"  => "page.RSS_FEEDS",
        "route" => 'rss-feeds.index',
        "svg"   => "rss-feed",
    ],
    [
        "name"  => "page.E_NEWSPAPERS_AND_MAGAZINES",
        "route" => 'e-newspapers.index',
        "svg"   => "e-paper",
    ],

    [
        "name"  => "page.SPONSORED_ADS",
        "route" => 'custom-ads-request.index',
        "svg"   => "custom-ads",
    ],

    [
        "name"    => "page.USERS",
        "route"   => 'users.index',
        "ability" => ['customer-list', 'customer-create', 'customer-update', 'customer-delete'],
        "svg"     => "customer",
    ],
    [
        "name"  => "page.MEMBERSHIP_PLANS",
        "route" => 'pricing-plans.index',
        "svg"   => 'membership',
    ],
    [
        "name"  => "page.SUBSCRIPTIONS",
        "route" => 'subscription.index',
        "svg"   => 'subscription',
    ],
    [
        "name"  => "page.TRANSACTIONS",
        "route" => 'admin.transactions.index',
        "svg"   => 'transaction',
    ],
    [
        "name"     => "page.EMAIL_TEMPLATE",
        "children" => [
            [
                "name"  => "page.POST_EMAILS",
                "route" => "email-template.index",
            ],
            [
                "name"  => "page.SPONSOR_ADS_EMAILS",
                "route" => "email-Sponsor-Ads.index",
            ],
        ],
    ],

    [
        "name"  => "page.SUBSCRIBERS",
        "route" => 'subscriber.index',
        "svg"   => "subscriber",
    ],

    [
        "name"  => "page.NOTIFICATION",
        "route" => 'notification.index',
        "svg"   => "notification",
    ],
    [
        "name"  => "page.REPORTED_COMMENTS",
        "route" => 'report-comments.index',
        "svg"   => "comment-spam",
    ],
    [
        "name"  => "page.BLOCKED_COMMENTS",
        "route" => 'blocked-comments.index',
        "svg"   => "comment-spam",
    ],
    [
        "name"  => "page.CONTACT_US",
        "route" => 'contact-us.index',
        "svg"   => "contact-us",
    ],

    [
        "name"     => "page.ADMIN_USERS",
        "svg"      => "admin",
        "children" => [
            [
                "name"    => "page.ROLES",
                "route"   => "roles.index",
                "ability" => ['list-role', 'create-role', 'delete-role', 'update-role'],
            ],
            [
                "name"    => "page.ADMINS",
                "route"   => "admin-users.index",
                "ability" => ['list-adminuser', 'create-adminuser', 'delete-adminuser'],

            ],
        ],
    ],
    [
        "name"  => "page.SETTINGS",
        "route" => "settings.index",
        "svg"   => 'settings',
    ],
];

$svgs = [

    "home"           => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-home">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
              <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
              <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
            </svg>',

    "countries"      => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-world">
                 <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                 <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                 <path d="M3.6 9h16.8" />
                 <path d="M3.6 15h16.8" />
                 <path d="M11.5 3a17 17 0 0 0 0 18" />
                 <path d="M12.5 3a17 17 0 0 1 0 18" />
                 </svg>',

    "admin"          => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user-cog">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                <path d="M6 21v-2a4 4 0 0 1 4 -4h2.5" />
                <path d="M19.001 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                <path d="M19.001 15.5v1.5" />
                <path d="M19.001 21v1.5" />
                <path d="M22.032 17.25l-1.299 .75" />
                <path d="M17.27 20l-1.3 .75" />
                <path d="M15.97 17.25l1.3 .75" />
                <path d="M20.733 20l1.3 .75" />
                </svg>',

    "Profile"        => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user-screen"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19.03 17.818a3 3 0 0 0 1.97 -2.818v-8a3 3 0 0 0 -3 -3h-12a3 3 0 0 0 -3 3v8c0 1.317 .85 2.436 2.03 2.84" /><path d="M10 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M8 21a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2" /></svg>',

    "customer"       => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                </svg>',

    "custom-ads"     => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-badge-ad" viewBox="0 0 16 16">
  <path d="m3.7 11 .47-1.542h2.004L6.644 11h1.261L5.901 5.001H4.513L2.5 11zm1.503-4.852.734 2.426H4.416l.734-2.426zm4.759.128c-1.059 0-1.753.765-1.753 2.043v.695c0 1.279.685 2.043 1.74 2.043.677 0 1.222-.33 1.367-.804h.057V11h1.138V4.685h-1.16v2.36h-.053c-.18-.475-.68-.77-1.336-.77zm.387.923c.58 0 1.002.44 1.002 1.138v.602c0 .76-.396 1.2-.984 1.2-.598 0-.972-.449-.972-1.248v-.453c0-.795.37-1.24.954-1.24z"/>
  <path d="M14 3a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1zM2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/>
</svg>',

    "settings"       => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-settings">
                  <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                  <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                  <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                </svg>',

    "news languages" => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-language"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 5h7" /><path d="M9 3v2c0 4.418 -2.239 8 -5 8" /><path d="M5 9c0 2.144 2.952 3.908 6.7 4" /><path d="M12 20l4 -9l4 9" /><path d="M19.1 18h-6.2" /></svg>',

    "channels"       => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-brand-youtube">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M2 8a4 4 0 0 1 4 -4h12a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-12a4 4 0 0 1 -4 -4v-8z" />
                <path d="M10 9l5 3l-5 3z" />
                </svg>',

    "rss-feed"       => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-brand-facebook">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3" />
                </svg>',

    "post"           => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-article">
               <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
               <path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
               <path d="M7 8h10" />
               <path d="M7 12h10" />
               <path d="M7 16h10" />
               </svg>',

    "videos"         => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-video"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 10l4.553 -2.276a1 1 0 0 1 1.447 .894v6.764a1 1 0 0 1 -1.447 .894l-4.553 -2.276v-4z" /><path d="M3 6m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" /></svg>',

    "comment"        => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-message-plus">
              <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
              <path d="M8 9h8" />
              <path d="M8 13h6" />
              <path d="M12.01 18.594l-4.01 2.406v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v5.5" />
              <path d="M16 19h6" />
              <path d="M19 16v6" />
              </svg>',

    "comment-spam"   => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-message-report">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                <path d="M12 8v3" />
                <path d="M12 14v.01" />
                </svg>',

    "notification"   => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-bell">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                </svg>',

    'topic'          => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-circle-letter-t">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                <path d="M10 8h4" />
                <path d="M12 8v8" />
                </svg>',

    'contact-us'     => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-headphones">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 13m0 2a2 2 0 0 1 2 -2h1a2 2 0 0 1 2 2v3a2 2 0 0 1 -2 2h-1a2 2 0 0 1 -2 -2z" />
                <path d="M15 13m0 2a2 2 0 0 1 2 -2h1a2 2 0 0 1 2 2v3a2 2 0 0 1 -2 2h-1a2 2 0 0 1 -2 -2z" />
                <path d="M4 15v-3a8 8 0 0 1 16 0v3" />
                </svg>',

    'subscriber'     => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                <path d="M3 7l9 6l9 -6" />
                </svg>',

    'web_story'      => '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M15 6H9C8.44772 6 8 6.44772 8 7V17C8 17.5523 8.44772 18 9 18H15C15.5523 18 16 17.5523 16 17V7C16 6.44772 15.5523 6 15 6ZM9 4C7.34315 4 6 5.34315 6 7V17C6 18.6569 7.34315 20 9 20H15C16.6569 20 18 18.6569 18 17V7C18 5.34315 16.6569 4 15 4H9Z" fill="#929090"></path> <path d="M2 6C2 5.44772 2.44772 5 3 5C3.55228 5 4 5.44772 4 6V18C4 18.5523 3.55228 19 3 19C2.44772 19 2 18.5523 2 18V6Z" fill="#929090"></path> <path d="M20 6C20 5.44772 20.4477 5 21 5C21.5523 5 22 5.44772 22 6V18C22 18.5523 21.5523 19 21 19C20.4477 19 20 18.5523 20 18V6Z" fill="#929090"></path> </g></svg>',

    'membership'     => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-brand-my-oppo"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18.316 5h-12.632l-3.418 4.019a1.089 1.089 0 0 0 .019 1.447l9.714 10.534l9.715 -10.49a1.09 1.09 0 0 0 .024 -1.444l-3.422 -4.066z" /><path d="M9 11l3 3l3 -3" /></svg>',

    'subscription'   => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-paywall"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 21h-6a2 2 0 0 1 -2 -2v-6a2 2 0 0 1 2 -2h10" /><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" /><path d="M8 11v-4a4 4 0 1 1 8 0v4" /><path d="M21 15h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" /><path d="M19 21v1" /><path d="M19 14v1" /></svg>',

    'transaction'    => '<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-arrows-transfer-up-down"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 21v-6" /><path d="M20 6l-3 -3l-3 3" /><path d="M10 18l-3 3l-3 -3" /><path d="M7 3v2" /><path d="M7 9v2" /><path d="M17 3v6" /><path d="M17 21v-2" /><path d="M17 15v-2" /></svg>',

    'e-paper'        => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-newspaper" viewBox="0 0 16 16">
  <path d="M0 2.5A1.5 1.5 0 0 1 1.5 1h11A1.5 1.5 0 0 1 14 2.5v10.528c0 .3-.05.654-.238.972h.738a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 1 1 0v9a1.5 1.5 0 0 1-1.5 1.5H1.497A1.497 1.497 0 0 1 0 13.5zM12 14c.37 0 .654-.211.853-.441.092-.106.147-.279.147-.531V2.5a.5.5 0 0 0-.5-.5h-11a.5.5 0 0 0-.5.5v11c0 .278.223.5.497.5z"/>
  <path d="M2 3h10v2H2zm0 3h4v3H2zm0 4h4v1H2zm0 2h4v1H2zm5-6h2v1H7zm3 0h2v1h-2zM7 8h2v1H7zm3 0h2v1h-2zm-3 2h2v1H7zm3 0h2v1h-2zm-3 2h2v1H7zm3 0h2v1h-2z"/>
</svg>',

    'videos'         => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-play" viewBox="0 0 16 16">
  <path d="M6 10.117V5.883a.5.5 0 0 1 .757-.429l3.528 2.117a.5.5 0 0 1 0 .858l-3.528 2.117a.5.5 0 0 1-.757-.43z"/>
  <path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1"/>
</svg>',
];

function mergeChildAbilities($navs, $svgs)
{
    foreach ($navs as &$nav) {
        if (isset($nav['svg']) && array_key_exists($nav['svg'], $svgs)) {
            $nav['svg'] = $svgs[$nav['svg']];
        } else {
            $nav['svg'] = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-home">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
              <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
              <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
            </svg>';
        }

        if (! isset($nav["ability"]) && ! isset($nav["children"])) {
            $nav["ability"] = ["*"];
        }

        if (isset($nav['children'])) {
            $parentAbility = $nav['ability'] ?? [];

            foreach ($nav['children'] as $child) {
                $childAbility  = $child['ability'] ?? ["*"];
                $parentAbility = array_merge($parentAbility, $childAbility);
            }

            $nav['ability']  = array_unique($parentAbility);
            $nav['children'] = mergeChildAbilities($nav['children'], $svgs);
        }
    }
    return $navs;
}
return mergeChildAbilities($nav, $svgs);
