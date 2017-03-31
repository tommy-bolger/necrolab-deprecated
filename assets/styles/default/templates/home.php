<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php echo $this->meta_tags; ?>
    <?php echo $this->title; ?>
    <?php echo $this->css; ?>
<body>
    <div class="wrap">
        <nav id="w1" class="navbar navbar-top header" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">
                        <div class="site_logo_small">The</div>
                        <div class="site_logo_medium no_wrap">Necro Lab<img src="/assets/images/modules/necrolab/styles/default/Amplified.png" /></div>
                    </a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle menu_smaller" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Rankings <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="menu_smaller" href="/rankings/power">Power</a></li>
                                <li><a class="menu_smaller" href="/rankings/power/categories/score">Score</a></li>
                                <li><a class="menu_smaller" href="/rankings/power/categories/speed">Speed</a></li>
                                <li><a class="menu_smaller" href="/rankings/power/categories/deathless">Deathless</a></li>
                                <li><a class="menu_smaller" href="/rankings/power/categories/character">Character</a></li>
                                <li><a class="menu_smaller" href="/rankings/daily">Daily</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle menu_smaller" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Leaderboards <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="menu_smaller" href="/leaderboards/score">Score</a></li>
                                <li><a class="menu_smaller" href="/leaderboards/speed">Speed</a></li>
                                <li><a class="menu_smaller" href="/leaderboards/deathless">Deathless</a></li>
                                <li><a class="menu_smaller" href="/leaderboards/daily/entries">Daily</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a class="menu_smaller" href="/players">Players</a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle menu_smaller" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">About <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="menu_smaller" href="/about/overview">Overview</a></li>
                                <li><a class="menu_smaller" href="/about/api">API</a></li>
                                <li><a class="menu_smaller" href="/about/development">Development</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>        
        <div class="container">
            <div class="site-index">  
                <?php echo $this->breadcrumbs; ?>
                <?php echo $this->content; ?>
            </div>                
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <p class="pull-left">
                Created by <a href="http://www.twitch.tv/wilarseny" target="_blank">Wilarseny</a> and <a href="http://www.twitch.tv/squega" target="_blank">Squega</a>. Ranking algorithm provided by <a href="http://www.twitch.tv/jakkdl" target="_blank">Jakkdl</a>. Graphics assistance by <a href="http://www.twitch.tv/gromfalloon" target="_blank">Gromfalloon</a>.
                <br /> 
                Website name by <a href="http://www.twitch.tv/ratracing" target="_blank">RatRacing</a>.
            </p>
            <p class="pull-right"><a href="http://steampowered.com">Powered by Steam</a><br />Powered by <a href="https://github.com/tommy-bolger/Flow" rel="external">Flow Framework</a></p>
            <div class="clear"></div>
        </div>
    </footer>
    <?php echo $this->javascript; ?>
    <script type="text/javascript">
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        
        ga('create', 'UA-41045236-2', 'auto');
        ga('send', 'pageview');
    </script>
</body>
</html>