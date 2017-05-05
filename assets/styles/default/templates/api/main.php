<a name="table_of_contents">
    <span class="menu_smaller">Table of Contents</span>
</a>
<p>
    <ul class="table_of_contents_items indented_list">
        <li><a href="#introduction">Introduction</a></li>
        <li><a href="#response_codes">Response Codes</a></li>
        <li><a href="#response_structure">Response Structure</a></li>
        <li><a href="#common_parameters">Common Parameters</a></li>
        <li>
            <a href="#top_level_endpoints">Top Level Endpoints</a>
            <ul class="table_of_contents_items">
                <li><a href="#releases"><code>/releases</code></a></li>
                <li><a href="#modes"><code>/modes</code></a></li>
                <li><a href="#characters"><code>/characters</code></a></li>
                <li><a href="#external_sites"><code>/external_sites</code></a></li>
            </ul>
        </li>
        <li>
            <a href="#leaderboard_endpoints">Leaderboard Endpoints</a>
            <ul class="table_of_contents_items">
                <li><a href="#leaderboards_xml"><code>/leaderboards/xml</code></a></li>
                <li><a href="#leaderboards"><code>/leaderboards</code></a></li>
                <li><a href="#leaderboards_score"><code>/leaderboards/score</code></a></li>
                <li><a href="#leaderboards_speed"><code>/leaderboards/speed</code></a></li>
                <li><a href="#leaderboards_deathless"><code>/leaderboards/deathless</code></a></li>
                <li><a href="#leaderboards_daily"><code>/leaderboards/daily</code></a></li>
                <li><a href="#leaderboards_leaderboard"><code>/leaderboards/leaderboard</code></a></li>
                <li><a href="#leaderboards_snapshots"><code>/leaderboards/snapshots</code></a></li>
                <li><a href="#leaderboards_entries"><code>/leaderboards/entries</code></a></li>
                <li><a href="#leaderboards_daily_entries"><code>/leaderboards/daily/entries</code></a></li>
                <li><a href="#leaderboards_replays"><code>/leaderboards/replays</code></a></li>
                <li><a href="#leaderboards_replays_replay"><code>/leaderboards/replays/replay</code></a></li>
            </ul>
        </li>
        <li>
            <a href="#ranking_endpoints">Ranking Endpoints</a>
            <ul class="table_of_contents_items">
                <li><a href="#rankings_power"><code>/rankings/power</code></a></li>
                <li><a href="#rankings_power_score"><code>/rankings/power/score</code></a></li>
                <li><a href="#rankings_power_speed"><code>/rankings/power/speed</code></a></li>
                <li><a href="#rankings_power_deathless"><code>/rankings/power/deathless</code></a></li>
                <li><a href="#rankings_power_character"><code>/rankings/power/character</code></a></li>
                <li><a href="#rankings_power_entries"><code>/rankings/power/entries</code></a></li>
                <li><a href="#rankings_power_score_entries"><code>/rankings/power/score/entries</code></a></li>
                <li><a href="#rankings_power_speed_entries"><code>/rankings/power/speed/entries</code></a></li>
                <li><a href="#rankings_power_deathless_entries"><code>/rankings/power/deathless/entries</code></a></li>
                <li><a href="#rankings_power_character_entries"><code>/rankings/power/character/entries</code></a></li>
                <li><a href="#rankings_daily"><code>/rankings/daily</code></a></li>
                <li><a href="#rankings_daily_entries"><code>/rankings/daily/entries</code></a></li>
            </ul>
        </li>
        <li>
            <a href="#player_endpoints">Player Endpoints</a>
            <ul class="table_of_contents_items">
                <li><a href="#players"><code>/players</code></a></li>
                <li><a href="#players_player"><code>/players/player</code></a></li>
            </ul>
        </li>
        <li>
            <a href="#player_endpoints">Achievements Endpoints</a>
            <ul class="table_of_contents_items">
                <li><a href="#achievements"><code>/achievements</code></a></li>
                <li><a href="#players_player_achievements"><code>/players/player/achievements</code></a></li>
            </ul>
        </li>
        <li>
            <a href="#pbs_endpoints">PBs Endpoints</a>
            <ul class="table_of_contents_items">
                <li><a href="#players_pbs"><code>/players/pbs</code></a></li>
                <li><a href="#players_pbs_score"><code>/players/pbs/score</code></a></li>
                <li><a href="#players_pbs_speed"><code>/players/pbs/speed</code></a></li>
                <li><a href="#players_pbs_deathless"><code>/players/pbs/deathless</code></a></li>
                <li><a href="#players_player_pbs"><code>/players/player/pbs</code></a></li>
                <li><a href="#players_player_pbs_score"><code>/players/player/pbs/score</code></a></li>
                <li><a href="#players_player_pbs_speed"><code>/players/player/pbs/speed</code></a></li>
                <li><a href="#players_player_pbs_deathless"><code>/players/player/pbs/deathless</code></a></li>
            </ul>
        </li>
        <li>
            <a href="#player_leaderboard_endpoints">Player Leaderboard Endpoints</a>
            <ul class="table_of_contents_items">
                <li><a href="#players_player_leaderboards"><code>/players/player/leaderboards</code></a></li>
                <li><a href="#players_player_leaderboards_score"><code>/players/player/leaderboards/score</code></a></li>
                <li><a href="#players_player_leaderboards_speed"><code>/players/player/leaderboards/speed</code></a></li>
                <li><a href="#players_player_leaderboards_deathless"><code>/players/player/leaderboards/deathless</code></a></li>
                <li><a href="#players_player_leaderboards_daily"><code>/players/player/leaderboards/daily</code></a></li>
                <li><a href="#players_player_leaderboards_snapshots"><code>/players/player/leaderboards/snapshots</code></a></li>
                <li><a href="#players_player_leaderboards_entries"><code>/players/player/leaderboards/entries</code></a></li>
                <li><a href="#players_player_leaderboards_score_entries"><code>/players/player/leaderboards/score/entries</code></a></li>
                <li><a href="#players_player_leaderboards_speed_entries"><code>/players/player/leaderboards/speed/entries</code></a></li>
                <li><a href="#players_player_leaderboards_deathless_entries"><code>/players/player/leaderboards/deathless/entries</code></a></li>
                <li><a href="#players_player_leaderboards_daily_entries"><code>/players/player/leaderboards/daily/entries</code></a></li>
                <li><a href="#players_player_leaderboards_replays"><code>/players/player/leaderboards/replays</code></a></li>
            </ul>
        </li>
        <li>
            <a href="#player_ranking_endpoints">Player Ranking Endpoints</a>
            <ul class="table_of_contents_items">
                <li><a href="#players_player_rankings_power"><code>/players/player/rankings/power</code></a></li>
                <li><a href="#players_player_rankings_power_score"><code>/players/player/rankings/power/score</code></a></li>
                <li><a href="#players_player_rankings_power_speed"><code>/players/player/rankings/power/speed</code></a></li>
                <li><a href="#players_player_rankings_power_deathless"><code>/players/player/rankings/power/deathless</code></a></li>
                <li><a href="#players_player_rankings_power_character"><code>/players/player/rankings/power/character</code></a></li>
                <li><a href="#players_player_rankings_power_entries"><code>/players/player/rankings/power/entries</code></a></li>
                <li><a href="#players_player_rankings_power_score_entries"><code>/players/player/rankings/power/score/entries</code></a></li>
                <li><a href="#players_player_rankings_power_speed_entries"><code>/players/player/rankings/power/speed/entries</code></a></li>
                <li><a href="#players_player_rankings_power_deathless_entries"><code>/players/player/rankings/power/deathless/entries</code></a></li>
                <li><a href="#players_player_rankings_power_character_entries"><code>/players/player/rankings/power/character/entries</code></a></li>
                <li><a href="#players_player_rankings_daily"><code>/players/player/rankings/daily</code></a></li>
                <li><a href="#players_player_rankings_daily_entries"><code>/players/player/rankings/daily/entries</code></a></li>
            </ul>
        </li>
    </ul>
</p>
<br />
<br />
<a name="introduction">
    <span class="menu_smaller">Introduction</span>
</a>
<br />
<br />
<p>
    Necro Lab's API is RESTful and can be completely accessed via GET requests. It's base URL is:
</p>
<p>
    <code>
        https://api.necrolab.com
    </code>
</p>
<p>
    Https is required for all requests.
</p>
<br />
<br />
<a name="response_codes">
    <a name="response_codes"><span class="menu_smaller">Response Codes</span></a>
</a>
<br />
<table class="documentation">
    <thead>
        <tr>
            <th>Http Code</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <a name="http_200">
                    <code>200</code>
                </a>
            </td>
            <td>
                The request was successful.
            </td>
        </tr>
        <tr>
            <td>
                <a name="http_400">
                    <code>400</code>
                </a>
            </td>
            <td>
                A request parameter was incorrect. The request body should provide a description of which parameter was incorrect.
            </td>
        </tr>
        <tr>
            <td>
                <a name="http_500">
                    <code>500</code>
                </a>
            </td>
            <td>
                An internal error was encountered on the server. Please contact the site's administrator with the incident number specified in the response body to help resolve this error.
            </td>
        </tr>
        <tr>
            <td class="last">
                <a name="http_503">
                    <code>503</code>
                </a>
            </td>
            <td class="last">
                The site is down for maintenance. Please check back later.
            </td>
        </tr>
    </tbody>
</table>
<br />
<br />
<a name="response_body">
    <a name="response_structure"><span class="menu_smaller">Response Structure</span></a>
</a>
<br />
<br />
<p>
    All requests have a json response structure in the following format:
<p>
    <table class="documentation">
        <thead>
            <tr>
                <th>Property</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <a name="request">
                        <code>request</code>
                    </a>
                </td>
                <td>
                    Contains all of the parameters passed into the request.
                </td>
            </tr>
            <tr>
                <td class="last">
                    <a name="record_count">
                        <code>record_count</code>
                    </a>
                </td>
                <td>
                    The total number of records that this resultset contains. Can be used with <a href="#start"><code>start</code></a> and <a href="#limit"><code>limit</code></a> for pagination.
                </td>
            </tr>
            <tr>
                <td class="last">
                    <a name="response">
                        <code>response</code>
                    </a>
                </td>
                <td class="last">
                    Contains the resultset of the request.
                </td>
            </tr>
        </tbody>
    </table>
<br />
<br />
<p>
    An example json response structure looks similar to this:
</p>
    <pre><code>{
    "request": {
        "release": "amplified_dlc_early_access",
        "date": "2017-03-01",
        "start": 0,
        "limit": 100,
        "sort_by": "rank",
        "sort_direction": "asc",
        ...
    },
    "record_count": 0,
    "data": [
        ...
    ]
}</code></pre>
<br />
<br />
<a name="common_parameters">
    <a name="common_parameters"><span class="menu_smaller">Common Parameters</span></a>
</a>
<br />
<table class="documentation">
    <thead>
        <tr>
            <th>Parameter</th>
            <th>Description</th>
            <th>Valid Values</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <a name="release">
                    <code>release</code>
                </a>
            </td>
            <td>
                The name of the release that the data applies to. An always up to date list of releases can be found via the <a href="#releases"><code>/releases</code></a> endpoint.
            </td>
            <td>
                <ul class="plain_list">
                    <li>
                        <code>alpha</code>
                    </li>
                    <li>
                        <code>early_access</code>
                    </li>
                    <li>
                        <code>original_release</code>
                    </li>
                    <li>
                        <code>amplified_dlc_early_access</code>
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>
                <a name="mode">
                    <code>mode</code>
                </a>
            </td>
            <td>
                The name of the mode that the data applies to. An always up to date list of modes can be found via the <a href="#modes"><code>/modes</code></a> endpoint.
            </td>
            <td>
                <ul class="plain_list">
                    <li>
                        <code>normal</code>
                    </li>
                    <li>
                        <code>hard</code>
                    </li>
                    <li>
                        <code>no_return</code>
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>
                <a name="site">
                    <code>site</code>
                </a>
            </td>
            <td>Specifying a site name only pulls data for players that have linked their respective account to Necro Lab. An always up to date list of sites can be found via the <a href="#external_sites"><code>/external_sites</code></a> endpoint. Defaults to <code>null</code>, which assumes <code>steam</code>.</td>
            <td>
                <ul class="plain_list">
                    <li>
                        <code>beampro</code>
                    </li>
                    <li>
                        <code>discord</code>
                    </li>
                    <li>
                        <code>reddit</code>
                    </li>
                    <li>
                        <code>twitch</code>
                    </li>
                    <li>
                        <code>twitter</code>
                    </li>
                    <li>
                        <code>youtube</code>
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>
                <a name="character">
                    <code>character</code>
                </a>
            </td>
            <td>The character that the result will apply to. An always up to date list of characters can be found via the <a href="#characters"><code>/characters</code></a> endpoint.
            <td>
                <ul class="plain_list">
                    <li>
                        <code>cadence</code>
                    </li>
                    <li>
                        <code>bard</code>
                    </li>
                    <li>
                        <code>aria</code>
                    </li>
                    <li>
                        <code>bolt</code>
                    </li>
                    <li>
                        <code>monk</code>
                    </li>
                    <li>
                        <code>dove</code>
                    </li>
                    <li>
                        <code>eli</code>
                    </li>
                    <li>
                        <code>melody</code>
                    </li>
                    <li>
                        <code>dorian</code>
                    </li>
                    <li>
                        <code>coda</code>
                    </li>
                    <li>
                        <code>nocturna</code>
                    </li>
                    <li>
                        <code>diamond</code>
                    </li>
                    <li>
                        <code>all</code>
                    </li>
                    <li>
                        <code>story</code>
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>
                <a name="date">
                    <code>date</code>
                </a>
            </td>
            <td>The date that the resultset will be pulled for.</td>
            <td>
                A date in the <code>YYYY-MM-DD</code> format.
            </td>
        </tr>
        <tr>
            <td>
                <a name="start">
                    <code>start</code>
                </a>
            </td>
            <td>The offset that the data will begin from. This can be used in conjunction with <a href="#limit"><code>limit</code></a> for pagination. Defaults to 0.</td>
            <td>
                A valid integer between <code>0</code> and the value found in <code>request.record_count</code>
            </td>
        </tr>
        <tr>
            <td>
                <a name="limit">
                    <code>limit</code>
                </a>
            </td>
            <td>Limits the result to a specified number of rows. This can be used in conjunction with <a href="#start"><code>start</code></a> for pagination. Defaults to 100.</td>
            <td>
                A valid integer between <code>0</code> and <code>10000</code>.
            </td>
        </tr>
        <tr>
            <td>
                <a name="sort_by">
                    <code>sort_by</code>
                </a>
            </td>
            <td>Sorts the result by the specified column.</td>
            <td>
                Please see specific endpoint documentation for list of fields to sort by as it varies. Defaults to <code>null</code> for the default sort.
            </td>
        </tr>
        <tr>
            <td>
                <a name="sort_direction">
                    <code>sort_direction</code>
                </a>
            </td>
            <td>The direction to sort the result by. Defaults to <code>asc</code>.</td>
            <td>
                <ul class="plain_list">
                    <li>
                        <code>asc</code> for ascending order.
                    </li>
                    <li>
                        <code>desc</code> for descending order.
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>
                <a name="lbid">
                    <code>lbid</code>
                </a>
            </td>
            <td>The leaderboard's unique ID.</td>
            <td>
                A valid integer greater than 0. Values can be found by using the <code>lbid</code> property in the results of the following endpoints:
                <ul class="plain_list">
                    <li>
                        <a href="#leaderboards_score"><code>/leaderboards/score</code></a>
                    </li>
                    <li>
                        <a href="#leaderboards_speed"><code>/leaderboards/speed</code></a>
                    </li>
                    <li>
                        <a href="#leaderboards_deathless"><code>/leaderboards/deathless</code></a>
                    </li>
                    <li>
                        <a href="#leaderboards_daily"><code>/leaderboards/daily</code></a>
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>
                <a name="steamid">
                    <code>steamid</code>
                </a>
            </td>
            <td>The steamid of the player.</td>
            <td>
                A valid integer greater than 0. steamids that exist in NecroLab can be obtained via the <a href="#players"><code>/players</code></a> endpoint.
            </td>
        </tr>
        <tr>
            <td>
                <a name="number_of_days">
                    <code>number_of_days</code>
                </a>
            </td>
            <td>The timeframe in days to view daily rankings for.</td>
            <td>
                <ul class="plain_list">
                    <li>
                        <code>30</code> for a 30 day view.
                    </li>
                    <li>
                        <code>100</code> for a 100 day view.
                    </li>
                    <li>
                        <code>0</code> for all days since the beginning of the <a href="#release"><code>release</code></a>.
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>
                <a name="start_date">
                    <code>start_date</code>
                </a>
            </td>
            <td>The date that the result will start at.</td>
            <td>
                A date in the <code>YYYY-MM-DD</code> format.
            </td>
        </tr>
        <tr>
            <td>
                <a name="end_date">
                    <code>end_date</code>
                </a>
            </td>
            <td>The date that the result will end at.</td>
            <td>
                A date in the <code>YYYY-MM-DD</code> format. Defaults to today's date, or the corresponding <a href="#release"><code>release</code></a> end date if that is before today's date.
            </td>
        </tr>
        <tr>
            <td class="last">
                <a name="search">
                    <code>search</code>
                </a>
            </td>
            <td class="last">Filters records in the result to only include usernames containing this value. Username filtering applies to the corresponding <a href="#site"><code>site</code></a></td>
            <td class="last">
                Any string. Value is case-insensitive.
            </td>
        </tr>
    </tbody>
</table>
<br />
<br />
<a name="endpoints">
    <a name="top_level_endpoints"><span class="menu_smaller">Top Level Endpoints</span></a>
</a>
<br />
<br />
<p>
    <span class="bold">GET</span> <a name="releases"><code>/releases</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all releases that are currently active.
            </p>
            <br />
            <span class="bold">Required Parameters</span>
            <p>
                None
            </p>
            <span class="bold">Optional Parameters</span>
            <p>
                None
            </p>
            <span class="bold">Example Request</span>
            <p>
                <code>https://api.necrolab.com/releases</code>
            </p>
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {},
    "record_count": 4
    "data": [
        {
            "name": "alpha",
            "display_name": "Alpha",
            "start_date": "2000-01-01",
            "end_date": "2014-07-30"
        },
        {
            "name": "early_access",
            "display_name": "Early Access",
            "start_date": "2014-07-30",
            "end_date": "2015-04-22"
        },
        {
            "name": "original_release",
            "display_name": "Original Release",
            "start_date": "2015-04-23",
            "end_date": null
        },
        {
            "name": "amplified_dlc_early_access",
            "display_name": "Amplified DLC Early Access",
            "start_date": "2017-01-24",
            "end_date": null
        }
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="modes"><code>/modes</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all modes that are currently active.
            </p>
            <br />
            <span class="bold">Required Parameters</span>
            <p>
                None
            </p>
            <span class="bold">Optional Parameters</span>
            <p>
                None
            </p>
            <span class="bold">Example Request</span>
            <p>
                <code>https://api.necrolab.com/modes</code>
            </p>
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": [],
    "record_count": 3,
    "data": [
        {
            "name": "normal",
            "display_name": "Normal"
        },
        {
            "name": "hard",
            "display_name": "Hard"
        },
        {
            "name": "no_return",
            "display_name": "No Return"
        }
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>

<br />
<p>
    <span class="bold">GET</span> <a name="characters"><code>/characters</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all characters that are currently active.
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
                <code>https://api.necrolab.com/characters</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {},
    "record_count": 13,
    "data": [
        {
            "name": "cadence",
            "display_name": "Cadence"
        },
        {
            "name": "bard",
            "display_name": "Bard"
        },
        {
            "name": "aria",
            "display_name": "Aria"
        },
        {
            "name": "bolt",
            "display_name": "Bolt"
        },
        
            "name": "monk",
            "display_name": "Monk"
        },
        {
            "name": "dove",
            "display_name": "Dove"
        },
        {
            "name": "eli",
            "display_name": "Eli"
        },
        {
            "name": "melody",
            "display_name": "Melody"
        },
        {
            "name": "dorian",
            "display_name": "Dorian"
        },
        {
            "name": "coda",
            "display_name": "Coda"
        },
        {
            "name": "nocturna",
            "display_name": "Nocturna"
        },
        {
            "name": "story",
            "display_name": "Cadence, Melody, and Aria"
        },
        {
            "name": "all",
            "display_name": "All"
        }
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>
<br />
<p>
    <span class="bold">GET</span> <a name="external_sites"><code>/external_sites</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all active external sites that results in other endpoints can be filtered by.
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
                <code>https://api.necrolab.com/external_sites</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {},
    "record_count": 6
    "data": [
        {
            "name": "beampro",
            "display_name": "Beam.pro"
        },
        {
            "name": "discord",
            "display_name": "Discord"
        },
        {
            "name": "reddit",
            "display_name": "Reddit"
        },
        {
            "name": "twitch",
            "display_name": "Twitch"
        },
        {
            "name": "twitter",
            "display_name": "Twitter"
        },
        {
            "name": "youtube",
            "display_name": "Youtube"
        }
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>
<?php echo $this->leaderboards; ?>
<?php echo $this->rankings; ?>
<?php echo $this->players; ?>
<?php echo $this->achievements; ?>
<?php echo $this->pbs; ?>
<?php echo $this->player_leaderboards; ?>
<?php echo $this->player_rankings; ?>