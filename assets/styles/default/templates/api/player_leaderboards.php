<br />
<br />
<a name="endpoints">
    <a name="player_leaderboard_endpoints"><span class="menu_smaller">Player Leaderboard Endpoints</span></a>
</a>
<br />
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_leaderboards"><code>/players/player/leaderboards</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all of a player's leaderboards for the specified release and mode.
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
                                <code>steamid</code>
                            </td>
                            <td>
                                Please see the <a href="#steamid"><code>steamid</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
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
                <code>https://api.necrolab.com/players/player/leaderboards/score?steamid=76561197987716503&release=original&mode=normal</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "steamid": 76561197987716503,
        "release": "original",
        "mode": "normal",
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null
    },
    "record_count": 23,
    "data": [
        {
            "lbid": 740390,
            "name": "HARDCORE Melody_PROD",
            "display_name": "Melody Score",
            "entries_url": "http://steamcommunity.com/stats/247080/leaderboards/740390/?xml=1",
            "character": "melody",
            "character_number": 8,
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
    <span class="bold">GET</span> <a name="players_player_leaderboards_score"><code>/players/player/leaderboards/score</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_leaderboards"><code>/players/player/leaderboards</code></a> that only retrieves a player's score leaderboards.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_leaderboards_speed"><code>/players/player/leaderboards/speed</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_leaderboards"><code>/players/player/leaderboards</code></a> that only retrieves a player's speed leaderboards.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_leaderboards_deathless"><code>/players/player/leaderboards/deathless</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_leaderboards"><code>/players/player/leaderboards</code></a> that only retrieves a player's deathless leaderboards.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_leaderboards_daily"><code>/players/player/leaderboards/daily</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_leaderboards"><code>/players/player/leaderboards</code></a> that only retrieves a player's daily leaderboards.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_leaderboards_snapshots"><code>/players/player/leaderboards/snapshots</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all of the dates that a specified player and leaderboard has entries for.
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
                                <code>steamid</code>
                            </td>
                            <td>
                                Please see the <a href="#steamid"><code>steamid</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
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
                <code>https://api.necrolab.com/players/player/leaderboards/snapshots?steamid=76561197987716503&lbid=1694062</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "steamid": 76561197987716500,
        "lbid": 1694062,
    },
    "record_count": 52,
    "data": [
            "2017-01-25",
            "2017-01-26",
            "2017-01-27",
            "2017-01-28",
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
    <span class="bold">GET</span> <a name="players_player_leaderboards_entries"><code>/players/player/leaderboards/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves a player's entries for a leaderboard on a specified date and criteria.
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
                                <code>steamid</code>
                            </td>
                            <td>
                                Please see the <a href="#steamid"><code>steamid</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <code>date</code>
                            </td>
                            <td>
                                Please see the <a href="#date"><code>date</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
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
                            <td>
                                <ul class="plain_list">
                                    <!-- <li>
                                        <code>lbid</code>
                                    </li>
                                    -->
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
                <code>https://api.necrolab.com/players/player/leaderboards/entries?steamid=76561198171801786&date=2017-03-07&release=amplified_dlc</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "steamid": 76561198171801800,
        "date": "2017-03-07",
        "release": "amplified_dlc",
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null
    },
    "record_count": 20,
    "data": [
        {
            "leaderboard": {
                "lbid": 1694068,
                "name": "DLC HARDCORE Aria_PROD",
                "display_name": "Aria Score (Amplified)",
                "entries_url": "http://steamcommunity.com/stats/247080/leaderboards/1694068/?xml=1",
                "character": "aria",
                "character_number": 3,
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
            "entry": {
                "rank": 152,
                "details": "0100000006000000",
                "zone": 1,
                "level": 6,
                "win": 0,
                "score": 3314,
                "replay": {
                    "ugcid": 91597306689706578,
                    "version": null,
                    "seed": 2939030907,
                    "run_result": null,
                    "file_url": "https://necrolab.s3.amazonaws.com/replays/91597306689706578.zip"
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
    <span class="bold">GET</span> <a name="players_player_leaderboards_score_entries"><code>/players/player/leaderboards/score/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_leaderboards_entries"><code>/players/player/leaderboards/entries</code></a> that retrieves a player's entries for score leaderboards only.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_leaderboards_speed_entries"><code>/players/player/leaderboards/speed/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_leaderboards_entries"><code>/players/player/leaderboards/entries</code></a> that retrieves a player's entries for speed leaderboards only.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_leaderboards_deathless_entries"><code>/players/player/leaderboards/deathless/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_leaderboards_entries"><code>/players/player/leaderboards/entries</code></a> that retrieves a player's entries for deathless leaderboards only.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_leaderboards_daily_entries"><code>/players/player/leaderboards/daily/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves a player's entries for a daily leaderboard on a specified release, date range, and other criteria.
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
                                <code>steamid</code>
                            </td>
                            <td>
                                Please see the <a href="#steamid"><code>steamid</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
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
                                <code>start_date</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#start_date"><code>start_date</code></a> section under common parameters.
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
                                <code>end_date</code>
                            </td>
                            <td>
                                Please see the <a href="#end_date"><code>end_date</code></a> section under common parameters.
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
                <code>https://api.necrolab.com/players/player/leaderboards/daily/entries?steamid=76561198051520646&release=original&start_date=2016-02-01</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "steamid": "76561198051520646",
        "start_date": "2016-02-01",
        "end_date": "2017-03-18",
        "release": "original",
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null
    },
    "record_count": 2,
    "data": [
        {
            "leaderboard": {
                "lbid": 1114962,
                "name": "2/2/2016_PROD",
                "display_name": null,
                "entries_url": "http://steamcommunity.com/stats/247080/leaderboards/1114962/?xml=1",
                "character": "cadence",
                "character_number": 1,
                "is_daily": 1,
                "daily_date": "2016-02-02",
                "is_score_run": 1,
                "is_speedrun": 0,
                "is_deathless": 0,
                "is_seeded": 0,
                "is_co_op": 0,
                "is_custom": 0,
                "is_power_ranking": 0,
                "is_daily_ranking": 1,
                "mode": {
                    "name": "normal",
                    "display_name": "Normal"
                }
            },
            "entry": {
                "rank": 7,
                "details": "0400000002000000",
                "zone": null,
                "level": null,
                "win": 0,
                "score": 9460,
                "replay": {
                    "ugcid": null,
                    "version": null,
                    "seed": null,
                    "run_result": null,
                    "file_url": null
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
    <span class="bold">GET</span> <a name="players_player_leaderboards_replays"><code>/players/player/leaderboards/replays</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all of a player's leaderboard entry replays for the specified release.
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
                <code>https://api.necrolab.com/players/player/leaderboards/replays?release=amplified_dlc&steamid=76561197979118640</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "release": "amplified_dlc",
        "steamid": 76561197979118640,
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null
    },
    "record_count": 55306,
    "data": [
        {
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