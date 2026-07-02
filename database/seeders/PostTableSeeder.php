<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('posts')->insert([
            [
                'user_id' => 0,
                'channel_id' => 1,
                'topic_id' => 1,
                'title' => 'Bashir showed the world what he can do in England win',
                'slug' => 'bashir-showed-the-world-what-he-can-do-in-england-win',
                'image' => 'https:ichef.bbci.co.uknews240cpsprodpb4334live51301e40-4792-11ef-b74c-bb483a802c97.jpg',
                'description' => 'Captain Ben Stokes says Shoaib Bashir has shown the world what he can do after bowling England to victory in the second Test against West Indies.',
                'status' => 'active',
                'publish_date' => 'Sun, 21 Jul 2024 19:38:41 GMT'
            ],
            [
                'user_id' => 0,
                'channel_id' => 1,
                'topic_id' => 1,
                'title' => 'England dismantle Windies to win Test and series',
                'slug' => 'england-dismantle-windies-to-win-test-and-series',
                'image' => 'https:ichef.bbci.co.uknews240cpsprodpbf423live24f3f3b0-4783-11ef-b74c-bb483a802c97.jpg',
                'description' => 'England surge to a series victory by blowing away West Indies in a single session on the fourth day of the second Test at Trent Bridge.',
                'status' => 'active',
                'publish_date' => 'Sun, 21 Jul 2024 17:43:08 GMT'
            ],
            [
                'user_id' => 0,
                'channel_id' => 1,
                'topic_id' => 1,
                'title' => 'Boycott back in hospital with pneumonia',
                'slug' => 'boycott-back-in-hospital-with-pneumonia',
                'image' => 'https:ichef.bbci.co.uknews240cpsprodpb2c7clive61f72fb0-4775-11ef-b68c-050ea61c75fc.jpg',
                'description' => 'Geoffrey Boycott, 83, is back in hospital two days after being discharged following cancer surgery.',
                'status' => 'active',
                'publish_date' => 'Sun, 21 Jul 2024 15:50:28 GMT'
            ],
            [
                'user_id' => 0,
                'channel_id' => 1,
                'topic_id' => 1,
                'title' => 'England win by 241 runs as West Indies bowled out in one session',
                'slug' => 'england-win-by-241-runs-as-west-indies-bowled-out-in-one-session',
                'image' => 'https:ichef.bbci.co.uknews240cpsprodpb0e17live040df780-4798-11ef-96a8-e710c6bfc866.jpg',
                'description' => 'Watch highlights as England bowl West Indies out for 143 in the final session on day four of the second Test at Trent Bridge to win by 241 runs and take the series with a game to spare.',
                'status' => 'active',
                'publish_date' => 'Sun, 21 Jul 2024 19:33:32 GMT'
            ],
            [
                'user_id' => 0,
                'channel_id' => 1,
                'topic_id' => 1,
                'title' => 'Watch Bashir\'s five-wicket haul against West Indies',
                'slug' => 'watch-bashirs-five-wicket-haul-against-west-indies',
                'image' => 'https:ichef.bbci.co.uknews240cpsprodpb623clive31764800-4791-11ef-96a8-e710c6bfc866.jpg',
                'description' => 'Watch England\'s Shoaib Bashir five-wicket haul as he gets figures of 5-41, against the West Indies in the second Test at Trent Bridge.',
                'status' => 'active',
                'publish_date' => 'Sun, 21 Jul 2024 18:50:09 GMT'
            ],
            [
                'user_id' => 0,
                'channel_id' => 1,
                'topic_id' => 1,
                'title' => 'Wonderful talent - Vaughan praises Bashir after his five-wicket haul',
                'slug' => 'wonderful-talent-vaughan-praises-bashir-after-his-five-wicket-haul',
                'image' => 'https:ichef.bbci.co.uknews240cpsprodpb16d2lived40e4d90-478e-11ef-9e1c-3b4a473456a6.jpg',
                'description' => 'Watch as former England captain Michael Vaughan describes Shoaib Bashir as a "wonderful talent" as he picks up figures of 5-41 to help England to victory in the second test against the West Indies at Trent Bridge.',
                'status' => 'active',
                'publish_date' => 'Sun, 21 Jul 2024 18:32:52 GMT'
            ],
            [
                'user_id' => 0,
                'channel_id' => 1,
                'topic_id' => 1,
                'title' => 'Stokes praises outstanding bowlers after Windies thrashing',
                'slug' => 'stokes-praises-outstanding-bowlers-after-windies-thrashing',
                'image' => 'https:ichef.bbci.co.uknews240cpsprodpb6560live830ead40-478e-11ef-96a8-e710c6bfc866.jpg',
                'description' => 'England captain Ben Stokes praise\'s his "outstanding" bowlers as Chris Woakes, Mark Wood, Gus Atkinson and Shoaib Bashir bowl West Indies out in a session to claim victory in the second test at Trent Bridge.',
                'status' => 'active',
                'publish_date' => 'Sun, 21 Jul 2024 18:30:46 GMT'
            ],
            [
                'user_id' => 0,
                'channel_id' => 1,
                'topic_id' => 1,
                'title' => 'White excited for historic first Test match in NI',
                'slug' => 'white-excited-for-historic-first-test-match-in-ni',
                'image' => 'https:ichef.bbci.co.uknews240cpsprodpb352clived65b1d40-442c-11ef-b6ff-e7f43eb71781.jpg',
                'description' => 'Andrew White is relishing this week\'s encounter between Ireland and Zimbabwe as Test cricket comes to Belfast for the first time.',
                'status' => 'active',
                'publish_date' => 'Mon, 22 Jul 2024 06:25:10 GMT'
            ],
            [
                'user_id' => 0,
                'channel_id' => 1,
                'topic_id' => 1,
                'title' => 'Catch-up: Today at the Test on BBC iPlayer',
                'slug' => 'catch-up-today-at-the-test-on-bbc-iplayer',
                'image' => 'https:ichef.bbci.co.ukimagesic240x135p0j9tj88.jpg',
                'description' => 'Day four highlights from the second Test between England and the West Indies.',
                'status' => 'active',
                'publish_date' => 'Sun, 21 Jul 2024 19:01:59 GMT'
            ],
            [
                'user_id' => 0,
                'channel_id' => 1,
                'topic_id' => 1,
                'title' => 'TMS podcast: Bashir bags five as England win emphatically',
                'slug' => 'tms-podcast-bashir-bags-five-as-england-win-emphatically',
                'image' => 'https:ichef.bbci.co.ukimagesic240x135p0jcsjtd.jpg',
                'description' => 'Jonathan Agnew presents reaction from the Trent Bridge pitch as England seal a series win',
                'status' => 'active',
                'publish_date' => 'Sun, 21 Jul 2024 19:37:00 GMT'
            ],
            [
                'user_id' => 0,
                'channel_id' => 1,
                'topic_id' => 1,
                'title' => 'How Edwards builds a squad to win The Hundred',
                'slug' => 'how-edwards-builds-a-squad-to-win-the-hundred',
                'image' => 'https://ichef.bbci.co.uk/news/240/cpsprodpb/717e/live/96486d50-45b9-11ef-92c5-db02d158fbbb.jpg',
                'description' => 'Southern Brave coach Charlotte Edwards tells BBC Sport about the challenges involved in building a squad capable of winning The Hundred.',
                'status' => 'active',
                'publish_date' => 'Sun, 21 Jul 2024 06:21:36 GMT'
            ],
            [
                'user_id' => 0,
                'channel_id' => 1,
                'topic_id' => 1,
                'title' => '\'Take the positives\' - Carlos Brathwaite on where West Indies can improve',
                'slug' => 'take-the-positives-carlos-brathwaite-on-where-west-indies-can-improve',
                'image' => 'https://ichef.bbci.co.uk/news/240/cpsprodpb/f789/live/6252a1e0-4790-11ef-96a8-e710c6bfc866.jpg',
                'description' => 'Watch as former West Indies T20 captain Carlos Brathwaite looks at how they can improve in the future following their series defeat to England.',
                'status' => 'active',
                'publish_date' => 'Sun, 21 Jul 2024 18:44:35 GMT'
            ],
            [
                'user_id' => 0,
                'channel_id' => 1,
                'topic_id' => 1,
                'title' => 'West Indies four down as Hodge goes for a duck',
                'slug' => 'west-indies-four-down-as-hodge-goes-for-a-duck',
                'image' => 'https://ichef.bbci.co.uk/news/240/cpsprodpb/00cb/live/009a3e40-477c-11ef-b74c-bb483a802c97.jpg',
                'description' => 'Watch as England\'s Shoaib Bashir removes Kavem Hodge for a duck as West Indies lose four wickets in four overs, on the fourth day of the second Test at Trent Bridge.',
                'status' => 'active',
                'publish_date' => 'Sun, 21 Jul 2024 16:15:00 GMT'
            ]
        ]);
        
    }
}