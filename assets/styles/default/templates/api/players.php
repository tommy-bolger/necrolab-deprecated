<br />
<br />
<a name="endpoints">
    <a name="player_endpoints"><span class="menu_smaller">Player Endpoints</span></a>
</a>
<br />
<br />
<p>
    <span class="bold">GET</span> <a name="players"><code>/players</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all general info for all players.
            </p>
            <br />
            <span class="bold">Required Parameters</span>
            <p>
                None
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
                <code>https://api.necrolab.com/players</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null,
        "site": ""
    },
    "record_count": 374223
    "data": [
        {
            "steamid": "76561198029239987",
            "personaname": "``",
            "linked": {
                "steam": {
                    "personaname": "``",
                    "profile_url": "http://steamcommunity.com/profiles/76561198029239987/"
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
        ...
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_pbs"><code>/players/pbs</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all PBs for all players.
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
                <code>https://api.necrolab.com/players/pbs?release=original_release</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null,
        "release": "original_release"
    },
    "record_count": 769194,
    "data": [
        {
            "player": {
                "steamid": "76561198165350197",
                "personaname": "Roland",
                "linked": {
                    "steam": {
                        "personaname": "Roland",
                        "profile_url": "http://steamcommunity.com/profiles/76561198165350197/"
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
            "leaderboards": {
                "1143488": {
                    "leaderboard": {
                        "lbid": 1143488,
                        "name": "1/2/2016_PROD",
                        "display_name": null,
                        "entries_url": "http://steamcommunity.com/stats/247080/leaderboards/1143488/?xml=1",
                        "character": "cadence",
                        "character_number": 1,
                        "is_daily": 1,
                        "daily_date": "2016-02-01",
                        "is_score_run": 1,
                        "is_speedrun": 0,
                        "is_deathless": 0,
                        "is_seeded": 0,
                        "is_co_op": 0,
                        "is_custom": 0,
                        "is_power_ranking": 0,
                        "is_daily_ranking": 1
                    },
                    "entries": [
                        {
                            "date": "2016-02-01",
                            "rank": 3,
                            "details": "0400000006000000",
                            "zone": null,
                            "level": null,
                            "win": 0,
                            "score": 15142,
                            "replay": {
                                "ugcid": "357276631073900215",
                                "version": null,
                                "seed": null,
                                "run_result": null,
                                "file_url": null
                            }
                        }
                    ]
                }
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
    <span class="bold">GET</span> <a name="players_player"><code>/players/player</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all general information for a specified player.
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
                                <code>steamid</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#steamid"><code>steamid</code></a> section under common parameters.
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
                <code>https://api.necrolab.com/players/player?steamid=76561198004612980</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "steamid": 76561198004612980,
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null
    },
    "record_count": 1
    "data": {
        "steamid": "76561198004612980",
        "personaname": "wilarseny",
        "linked": {
            "steam": {
                "personaname": "wilarseny",
                "profile_url": "http://steamcommunity.com/id/wilarseny/"
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
    }
}</code></pre>
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_pbs"><code>/players/player/pbs</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all PBs for a specified player.
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
                                <code>steamid</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#steamid"><code>steamid</code></a> section under common parameters.
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
                <code>https://api.necrolab.com/players/player/pbs?release=original_release&steamid=76561197987716503</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "steamid": 76561197987716500,
        "release": "original_release",
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null
    },
    "record_count": 53,
    "data": [
        {
            "leaderboard": {
                "lbid": 741312,
                "name": "hardcore all chars_prod",
                "display_name": "all characters score",
                "entries_url": "http://steamcommunity.com/stats/247080/leaderboards/741312/?xml=1",
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
                "is_daily_ranking": 0
            },
            "entries": [
                {
                    "date": "2016-02-01",
                    "rank": 349,
                    "details": "0900000004000000",
                    "zone": null,
                    "level": null,
                    "win": 0,
                    "score": 4451,
                    "replay": {
                        "ugcid": "359526421598130565",
                        "version": null,
                        "seed": null,
                        "run_result": null,
                        "file_url": null
                    }
                },
                {
                    "date": "2017-03-07",
                    "rank": 149,
                    "details": "0400000006000000",
                    "zone": 4,
                    "level": 6,
                    "win": 1,
                    "score": 16345,
                    "replay": {
                        "ugcid": "270590474407201738",
                        "version": null,
                        "seed": null,
                        "run_result": null,
                        "file_url": null
                    }
                }
            ]
        },
        ...
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>