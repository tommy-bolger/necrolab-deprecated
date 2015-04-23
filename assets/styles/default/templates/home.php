<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8"/>
    <?php echo $this->title; ?>
    <?php echo $this->meta_tags; ?>
    <?php echo $this->css; ?>
<body>
    <div class="wrap">
        <nav id="w1" class="navbar-top navbar" role="navigation">
            <div class="container">
                <div class="site_logo_container">
                    <a class="site-header site_logo" href="/">
                        <span class="site_logo_small">The</span>
                        <br />
                        <span class="site_logo_medium">Necro Lab</span>
                    </a>
                </div>
                <div class="no_wrap">
                    <ul class="navbar-nav nav">
                        <li class="active">
                            <a class="first<?php if($this->active_page == 'power_rankings'): echo " selected_nav_button"; endif; ?>" href="/power_rankings">
                                <span class="menu_large">Power</span>
                                <br />
                                <span class="menu_small">Rankings</span>
                            </a>
                        </li>
                        <li>
                            <a<?php if($this->active_page == 'daily_rankings'): echo ' class="selected_nav_button"'; endif; ?> href="/daily_rankings">
                                <span class="menu_large">Daily</span>
                                <br />
                                <span class="menu_small">Rankings</span>
                            </a>
                        </li>
                        <li>
                            <a<?php if($this->active_page == 'score_rankings'): echo ' class="selected_nav_button"'; endif; ?> href="/score_rankings">
                                <span class="menu_large">Score</span>
                                <br />
                                <span class="menu_small">Rankings</span>
                            </a>
                        </li>
                        <li>
                            <a<?php if($this->active_page == 'speed_rankings'): echo ' class="selected_nav_button"'; endif; ?> href="/speed_rankings">
                                <span class="menu_large">Speed</span>
                                <br />
                                <span class="menu_small">Rankings</span>                        
                            </a>
                        </li>
                        <li>
                            <a<?php if($this->active_page == 'deathless_score_rankings'): echo ' class="selected_nav_button"'; endif; ?> href="/deathless_score_rankings">
                                <span class="menu_large">Deathless</span>
                                <br />
                                <span class="menu_small">Rankings</span>                        
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>        
        <div class="container">
            <div class="site-index">        
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
            <div class="last_refreshed">
                <p>
                    <?php echo $this->last_refreshed; ?> Updates every 20 minutes.
                </p>
            </div>
        </div>
    </footer>
    <?php echo $this->javascript; ?>
</body>
</html>