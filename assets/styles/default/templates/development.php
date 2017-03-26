<span class="menu_smaller">Introduction</span>
<p>
    Necro Lab is built as a service oriented architecture. All dynamic data powering the website comes from its API. Most pages on the site are barebones templates that utilize Javascript to make requests against the site's API to render the dynamic content of the page.
</p>
<br />
<span class="menu_smaller">Backend</span>
<p>
    Necro Lab is built with PHP 7.0, Postgres 9.6, and Redis 3.2. The site is built as a <a class="underline" href="https://github.com/tommy-bolger/necrolab" target="_blank">module</a> for my custom framework, <a class="underline" href="https://github.com/tommy-bolger/Flow" target="_blank">Flow</a>
</p>
<p>
    The live site is served from a Nginx server that sits behind a Varnish server to cache web content. The live site's API is served directly from Nginx to bypass Varnish's cache. The site's code sits on a separate PHP-FPM server to offload web and API requests to a dedicated instance. A separate, dedicated backend VPS instance runs several scheduled tasks from crontab that populate the site's data. This instance handles most of the heavy lifting to allow the PHP-FPM server to use its resources solely for web requests. Redis and Postgres are also hosted on separate VPS instances. All instances communicate with each other over a private network that is not accessible publicly. Inter-server communications are done via SSL; either by SSL configuration (Postgres) or via stunnel.
</p>
<p>
    The following diagram illusrates how the live site's architecture is laid out:
</p>
<p>
    <img src="/assets/images/modules/necrolab/styles/default/necrolab_architecture.png" />
</p>
<br />
<span class="menu_smaller">Frontend</span>
<p>
    Necro Lab uses jQuery 3.1.1 and Bootstrap 3.3.1 as its frontend backbone. All tables on the site use a library, <code>NecroTable</code>, to interact with its data via AJAX calls to the API. <code>NecroTable</code> is powered by jQuery DataTables 1.10.13.
</p>
<br />
<span class="menu_smaller">Source Code</span>
<p>
    To access the source code for this site navigate to these links:
    <br />
    <ul>
        <li><a class="underline" href="https://github.com/tommy-bolger/Flow" target="_blank">The Flow framework</a></li>
        <li><a class="underline" href="https://github.com/tommy-bolger/necrolab" target="_blank">The Necro Lab module</a></li>
    </ul>

    If you have any questions about the source code, or wish to contribute to the project, please contact me about getting setup with a development environment. I look forward to working with you!
</p>