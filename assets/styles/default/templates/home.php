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
                        <span class="site_logo_medium no_wrap">Necro Lab<img src="/assets/images/modules/necrolab/styles/default/Amplified2.png" /></span>
                    </a>
                </div>
                <div class="no_wrap">
                    <ul class="navbar-nav nav">
                        <li class="active">
                            <a class="first<?php if($this->active_page_category == 'rankings'): echo " selected_nav_button"; endif; ?>" href="/rankings/">
                                <span class="menu_large">Rankings<span class="menu_small"><?php if($this->active_page_category == 'rankings'): echo "&#x25BC;"; else: echo "&#x25B2;"; endif; ?></span></span>
                            </a>
                        </li>
                        <li class="active">
                            <a class="first<?php if($this->active_page_category == 'leaderboards'): echo " selected_nav_button"; endif; ?>" href="/leaderboards/">
                                <span class="menu_large">Leaderboards<span class="menu_small"><?php if($this->active_page_category == 'leaderboards'): echo "&#x25BC;"; else: echo "&#x25B2;"; endif; ?></span></span>
                            </a>
                        </li>
                        <li class="active">
                            <a class="first<?php if($this->active_page_category == 'dailies'): echo " selected_nav_button"; endif; ?>" href="/dailies/">
                                <span class="menu_large">Dailies<span class="menu_small"><?php if($this->active_page_category == 'dailies'): echo "&#x25BC;"; else: echo "&#x25B2;"; endif; ?></span></span>
                            </a>
                        </li>
                        <li class="active">
                            <a class="first<?php if($this->active_page_category == 'players'): echo " selected_nav_button"; endif; ?>" href="/players/">
                                <span class="menu_large">Players</span>
                            </a>
                        </li>
                    </ul>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
                <div class="no_wrap">
                    <ul class="navbar-nav nav">
                        <?php if($this->active_page_category == 'rankings'): ?>
                        <li class="active">
                            <a class="first<?php if($this->active_page == 'power_rankings'): echo " selected_nav_button"; endif; ?>" href="/rankings/power">
                                <span class="menu_small">Power</span>
                            </a>
                        </li>
                        <li>
                            <a<?php if($this->active_page == 'score_rankings'): echo ' class="selected_nav_button"'; endif; ?> href="/rankings/categories/score">
                                <span class="menu_small">Score</span>
                            </a>
                        </li>
                        <li>
                            <a<?php if($this->active_page == 'speed_rankings'): echo ' class="selected_nav_button"'; endif; ?> href="/rankings/categories/speed">
                                <span class="menu_small">Speed</span>                       
                            </a>
                        </li>
                        <li>
                            <a<?php if($this->active_page == 'deathless_rankings'): echo ' class="selected_nav_button"'; endif; ?> href="/rankings/categories/deathless">
                                <span class="menu_small">Deathless</span>                       
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($this->active_page_category == 'leaderboards'): ?>
                        <li class="active">
                            <a class="first<?php if($this->active_page == 'score_leaderboards'): echo " selected_nav_button"; endif; ?>" href="/leaderboards/score/">
                                <span class="menu_small">Score</span>
                            </a>
                        </li>
                        <li>
                            <a<?php if($this->active_page == 'speed_leaderboards'): echo ' class="selected_nav_button"'; endif; ?> href="/leaderboards/speed/">
                                <span class="menu_small">Speed</span>
                            </a>
                        </li>
                        <li>
                            <a<?php if($this->active_page == 'deathless_leaderboards'): echo ' class="selected_nav_button"'; endif; ?> href="/leaderboards/deathless/">
                                <span class="menu_small">Deathless</span>
                            </a>
                        </li>
                        <li>
                            <a<?php if($this->active_page == 'daily_leaderboards'): echo ' class="selected_nav_button"'; endif; ?> href="/leaderboards/daily/">
                                <span class="menu_small">Daily</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($this->active_page_category == 'dailies'): ?>
                        <!-- <li class="active">
                            <a class="first<?php if($this->active_page == 'season'): echo " selected_nav_button"; endif; ?>" href="/dailies/season">
                                <span class="menu_small">Season</span>
                            </a>
                        </li> -->
                        <?php if(!empty($this->daily_ranking_day_types)): ?>
                            <?php foreach($this->daily_ranking_day_types as $number_of_days => $daily_ranking_day_type_id): ?>
                                <?php if($number_of_days != 0): ?>
                                <li>
                                    <a<?php if($this->active_page == "{$number_of_days}d"): echo ' class="selected_nav_button"'; endif; ?> href="/dailies/rankings?dt=<?php echo $number_of_days; ?>">
                                        <span class="menu_small"><?php echo $number_of_days; ?> Day</span>
                                    </a>
                                </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <li>
                            <a<?php if($this->active_page == 'all_time'): echo ' class="selected_nav_button"'; endif; ?> href="/dailies/rankings">
                                <span class="menu_small">All Time</span>
                            </a>
                        </li>
                        <?php endif; ?>
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