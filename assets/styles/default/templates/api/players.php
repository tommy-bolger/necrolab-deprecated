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