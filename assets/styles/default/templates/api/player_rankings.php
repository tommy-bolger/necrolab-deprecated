<br />
<br />
<a name="endpoints">
    <a name="player_ranking_endpoints"><span class="menu_smaller">Player Ranking Endpoints</span></a>
</a>
<br />
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_rankings_power"><code>/players/player/rankings/power</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all of the dates that a player has power rankings for in a specified release and mode.
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
                            <td class="last">
                                <code>limit</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#limit"><code>limit</code></a> section under common parameters.
                            </td>
                            <td class="last"></td>
                        </tr>
                    </tbody>
                </table>
            </p>
            <br />
            <span class="bold">Example Request</span>
            <p>
                <code>https://api.necrolab.com/api/players/player/rankings/power/score?steamid=76561197987716503&release=amplified_dlc&mode=normal</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "steamid": 76561197987716500,
        "release": "amplified_dlc",
        "mode": "normal",
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null
    },
    "record_count": 35,
    "data": [
        "2017-01-25",
        "2017-01-26",
        "2017-01-27",
        "2017-01-28",
        ...
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_rankings_power_score"><code>/players/player/rankings/power/score</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_rankings_power"><code>/players/player/rankings/power</code></a> that only retrieves dates that the player has a score ranking.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_rankings_power_speed"><code>/players/player/rankings/power/speed</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_rankings_power"><code>/players/player/rankings/power</code></a> that only retrieves dates that the player has a speed ranking.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_rankings_power_deathless"><code>/players/player/rankings/power/deathless</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_rankings_power"><code>/players/player/rankings/power</code></a> that only retrieves dates that the player has a deathless ranking.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_rankings_power_character"><code>/players/player/rankings/power/character</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_rankings_power"><code>/players/player/rankings/power</code></a> that only retrieves dates that the player has a ranking in the specified character.
            </p>
            <br />
            <span class="bold">Required Parameters</span>
            <br />
            <br />
            <p>
                In addition to the required parameters in <a href="#players_player_rankings_power"><code>/players/player/rankings/power</code></a>, the following is also required:
                <br />
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
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_rankings_power_entries"><code>/players/player/rankings/power/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all of a player's power ranking entries for a specified release and date.
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
                <code>https://api.necrolab.com/api/players/player/rankings/power/entries?steamid=76561197987716503&release=amplified_dlc&start_date=2017-03-01</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "steamid": "76561197987716503",
        "release": "amplified_dlc",
        "start_date": "2017-03-01",
        "end_date": "2017-03-18",
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null
    },
    "record_count": 18,
    "data": [
        {
            "date": "2017-03-10",
            "steamid": 76561197987716500,
            "mode": {
                "name": "normal",
                "display_name": "Normal"
            },
            "cadence": {
                "score": {
                    "rank": 1253,
                    "rank_points": 1.5014223465856145,
                    "score": 4726
                },
                "speed": {
                    "rank": 25,
                    "rank_points": 15.85673427185604,
                    "time": "414.681"
                },
                "deathless": {
                    "rank": null,
                    "rank_points": null,
                    "win_count": null
                },
                "rank": 80,
                "rank_points": 17.358156618441654
            },
            "bard": {
                "score": {
                    "rank": 868,
                    "rank_points": 1.7220085959614224,
                    "score": 3445
                },
                "speed": {
                    "rank": 14,
                    "rank_points": 24.931854734470637,
                    "time": "234.696"
                },
                "deathless": {
                    "rank": null,
                    "rank_points": null,
                    "win_count": null
                },
                "rank": 49,
                "rank_points": 26.65386333043206
            },
            "aria": {
                "score": {
                    "rank": 225,
                    "rank_points": 3.2953793271793153,
                    "score": 2407
                },
                "speed": {
                    "rank": 47,
                    "rank_points": 9.654085098354873,
                    "time": "787.118"
                },
                "deathless": {
                    "rank": null,
                    "rank_points": null,
                    "win_count": null
                },
                "rank": 141,
                "rank_points": 12.949464425534188
            },
            "bolt": {
                "score": {
                    "rank": 253,
                    "rank_points": 3.0827817685178776,
                    "score": 1651
                },
                "speed": {
                    "rank": 20,
                    "rank_points": 18.908824790745232,
                    "time": "312.067"
                },
                "deathless": {
                    "rank": null,
                    "rank_points": null,
                    "win_count": null
                },
                "rank": 63,
                "rank_points": 21.99160655926311
            },
            "monk": {
                "score": {
                    "rank": null,
                    "rank_points": null,
                    "score": null
                },
                "speed": {
                    "rank": null,
                    "rank_points": null,
                    "time": null
                },
                "deathless": {
                    "rank": null,
                    "rank_points": null,
                    "win_count": null
                },
                "rank": null,
                "rank_points": 0
            },
            "dove": {
                "score": {
                    "rank": 860,
                    "rank_points": 1.728298666122134,
                    "score": 108
                },
                "speed": {
                    "rank": 19,
                    "rank_points": 19.685077966428707,
                    "time": "226.02"
                },
                "deathless": {
                    "rank": null,
                    "rank_points": null,
                    "win_count": null
                },
                "rank": 56,
                "rank_points": 21.413376632550843
            },
            "eli": {
                "score": {
                    "rank": 308,
                    "rank_points": 2.7694431031178897,
                    "score": 1756
                },
                "speed": {
                    "rank": 18,
                    "rank_points": 20.535029238882288,
                    "time": "599.181"
                },
                "deathless": {
                    "rank": null,
                    "rank_points": null,
                    "win_count": null
                },
                "rank": 48,
                "rank_points": 23.304472342000178
            },
            "melody": {
                "score": {
                    "rank": 1031,
                    "rank_points": 1.611965632944191,
                    "score": 2379
                },
                "speed": {
                    "rank": 14,
                    "rank_points": 24.931854734470637,
                    "time": "399.742"
                },
                "deathless": {
                    "rank": null,
                    "rank_points": null,
                    "win_count": null
                },
                "rank": 46,
                "rank_points": 26.543820367414828
            },
            "dorian": {
                "score": {
                    "rank": 182,
                    "rank_points": 3.7375381134749834,
                    "score": 1787
                },
                "speed": {
                    "rank": 18,
                    "rank_points": 20.535029238882288,
                    "time": "302.668"
                },
                "deathless": {
                    "rank": null,
                    "rank_points": null,
                    "win_count": null
                },
                "rank": 50,
                "rank_points": 24.27256735235727
            },
            "coda": {
                "score": {
                    "rank": null,
                    "rank_points": null,
                    "score": null
                },
                "speed": {
                    "rank": null,
                    "rank_points": null,
                    "time": null
                },
                "deathless": {
                    "rank": null,
                    "rank_points": null,
                    "win_count": null
                },
                "rank": null,
                "rank_points": 0
            },
            "nocturna": {
                "score": {
                    "rank": 2160,
                    "rank_points": 1.254904689831439,
                    "score": 3780
                },
                "speed": {
                    "rank": 32,
                    "rank_points": 13.043434710975438,
                    "time": "407.38"
                },
                "deathless": {
                    "rank": null,
                    "rank_points": null,
                    "win_count": null
                },
                "rank": 100,
                "rank_points": 14.298339400806878
            },
            "diamond": {
                "score": {
                    "rank": 358,
                    "rank_points": 2.561394650763757,
                    "score": 1656
                },
                "speed": {
                    "rank": 35,
                    "rank_points": 12.15335361230221,
                    "time": "484.532"
                },
                "deathless": {
                    "rank": null,
                    "rank_points": null,
                    "win_count": null
                },
                "rank": 84,
                "rank_points": 14.714748263065967
            },
            "story": {
                "score": {
                    "rank": null,
                    "rank_points": null,
                    "score": null
                },
                "speed": {
                    "rank": null,
                    "rank_points": null,
                    "time": null
                },
                "deathless": [],
                "rank": null,
                "rank_points": 0
            },
            "all": {
                "score": {
                    "rank": null,
                    "rank_points": null,
                    "score": null
                },
                "speed": {
                    "rank": null,
                    "rank_points": null,
                    "time": null
                },
                "deathless": [],
                "rank": null,
                "rank_points": 0
            },
            "score": {
                "total_score": 23695,
                "rank": 526,
                "rank_points": 23.26513689449862
            },
            "speed": {
                "total_time": 4168.085,
                "rank": 20,
                "rank_points": 180.23527839736835
            },
            "deathless": {
                "total_win_count": null,
                "rank": null,
                "rank_points": null
            },
            "rank": 66,
            "total_points": 203.50041529186694
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
    <span class="bold">GET</span> <a name="players_player_rankings_power_score_entries"><code>/players/player/rankings/power/score/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_rankings_power_entries"><code>/players/player/rankings/power/entries</code></a> that only retrieves a player's entries that have a score ranking.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_rankings_power_speed_entries"><code>/players/player/rankings/power/speed/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_rankings_power_entries"><code>/players/player/rankings/power/entries</code></a> that only retrieves a player's entries that have a speed ranking.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_rankings_power_deathless_entries"><code>/players/player/rankings/power/deathless/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_rankings_power_entries"><code>/players/player/rankings/power/entries</code></a> that only retrieves a player's entries that have a deathless ranking.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_rankings_power_character_entries"><code>/players/player/rankings/power/character/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#players_player_rankings_power_entries"><code>/players/player/rankings/power/entries</code></a> that only retrieves a player's entries that have a ranking for the specified character.
            </p>
            <br />
            <span class="bold">Required Parameters</span>
            <br />
            <br />
            <p>
                In addition to the required parameters in <a href="#players_player_rankings_power_entries"><code>/players/player/rankings/power/entries</code></a>, the following is also required:
                <br />
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
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_rankings_daily"><code>/players/player/rankings/daily</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all of the dates that a player has daily rankings in a given release, mode, and timeframe.
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
                                <code>number_of_days</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#number_of_days"><code>number_of_days</code></a> section under common parameters.
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
                            <td class="last">
                                <code>limit</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#limit"><code>limit</code></a> section under common parameters.
                            </td>
                            <td class="last"></td>
                        </tr>
                    </tbody>
                </table>
            </p>
            <br />
            <span class="bold">Example Request</span>
            <p>
                <code>https://api.necrolab.com/players/player/rankings/daily?steamid=76561198051520646&release=original&mode=normal&number_of_days=0</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "steamid": 76561198051520640,
        "release": "original",
        "mode": "normal",
        "number_of_days": 0,
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null
    },
    "record_count": 639,
    "data": [
        "2015-04-26",
        "2015-04-27",
        "2015-04-28",
        "2015-04-29",
        "2015-04-30",
        ...
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="players_player_rankings_daily_entries"><code>/players/player/rankings/daily/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all of a player's daily ranking entries for a specified release, mode, and timeframe.
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
                                <code>date</code>
                            </td>
                            <td>
                                Please see the <a href="#date"><code>date</code></a> section under common parameters.
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="last">
                                <code>number_of_days</code>
                            </td>
                            <td class="last">
                                Please see the <a href="#number_of_days"><code>number_of_days</code></a> section under common parameters.
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
                <code>https://api.necrolab.com/players/player/rankings/daily/entries?steamid=76561198051520646&release=original&number_of_days=0&start_date=2016-02-01</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "steamid": 76561198051520640,
        "release": "original",
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null,
        "number_of_days": 0,
        "start_date": "2016-02-01",
        "end_date": "2017-03-18"
    },
    "record_count": 358,
    "data": [
        {
            "date": "2016-02-01",
            "steamid": 76561198051520640,
            "mode": {
                "name": "normal",
                "display_name": "Normal"
            },
            "first_place_ranks": 19,
            "top_5_ranks": 76,
            "top_10_ranks": 27,
            "top_20_ranks": 18,
            "top_50_ranks": 16,
            "top_100_ranks": 14,
            "total_points": "8912.35030015233",
            "points_per_day": "40.327376923766",
            "total_dailies": 221,
            "total_wins": 4,
            "average_rank": "106.51131221719",
            "sum_of_ranks": 23539,
            "rank": 2
        },
        ...
    ]
},</code></pre>
            </p>
        </li>
    </ul>
</p>