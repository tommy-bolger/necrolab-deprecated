<br />
<br />
<a name="endpoints">
    <a name="pbs_endpoints"><span class="menu_smaller">PB Endpoints</span></a>
</a>
<br />
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
                                <code>character</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#character"><code>character</code></a> section under common parameters.
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
                    </tbody>
                </table>
            </p>
            <br />
            <span class="bold">Example Request</span>
            <p>
                <code>https://api.necrolab.com/players/pbs?release=original_release&mode=normal&character=cadence</code>
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
        "release": "original_release",
        "mode": "normal",
        "character": "cadence"
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
            "pb": {
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
    <span class="bold">GET</span> <a name="players_pbs_score"><code>/players/pbs/score</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_pbs"><code>/players/pbs</code></a> that only retrieves score PBs.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_pbs_speed"><code>/players/pbs/speed</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_pbs"><code>/players/pbs</code></a> that only retrieves speed PBs.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_pbs_deathless"><code>/players/pbs/deathless</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_pbs"><code>/players/pbs</code></a> that only retrieves deathless PBs.
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
                                <code>character</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#character"><code>character</code></a> section under common parameters.
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
                <code>https://api.necrolab.com/players/player/pbs?steamid=76561197987716503&release=original_release&mode=normal&character=all</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "steamid": 76561197987716500,
        "release": "original_release",
        "mode": "normal",
        "character": "all",
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
            "entry": {
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
    <span class="bold">GET</span> <a name="players_player_pbs_score"><code>/players/player/pbs/score</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_pbs"><code>/players/player/pbs</code></a> that only retrieves score PBs for a specified player.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_pbs_speed"><code>/players/player/pbs/speed</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_pbs"><code>/players/player/pbs</code></a> that only retrieves speed PBs for a specified player.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_pbs_deathless"><code>/players/player/pbs/deathless</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_pbs"><code>/players/player/pbs</code></a> that only retrieves deathless PBs for a specified player.
            </p>
        </li>
    </ul>
</p>