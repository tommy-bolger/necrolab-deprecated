<br />
<br />
<a name="endpoints">
    <a name="achievements_endpoints"><span class="menu_smaller">Achievements Endpoints</span></a>
</a>
<br />
<br />
<p>
    <span class="bold">GET</span> <a name="achievements"><code>/achievements</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all achievements available.
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
                <code>https://api.necrolab.com/achievements</code>
            </p>
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": [],
    "record_count": 30,
    "data": [
        {
            "name": "ACH_KILL_3_GREEN_BATS",
            "display_name": "Bat Trick",
            "description": "Kill three green bats",
            "icon_url": "http://cdn.akamai.steamstatic.com/steamcommunity/public/images/apps/247080/b272cda3cf5dc6d752e212a5519e3057bab13c09.jpg",
            "icon_gray_url": "http://cdn.akamai.steamstatic.com/steamcommunity/public/images/apps/247080/ef58d8c12cb3105bf3843f5697738361f11e554e.jpg"
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
    <span class="bold">GET</span> <a name="players_player_achievements"><code>/players/player/achievements</code></a>
    <ul class="indented_list">
        <li>
            <p>
                Retrieves all achievements for a specified player.
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
                <code>https://api.necrolab.com/players/player/achievements?steamid=76561198280004888</code>
            </p>
            <br />
            <span class="bold">Example Response</span>
            <p>
                <pre><code>{
    "request": {
        "steamid": "76561198280004888"
    },
    "record_count": 30,
    "data": [
        {
            "name": "ACH_KILL_3_GREEN_BATS",
            "display_name": "Bat Trick",
            "description": "Kill three green bats",
            "achieved": 1,
            "achieved_date": "2017-02-24 20:29:43",
            "icon_url": "http://cdn.akamai.steamstatic.com/steamcommunity/public/images/apps/247080/b272cda3cf5dc6d752e212a5519e3057bab13c09.jpg"
        },
        ...
    ]
}</code></pre>
            </p>
        </li>
    </ul>
</p>