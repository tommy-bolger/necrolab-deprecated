<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php echo $this->title; ?>
    <?php echo $this->meta_tags; ?>
    <?php echo $this->css; ?>
<body>
    <div class="wrap">
        <nav id="w1" class="navbar-top navbar" role="navigation"><div class="container">
            <div class="navbar-header"><button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#w1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span></button>
            </div>
            <div id="w1-collapse" class="collapse navbar-collapse">
                <ul id="w2" class="navbar-nav nav">
                    <li class="active">
                        <a class="first" href="/power_rankings"><img src="<?php echo $this->power_rankings_button; ?>" alt=""></a></li>
                    <li>
                        <a href="/daily_rankings"><img src="<?php echo $this->daily_rankings_button; ?>" alt=""></a>
                    </li>
                    <li>
                        <a class="site-header" href="/"><img src="<?php echo $this->site_logo; ?>" alt=""></a>
                    </li>
                    <li>
                        <a href="#"><img src="<?php echo $this->cool_stats_button; ?>" alt=""></a>
                    </li>
                    <li>
                        <a class="last" href="#"><img src="<?php echo $this->seven_character_speedrun_button; ?>" alt=""></a>
                    </li>
                </ul>
            </div></div>
        </nav>        
        <div class="container">
            <img src="<?php echo $this->menu_bar; ?>">    
            <div class="site-index">        
                <?php echo $this->content; ?>
            </div>                
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <p class="pull-left">Created by <a href="http://www.twitch.tv/wilarseny" target="_blank">Wilarseny</a> and <a href="http://www.twitch.tv/squega" target="_blank">Squega</a>. Website name by <a href="http://www.twitch.tv/ratracing" target="_blank">RatRacing</a>.</p>
            <p class="pull-right">Powered by <a href="http://www.yiiframework.com/" rel="external">Yii Framework</a></p>
        </div>
    </footer>
    <?php echo $this->javascript; ?>
</body>
</html>