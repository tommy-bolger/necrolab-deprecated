<br />
<br />
<a name="endpoints">
    <a name="leaderboard_endpoints"><span class="menu_smaller">Leaderboard Endpoints</span></a>
</a>
<br />
<br />
<p>
    <span class="bold">GET</span><a name="leaderboards_xml"><code>/leaderboards/xml</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves the urls for all leaderboard XML downloaded.
            </p>
            <br />
            <span class="bold">Required Parameters</span>
            <p>
                None
            </p>
            <br />
            <span class="bold">Optional Parameters</span>
            <p>
                None
            </p>
            <br />
            <span class="bold">Example Request</span>
            <p>
                <code>https://api.necrolab.com/leaderboards/xml</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": [],
    "record_count": 81,
    "data": [
        "https://necrolab.s3.amazonaws.com/leaderboard_xml/2017-01-01.zip",
        "https://necrolab.s3.amazonaws.com/leaderboard_xml/2017-01-02.zip",
        "https://necrolab.s3.amazonaws.com/leaderboard_xml/2017-01-03.zip",
        "https://necrolab.s3.amazonaws.com/leaderboard_xml/2017-01-04.zip",
        "https://necrolab.s3.amazonaws.com/leaderboard_xml/2017-01-05.zip",
        "https://necrolab.s3.amazonaws.com/leaderboard_xml/2017-01-06.zip",
        ...
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="leaderboards"><code>/leaderboards</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all leaderboards for the specified release and mode.
            </p>
            <br />
            <span class="bold">Required Parameters</span>
            <p>
                <table class="documentation">
                    <thead>
                        <tr>
                            <th>
                                Parameter
                            </th>
                            <th>
                                Description
                            </th>
                            <th>
                                Valid Values
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <code>release</code>
                            </td>
                            <td>
                                Please see the <a href="#release"><code>release</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="last">
                                <code>mode</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#mode"><code>mode</code></a> section under common parameters.
                            </td>
                            <td class="last"></td>
                        </tr>
                    </tbody>
                </table>
            </p>
            <br />
            <span class="bold">Optional Parameters</span>
            <p>
                <table class="documentation">
                    <thead>
                        <tr>
                            <th>
                                Parameter
                            </th>
                            <th>
                                Description
                            </th>
                            <th>
                                Valid Values
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <code>start</code>
                            </td>
                            <td>
                                Please see the <a href="#start"><code>start</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <code>limit</code>
                            </td>
                            <td>
                                Please see the <a href="#limit"><code>limit</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <code>sort_by</code>
                            </td>
                            <td>
                                Please see the <a href="#sort_by"><code>sort_by</code></a> section under common parameters.
                            </td>
                            <td class="last">
                                <ul class="plain_list">
                                    <li>
                                        <code>name</code>
                                    </li>
                                    <li>
                                        <code>display_name</code>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td class="last">
                                <code>sort_direction</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#sort_direction"><code>sort_direction</code></a> section under common parameters.
                            </td>
                            <td class="last"></td>
                        </tr>
                    </tbody>
                </table>
            </p>
            <br />
            <span class="bold">Example Request</span>
            <p>
                <code>https://api.necrolab.com/leaderboards?release=amplified_dlc_early_access&mode=normal</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "release": "amplified_dlc_early_access",
        "mode": "normal",
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null
    },
    "record_count": 73,
    "data": [
        {
            "lbid": 1694062,
            "name": "DLC HARDCORE_PROD",
            "display_name": "Cadence Score (Amplified)",
            "entries_url": "http://steamcommunity.com/stats/247080/leaderboards/1694062/?xml=1",
            "character": "cadence",
            "character_number": 1,
            "is_daily": 0,
            "daily_date": null,
            "is_score_run": 1,
            "is_speedrun": 0,
            "is_deathless": 0,
            "is_seeded": 0,
            "is_co_op": 0,
            "is_custom": 0,
            "is_power_ranking": 1,
            "is_daily_ranking": 0,
            "mode": {
                "name": "normal",
                "display_name": "Normal"
            }
        },
        ...
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="leaderboards_score"><code>/leaderboards/score</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#leaderboards"><code>/leaderboards</code></a> that only retrieves score leaderboards.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="leaderboards_speed"><code>/leaderboards/speed</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#leaderboards"><code>/leaderboards</code></a> that only retrieves speed leaderboards.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="leaderboards_deathless"><code>/leaderboards/deathless</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#leaderboards"><code>/leaderboards</code></a> that only retrieves deathless leaderboards.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="leaderboards_daily"><code>/leaderboards/daily</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#leaderboards"><code>/leaderboards</code></a> that only retrieves daily leaderboards.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="leaderboards_leaderboard"><code>/leaderboards/leaderboard</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves the properties of a single specified leaderboard.
            </p>
            <br />
            <span class="bold">Required Parameters</span>
            <p>
                <table class="documentation">
                    <thead>
                        <tr>
                            <th>
                                Parameter
                            </th>
                            <th>
                                Description
                            </th>
                            <th>
                                Valid Values
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="last">
                                <code>lbid</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#lbid"><code>lbid</code></a> section under common parameters.
                            </td>
                            <td class="last"></td>
                        </tr>
                    </tbody>
                </table>
            </p>
            <br />
            <span class="bold">Optional Parameters</span>
            <p>
                None
            </p>
            <br />
            <span class="bold">Example Request</span>
            <p>
                <code>https://api.necrolab.com/leaderboards/leaderboard?lbid=1694062</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "lbid": "1694062"
    },
    "record_count": 1,
    "data": [
        {
            "lbid": "1694062",
            "leaderboard_name": "DLC HARDCORE_PROD",
            "leaderboard_display_name": "Cadence Score (Amplified)",
            "url": "http://steamcommunity.com/stats/247080/leaderboards/1694062/?xml=1",
            "is_speedrun": 0,
            "is_custom": 0,
            "is_co_op": 0,
            "is_seeded": 0,
            "is_daily": 0,
            "daily_date": null,
            "is_score_run": 1,
            "is_all_character": 0,
            "is_deathless": 0,
            "is_story_mode": 0,
            "is_power_ranking": 1,
            "is_daily_ranking": 0,
            "release_id": 4,
            "character_id": 1,
            "character_name": "cadence",
            "character_number": 1,
            "mode": {
                "name": "normal",
                "display_name": "Normal"
            }
        }
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="leaderboards_snapshots"><code>/leaderboards/snapshots</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all of the dates that a specified leaderboard has entries for.
            </p>
            <br />
            <span class="bold">Required Parameters</span>
            <p>
                <table class="documentation">
                    <thead>
                        <tr>
                            <th>
                                Parameter
                            </th>
                            <th>
                                Description
                            </th>
                            <th>
                                Valid Values
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="last">
                                <code>lbid</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#lbid"><code>lbid</code></a> section under common parameters.
                            </td>
                            <td class="last"></td>
                        </tr>
                    </tbody>
                </table>
            </p>
            <br />
            <span class="bold">Optional Parameters</span>
            <p>
                None
            </p>
            <br />
            <span class="bold">Example Request</span>
            <p>
                <code>https://api.necrolab.com/leaderboards/snapshots?lbid=1701929</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
            "lbid": 1701929,
            "record_count": 48
    },
    "data": [
            "2017-01-27",
            "2017-01-28",
            "2017-01-30",
            "2017-01-31",
            ...
        }
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="leaderboards_entries"><code>/leaderboards/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves entries for a leaderboard on a specified date and criteria.
            </p>
            <br />
            <span class="bold">Required Parameters</span>
            <p>
                <table class="documentation">
                    <thead>
                        <tr>
                            <th>
                                Parameter
                            </th>
                            <th>
                                Description
                            </th>
                            <th>
                                Valid Values
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <code>lbid</code>
                            </td>
                            <td>
                                Please see the <a href="#lbid"><code>lbid</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="last">
                                <code>date</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#date"><code>date</code></a> section under common parameters.
                            </td>
                            <td class="last"></td>
                        </tr>
                    </tbody>
                </table>
            </p>
            <br />
            <span class="bold">Optional Parameters</span>
            <p>
                <table class="documentation">
                    <thead>
                        <tr>
                            <th>
                                Parameter
                            </th>
                            <th>
                                Description
                            </th>
                            <th>
                                Valid Values
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <code>site</code>
                            </td>
                            <td>
                                Please see the <a href="#site"><code>site</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <code>start</code>
                            </td>
                            <td>
                                Please see the <a href="#start"><code>start</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <code>limit</code>
                            </td>
                            <td>
                                Please see the <a href="#limit"><code>limit</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <code>sort_by</code>
                            </td>
                            <td>
                                Please see the <a href="#sort_by"><code>sort_by</code></a> section under common parameters.
                            </td>
                            <td class="last">
                                <ul class="plain_list">
                                    <!-- <li>
                                        <code>lbid</code>
                                    </li>
                                    -->
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <code>sort_direction</code>
                            </td>
                            <td>
                                Please see the <a href="#sort_direction"><code>sort_direction</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="last">
                                <code>search</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#search"><code>search</code></a> section under common parameters.
                            </td>
                            <td class="last"></td>
                        </tr>
                    </tbody>
                </table>
            </p>
            <br />
            <span class="bold">Example Request</span>
            <p>
                <code>https://api.necrolab.com/leaderboards/entries?lbid=1694062&date=2017-03-08</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "lbid": 1694062,
        "site": "",
        "date": "2017-03-08",
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null
    },
    "record_count": 6265,
    "data": [
        {
            "player": {
                "steamid": "76561198171801786",
                "personaname": "asinoura48",
                "linked": {
                    "steam": {
                        "personaname": "asinoura48",
                        "profile_url": "http://steamcommunity.com/profiles/76561198171801786/"
                    },
                    "twitch": null,
                    "discord": {
                        "username": null,
                        "discriminator": null
                    },
                    "reddit": null,
                    "youtube": null,
                    "twitter": {
                        "nickname": null,
                        "name": null
                    },
                    "beampro": null
                }
            },
            "rank": 1,
            "details": "0500000006000000",
            "zone": 5,
            "level": 6,
            "win": 1,
            "score": 31233,
            "replay": {
                "ugcid": 91599028255190178,
                "version": null,
                "seed": 1640128,
                "run_result": null,
                "file_url": https://necrolab.s3.amazonaws.com/replays/91599028255190178.zip
            }
        },
        ...
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>


<br />
<p>
    <span class="bold">GET</span> <a name="leaderboards_daily_entries"><code>/leaderboards/daily/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves entries for a daily leaderboard on a specified release, date, and other criteria.
            </p>
            <br />
            <span class="bold">Required Parameters</span>
            <p>
                <table class="documentation">
                    <thead>
                        <tr>
                            <th>
                                Parameter
                            </th>
                            <th>
                                Description
                            </th>
                            <th>
                                Valid Values
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <code>release</code>
                            </td>
                            <td>
                                Please see the <a href="#release"><code>release</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <code>mode</code>
                            </td>
                            <td>
                                Please see the <a href="#mode"><code>mode</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="last">
                                <code>date</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#date"><code>date</code></a> section under common parameters.
                            </td>
                            <td class="last"></td>
                        </tr>
                    </tbody>
                </table>
            </p>
            <br />
            <span class="bold">Optional Parameters</span>
            <p>
                <table class="documentation">
                    <thead>
                        <tr>
                            <th>
                                Parameter
                            </th>
                            <th>
                                Description
                            </th>
                            <th>
                                Valid Values
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <code>site</code>
                            </td>
                            <td>
                                Please see the <a href="#site"><code>site</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <code>start</code>
                            </td>
                            <td>
                                Please see the <a href="#start"><code>start</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <code>limit</code>
                            </td>
                            <td>
                                Please see the <a href="#limit"><code>limit</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <code>sort_by</code>
                            </td>
                            <td>
                                Please see the <a href="#sort_by"><code>sort_by</code></a> section under common parameters.
                            </td>
                            <td class="last">
                                <ul class="plain_list">
                                    <!-- <li>
                                        <code>lbid</code>
                                    </li>
                                    -->
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <code>sort_direction</code>
                            </td>
                            <td>
                                Please see the <a href="#sort_direction"><code>sort_direction</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="last">
                                <code>search</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#search"><code>search</code></a> section under common parameters.
                            </td>
                            <td class="last"></td>
                        </tr>
                    </tbody>
                </table>
            </p>
            <br />
            <span class="bold">Example Request</span>
            <p>
                <code>https://api.necrolab.com/leaderboards/daily/entries?release=original_release&mode=normal&date=2016-02-10</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "release": "original_release",
        "mode": "normal",
        "site": "",
        "date": "2016-02-10",
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null,
        "search": ""
    },
    "record_count": 6265,
    "data": [
        {
            "player": {
                "steamid": "76561198051520646",
                "personaname": "Merzon",
                    "linked": {
                        "steam": {
                            "personaname": "Merzon",
                            "profile_url": "http://steamcommunity.com/profiles/76561198051520646/"
                        },
                        "twitch": null,
                        "discord": {
                            "username": null,
                            "discriminator": null
                        },
                        "reddit": null,
                        "youtube": null,
                        "twitter": {
                            "nickname": null,
                            "name": null
                        },
                        "beampro": null
                    }
                },
            "rank": 1,
            "details": "0400000006000000",
            "zone": 4,
            "level": 6,
            "win": 1,
            "score": 15903,
            "replay": {
                "ugcid": 312241798833947815,
                "version": null,
                "seed": 42496,
                "run_result": null,
                "file_url": "https://necrolab.s3.amazonaws.com/replays/312241798833947815.zip"
            }
        },
        ...
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="leaderboards_replays"><code>/leaderboards/replays</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all leaderboard entry replays for the specified release.
            </p>
            <br />
            <span class="bold">Required Parameters</span>
            <p>
                <table class="documentation">
                    <thead>
                        <tr>
                            <th>
                                Parameter
                            </th>
                            <th>
                                Description
                            </th>
                            <th>
                                Valid Values
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="last">
                                <code>release</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#release"><code>release</code></a> section under common parameters.
                            </td>
                            <td class="last"></td>
                        </tr>
                    </tbody>
                </table>
            </p>
            <br />
            <span class="bold">Optional Parameters</span>
            <p>
                <table class="documentation">
                    <thead>
                        <tr>
                            <th>
                                Parameter
                            </th>
                            <th>
                                Description
                            </th>
                            <th>
                                Valid Values
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <code>start</code>
                            </td>
                            <td>
                                Please see the <a href="#start"><code>start</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <code>limit</code>
                            </td>
                            <td>
                                Please see the <a href="#limit"><code>limit</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <code>sort_by</code>
                            </td>
                            <td>
                                Please see the <a href="#sort_by"><code>sort_by</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="last">
                                <code>sort_direction</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#sort_direction"><code>sort_direction</code></a> section under common parameters.
                            </td>
                            <td class="last"></td>
                        </tr>
                    </tbody>
                </table>
            </p>
            <br />
            <span class="bold">Example Request</span>
            <p>
                <code>https://api.necrolab.com/leaderboards/replays?release=amplified_dlc_early_access</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "release": "amplified_dlc_early_access",
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null
    },
    "record_count": 55306,
    "data": [
        {
            "steamid": 76561197979118640,
            "leaderboard": {
                "lbid": 1700534,
                "name": "DLC HARDCORE All Chars_PROD",
                "display_name": "All Characters Score (Amplified)",
                "entries_url": "http://steamcommunity.com/stats/247080/leaderboards/1700534/?xml=1",
                "character": "all",
                "character_number": 14,
                "is_daily": 0,
                "daily_date": null,
                "is_score_run": 1,
                "is_speedrun": 0,
                "is_deathless": 0,
                "is_seeded": 0,
                "is_co_op": 0,
                "is_custom": 0,
                "is_power_ranking": 1,
                "is_daily_ranking": 0,
                "mode": {
                    "name": "normal",
                    "display_name": "Normal"
                }
            },
            "pb": {
                "date": "2017-03-20",
                "rank": 649,
                "details": "0000000001000000",
                "zone": 0,
                "level": 1,
                "win": 0,
                "score": 0,
                "replay": {
                    "ugcid": "80342832064696717",
                    "version": null,
                    "seed": null,
                    "run_result": null,
                    "file_url": null
                }
            },
            "replay": {
                "ugcid": "80342832064696717",
                "version": null,
                "seed": null,
                "run_result": null,
                "file_url": null
            }
        },
        ...
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="leaderboards_replays_replay"><code>/leaderboards/replays/replay</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves a single replay by its unique id.
            </p>
            <br />
            <span class="bold">Required Parameters</span>
            <p>
                <table class="documentation">
                    <thead>
                        <tr>
                            <th>
                                Parameter
                            </th>
                            <th>
                                Description
                            </th>
                            <th>
                                Valid Values
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="last">
                                <code>ugcid</code>
                            </td>
                            <td class="last">
                                The unique id of the replay. Must be a valid 64-bit integer greater than 0.
                            </td>
                            <td class="last"></td>
                        </tr>
                    </tbody>
                </table>
            </p>
            <br />
            <span class="bold">Optional Parameters</span>
            <p>
                None
            </p>
            <br />
            <span class="bold">Example Request</span>
            <p>
                <code>https://api.necrolab.com/leaderboards/replays/replay?ugcid=82590571760664522</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "ugcid": "82590571760664522"
    },
    "record_count": 1,
    "data": {
        "steamid": 76561197979118640,
        "leaderboard": {
            "lbid": 1700534,
            "name": "DLC HARDCORE All Chars_PROD",
            "display_name": "All Characters Score (Amplified)",
            "entries_url": "http://steamcommunity.com/stats/247080/leaderboards/1700534/?xml=1",
            "character": "all",
            "character_number": 14,
            "is_daily": 0,
            "daily_date": null,
            "is_score_run": 1,
            "is_speedrun": 0,
            "is_deathless": 0,
            "is_seeded": 0,
            "is_co_op": 0,
            "is_custom": 0,
            "is_power_ranking": 1,
            "is_daily_ranking": 0,
            "mode": {
                "name": "normal",
                "display_name": "Normal"
            }
        },
        "pb": {
            "date": "2017-03-07",
            "rank": 549,
            "details": "0000000001000000",
            "zone": 0,
            "level": 1,
            "win": 0,
            "score": 0,
            "replay": {
                "ugcid": "82590571760664522",
                "version": null,
                "seed": null,
                "run_result": null,
                "file_url": null
            }
        },
        "replay": {
            "ugcid": "82590571760664522",
            "version": null,
            "seed": null,
            "run_result": null,
            "file_url": null
        }
    }
}</code></pre>
            </p>
        </li>
    </ul>
</p>