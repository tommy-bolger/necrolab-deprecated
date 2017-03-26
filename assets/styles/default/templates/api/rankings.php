<br />
<br />
<a name="endpoints">
    <a name="ranking_endpoints"><span class="menu_smaller">Ranking Endpoints</span></a>
</a>
<br />
<br />
<p>
    <span class="bold">GET</span> <a name="rankings_power"><code>/rankings/power</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all dates that power rankings are generated on for the specified release and mode.
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
                <code>https://api.necrolab.com/rankings/power?release=amplified_dlc_early_access&mode=normal</code>
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
    "record_count": 50,
    "data": [
        "2017-03-13",
        "2017-03-14",
        "2017-01-25",
        "2017-01-26",
        ...
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="rankings_power_score"><code>/rankings/power/score</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#rankings_power"><code>/rankings/power</code></a>.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="rankings_power_speed"><code>/rankings/power/speed</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#rankings_power"><code>/rankings/power</code></a>.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="rankings_power_deathless"><code>/rankings/power/deathless</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#rankings_power"><code>/rankings/power</code></a>.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="rankings_power_character"><code>/rankings/power/character</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#rankings_power"><code>/rankings/power</code></a>.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="rankings_power_entries"><code>/rankings/power/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all power ranking entries for a specified release, mode, and date.
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
                <code>https://api.necrolab.com/rankings/power/entries?release=original_release&mode=normal&date=2017-03-10</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "release": "original_release",
        "mode": "normal",
        "date": "2017-03-10",
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null,
        "site": ""
    },
    "record_count": 232028,
    "data": [
        {
            "player": {
                "steamid": "76561198000263514",
                "personaname": "incnone",
                "linked": {
                    "steam": {
                        "personaname": "incnone",
                        "profile_url": "http://steamcommunity.com/id/incnone/"
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
            "cadence": {
                "score": {
                    "rank": 2,
                    "rank_points": 80.2291757924094,
                    "score": 23814
                },
                "speed": {
                    "rank": 2,
                    "rank_points": 80.2291757924094,
                    "time": "244.812"
                },
                "deathless": {
                    "rank": 9,
                    "rank_points": 34.54019295720166,
                    "win_count": null
                },
                "rank": 1,
                "rank_points": 194.99854454202045
            },
            "bard": {
                "score": {
                    "rank": 1,
                    "rank_points": 99.80427033011223,
                    "score": 25959
                },
                "speed": {
                    "rank": 3,
                    "rank_points": 67.17810208081157,
                    "time": null
                },
                "deathless": {
                    "rank": 2,
                    "rank_points": 80.2291757924094,
                    "win_count": null
                },
                "rank": 1,
                "rank_points": 247.2115482033332
            },
            "aria": {
                "score": {
                    "rank": 2,
                    "rank_points": 80.2291757924094,
                    "score": 14608
                },
                "speed": {
                    "rank": 1,
                    "rank_points": 99.80427033011223,
                    "time": "258.121"
                },
                "deathless": {
                    "rank": 5,
                    "rank_points": 50.8620283757091,
                    "win_count": null
                },
                "rank": 1,
                "rank_points": 230.89547449823073
            },
            "bolt": {
                "score": {
                    "rank": 2,
                    "rank_points": 80.2291757924094,
                    "score": 16653
                },
                "speed": {
                    "rank": 2,
                    "rank_points": 80.2291757924094,
                    "time": "152.557"
                },
                "deathless": {
                    "rank": 4,
                    "rank_points": 57.855052478691185,
                    "win_count": null
                },
                "rank": 1,
                "rank_points": 218.31340406350998
            },
            "monk": {
                "score": {
                    "rank": 2,
                    "rank_points": 80.2291757924094,
                    "score": 12661
                },
                "speed": {
                    "rank": 1,
                    "rank_points": 99.80427033011223,
                    "time": "297.821"
                },
                "deathless": {
                    "rank": 48,
                    "rank_points": 9.498429969393491,
                    "win_count": null
                },
                "rank": 1,
                "rank_points": 189.53187609191514
            },
            "dove": {
                "score": {
                    "rank": 26,
                    "rank_points": 15.372135394874483,
                    "score": 2340
                },
                "speed": {
                    "rank": 1,
                    "rank_points": 99.80427033011223,
                    "time": "115.661"
                },
                "deathless": {
                    "rank": 9,
                    "rank_points": 34.54019295720166,
                    "win_count": null
                },
                "rank": 3,
                "rank_points": 149.7165986821884
            },
            "eli": {
                "score": {
                    "rank": 14,
                    "rank_points": 24.931854734470637,
                    "score": 11981
                },
                "speed": {
                    "rank": 3,
                    "rank_points": 67.17810208081157,
                    "time": "251.533"
                },
                "deathless": {
                    "rank": 32,
                    "rank_points": 13.043434710975438,
                    "win_count": null
                },
                "rank": 9,
                "rank_points": 105.15339152625764
            },
            "melody": {
                "score": {
                    "rank": 2,
                    "rank_points": 80.2291757924094,
                    "score": 18609
                },
                "speed": {
                    "rank": 1,
                    "rank_points": 99.80427033011223,
                    "time": "232.152"
                },
                "deathless": {
                    "rank": 47,
                    "rank_points": 9.654085098354873,
                    "win_count": null
                },
                "rank": 2,
                "rank_points": 189.6875312208765
            },
            "dorian": {
                "score": {
                    "rank": 11,
                    "rank_points": 29.874430036695323,
                    "score": 10274
                },
                "speed": {
                    "rank": 2,
                    "rank_points": 80.2291757924094,
                    "time": "177.01"
                },
                "deathless": {
                    "rank": 15,
                    "rank_points": 23.64986821751062,
                    "win_count": null
                },
                "rank": 4,
                "rank_points": 133.75347404661534
            },
            "coda": {
                "score": {
                    "rank": 7,
                    "rank_points": 41.07005847776454,
                    "score": 3208
                },
                "speed": {
                    "rank": 13,
                    "rank_points": 26.373767168183036,
                    "time": null
                },
                "deathless": {
                    "rank": 5,
                    "rank_points": 50.8620283757091,
                    "win_count": 1
                },
                "rank": 5,
                "rank_points": 118.30585402165669
            },
            "nocturna": {
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
            "diamond": {
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
            "story": {
                "score": {
                    "rank": 1,
                    "rank_points": 99.80427033011223,
                    "score": 46300
                },
                "speed": {
                    "rank": 1,
                    "rank_points": 99.80427033011223,
                    "time": "961.715"
                },
                "deathless": [],
                "rank": 1,
                "rank_points": 199.60854066022446
            },
            "all": {
                "score": {
                    "rank": 2,
                    "rank_points": 80.2291757924094,
                    "score": 88904
                },
                "speed": {
                    "rank": 2,
                    "rank_points": 80.2291757924094,
                    "time": "2959.463"
                },
                "deathless": [],
                "rank": 1,
                "rank_points": 160.4583515848188
            },
            "score": {
                "total_score": 275311,
                "rank": 1,
                "rank_points": 792.2320740584858
            },
            "speed": {
                "total_time": 5650.845,
                "rank": 1,
                "rank_points": 980.6680261500047
            },
            "deathless": {
                "total_win_count": 1,
                "rank": 4,
                "rank_points": 364.7344889331565
            },
            "rank": 1,
            "total_points": 2137.634589141647
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
    <span class="bold">GET</span> <a name="rankings_power_score_entries"><code>/rankings/power/score/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#rankings_power_entries"><code>/rankings/power/entries</code></a> that only retrieves entries that have a score ranking.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="rankings_power_speed_entries"><code>/rankings/power/speed/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#rankings_power_entries"><code>/rankings/power/entries</code></a> that only retrieves entries that have a speed ranking.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="rankings_power_deathless_entries"><code>/rankings/power/deathless/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#rankings_power_entries"><code>/rankings/power/entries</code></a> that only retrieves entries that have a deathless ranking.
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="rankings_power_character_entries"><code>/rankings/power/character/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                An alias for <a href="#rankings_power_entries"><code>/rankings/power/entries</code></a> that only retrieves entries that have a ranking for the specified character.
            </p>
            <br />
            <span class="bold">Required Parameters</span>
            <p>
                In addition to the required parameters in <a href="#rankings_power_entries"><code>/rankings/power/entries</code></a>, the following is also required:
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
    <span class="bold">GET</span> <a name="rankings_daily"><code>/rankings/daily</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all dates that daily rankings are generated on for the specified release, mode, and number of days.
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
                None
            </p>
            <br />
            <span class="bold">Example Request</span>
            <p>
                <code>https://api.necrolab.com/rankings/daily?release=original_release&mode=normal&number_of_days=0</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "release": "original_release",
        "mode": "normal",
        "number_of_days": 0
    },
    "record_count": 677,
    "data": [
        "2015-10-04",
        "2015-10-05",
        "2015-10-06",
        "2015-05-04",
        "2015-05-05",
        ...
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="rankings_daily_number_of_days"><code>/rankings/daily/number_of_days</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all day periods that are supported for daily rankings.
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
                <code>https://api.necrolab.com/rankings/daily/number_of_days</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {},
    "record_count": 3
    "data": [
        30,
        100,
        0
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>

<br />
<p>
    <span class="bold">GET</span> <a name="rankings_daily_entries"><code>/rankings/daily/entries</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all daily ranking entries for a specified release, mode, and date.
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
                <code>https://api.necrolab.com/rankings/daily/entries?release=original_release&mode=normal&date=2016-02-10&number_of_days=0</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "release": "original_release",
        "mode": "normal",
        "date": "2016-02-10",
        "start": 0,
        "limit": 100,
        "sort_by": null,
        "sort_direction": null,
        "site": "",
        "number_of_days": 0
    },
    "record_count": 120025
    "data": [
        {
            "player": {
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
            },
            "first_place_ranks": 19,
            "top_5_ranks": 90,
            "top_10_ranks": 42,
            "top_20_ranks": 23,
            "top_50_ranks": 26,
            "top_100_ranks": 11,
            "total_points": "10467.8913974662",
            "points_per_day": "42.901194251911",
            "total_dailies": 244,
            "total_wins": 4,
            "average_rank": "94.040983606557",
            "sum_of_ranks": 22946,
            "rank": 1
        },
        ...
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>