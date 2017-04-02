function process_info_data(data, table) {
    var processed_data = [];
    
    
    if(data['steamid'] != null) {
        var beampro_info = Formatting.getBeamproFancyLink(data.linked.beampro.username);
        var discord_info = Formatting.getDiscordFancyLink(data.linked.discord.username, data.linked.discord.discriminator);
        var reddit_info = Formatting.getRedditFancyLink(data.linked.reddit.username);
        var twitch_info = Formatting.getTwitchFancyLink(data.linked.twitch.username);
        var twitter_info = Formatting.getTwitterFancyLink(data.linked.twitter.nickname);
        var youtube_info = Formatting.getYoutubeFancyLink(data.linked.youtube.username);
        
        if(NecroTable.user_api_key != null) {
            if(beampro_info == null) {
                beampro_info = Formatting.getBeamproLoginLink();
            }
            
            if(discord_info == null) {
                discord_info = Formatting.getDiscordLoginLink();
            }
            
            if(reddit_info == null) {
                reddit_info = Formatting.getRedditLoginLink();
            }
            
            if(twitch_info == null) {
                twitch_info = Formatting.getTwitchLoginLink();
            }
            
            if(twitter_info == null) {
                twitter_info = Formatting.getTwitterLoginLink();
            }
            
            if(youtube_info == null) {
                youtube_info = Formatting.getYoutubeLoginLink();
            }
        }
        
        processed_data.push([
            Formatting.getNecrolabUserLink(data.steamid, data.personaname),            
            Formatting.getSteamFancyLink(data.linked.steam.personaname, data.linked.steam.profile_url),
            beampro_info,
            discord_info,
            reddit_info,
            twitch_info,
            twitter_info,
            youtube_info
        ]);
    }
    
    return processed_data;
};

function initialize_info_table() {
    var table = new NecroTable($('#player_info_table'));
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player'));
    
    table.addRequestParameter('id', 'steamid');
    
    table.addColumns([
        {
            name: 'personaname',
            title: 'Player',
            type: 'string'
        },
        {
            name: 'steamid',
            title: 'Steam',
            type: 'string',
            orderable: false
        },
        {
            name: 'beampro_username',
            title: 'Beam.pro',
            type: 'string'
        },
        {
            name: 'discord_username',
            title: 'Discord',
            type: 'string'
        },
        {
            name: 'reddit_username',
            title: 'Reddit',
            type: 'string'
        },
        {
            name: 'twitch_username',
            title: 'Twitch',
            type: 'string'
        },
        {
            name: 'twitter_username',
            title: 'Twitter',
            type: 'string'
        },
        {
            name: 'youtube_username',
            title: 'Youtube',
            type: 'string'
        }
    ]);
    
    table.setDataProcessCallback(window, 'process_info_data');
    
    table.render();
};

function process_power_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [];
                
                processed_data.push([
                    null,
                    row_data.date,
                    row_data.mode.display_name,
                    'Ranks',
                    row_data.rank,
                    row_data.score.rank,
                    row_data.speed.rank,
                    row_data.deathless.rank
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    'Points',
                    Formatting.roundNumber(row_data.total_points),
                    Formatting.roundNumber(row_data.score.rank_points),
                    Formatting.roundNumber(row_data.speed.rank_points),
                    Formatting.roundNumber(row_data.deathless.rank_points)
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    'Score/Time/Wins',
                    null,
                    row_data.score.total_score,
                    Formatting.convertSecondsToTime(row_data.speed.total_time),
                    row_data.deathless.total_win_count
                ]);
            }
        }
    }
    
    return processed_data;
};

function initialize_power_table() {
    var table = new NecroTable($('#player_power_rankings_table'));

    table.enableButtons();
    table.enablePaging();
    table.setDefaultLimit(30);
    table.enableSort('date', 'desc');
    table.enableReleaseField();
    table.enableDateRangeFields();
    table.enableCollapsibleRows(2);    
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/rankings/power/entries'));
    
    table.addRequestParameter('id', 'steamid');
    
    table.addColumns([
        {
            name: 'date',
            title: 'Date',
            type: 'string'
        },
        {
            name: 'mode',
            title: 'Mode',
            type: 'string',
            orderable: false
        },
        {
            name: 'type',
            title: '&nbsp;',
            type: 'string',
            orderable: false
        },
        {
            name: 'rank',
            title: 'Overall',
            type: 'num-fmt'
        },
        {
            name: 'score_rank',
            title: 'Score',
            type: 'num-fmt'
        },
        {
            name: 'speed_rank',
            title: 'Speed',
            type: 'num-fmt'
        },
        {
            name: 'deathless_rank',
            title: 'Deathless',
            type: 'num-fmt'
        }
    ]);
    
    table.setDataProcessCallback(window, 'process_power_data');
    
    table.render();
};

function process_score_ranking_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                processed_data.push([
                    null,
                    row_data.date,
                    row_data.mode.display_name,
                    'Ranks',
                    row_data.score.rank,
                    row_data.cadence.score.rank,
                    row_data.bard.score.rank,
                    row_data.monk.score.rank,
                    row_data.aria.score.rank,
                    row_data.bolt.score.rank,
                    row_data.dove.score.rank,
                    row_data.eli.score.rank,
                    row_data.melody.score.rank,
                    row_data.dorian.score.rank,
                    row_data.coda.score.rank,
                    row_data.nocturna.score.rank,
                    row_data.diamond.score.rank,
                    row_data.all.score.rank,
                    row_data.story.score.rank
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    'Points',
                    Formatting.roundNumber(row_data.score.rank_points),
                    Formatting.roundNumber(row_data.cadence.score.rank_points),
                    Formatting.roundNumber(row_data.bard.score.rank_points),
                    Formatting.roundNumber(row_data.monk.score.rank_points),
                    Formatting.roundNumber(row_data.aria.score.rank_points),
                    Formatting.roundNumber(row_data.bolt.score.rank_points),
                    Formatting.roundNumber(row_data.dove.score.rank_points),
                    Formatting.roundNumber(row_data.eli.score.rank_points),
                    Formatting.roundNumber(row_data.melody.score.rank_points),
                    Formatting.roundNumber(row_data.dorian.score.rank_points),
                    Formatting.roundNumber(row_data.coda.score.rank_points),
                    Formatting.roundNumber(row_data.nocturna.score.rank_points),
                    Formatting.roundNumber(row_data.diamond.score.rank_points),
                    Formatting.roundNumber(row_data.all.score.rank_points),
                    Formatting.roundNumber(row_data.story.score.rank_points)
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    'Scores',
                    row_data.score.total_score,
                    row_data.cadence.score.score,
                    row_data.bard.score.score,
                    row_data.monk.score.score,
                    row_data.aria.score.score,
                    row_data.bolt.score.score,
                    row_data.dove.score.score,
                    row_data.eli.score.score,
                    row_data.melody.score.score,
                    row_data.dorian.score.score,
                    row_data.coda.score.score,
                    row_data.nocturna.score.score,
                    row_data.diamond.score.score,
                    row_data.all.score.score,
                    row_data.story.score.score
                ]);
            }
        }
    }
    
    return processed_data;
};

function initialize_ranking_score_table() {
    var table = new NecroTable($('#player_score_rankings_table'));

    table.enableButtons();
    table.enablePaging();
    table.setDefaultLimit(30);
    table.enableReleaseField();
    table.enableDateRangeFields();
    table.enableCollapsibleRows(2);
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/rankings/power/score/entries'));
    
    table.addRequestParameter('id', 'steamid');
    
    table.addColumns([        
        {
            name: 'date',
            title: 'Date',
            type: 'string'
        },
        {
            name: 'mode',
            title: 'Mode',
            type: 'string',
            orderable: false
        },
        {
            name: 'type',
            title: '&nbsp;',
            type: 'string',
            orderable: false
        },
        {
            name: 'score_rank',
            title: 'Overall',
            type: 'num-fmt'
        },
        {
            name: 'cadence_score_rank',
            title: Formatting.getCharacterImageHtml('cadence'),
            type: 'num-fmt'
        },
        {
            name: 'bard_score_rank',
            title: Formatting.getCharacterImageHtml('bard'),
            type: 'num-fmt'
        },
        {
            name: 'monk_score_rank',
            title: Formatting.getCharacterImageHtml('monk'),
            type: 'num-fmt'
        },
        {
            name: 'aria_score_rank',
            title: Formatting.getCharacterImageHtml('aria'),
            type: 'num-fmt'
        },
        {
            name: 'bolt_score_rank',
            title: Formatting.getCharacterImageHtml('bolt'),
            type: 'num-fmt'
        },
        {
            name: 'dove_score_rank',
            title: Formatting.getCharacterImageHtml('dove'),
            type: 'num-fmt'
        },
        {
            name: 'eli_score_rank',
            title: Formatting.getCharacterImageHtml('eli'),
            type: 'num-fmt'
        },
        {
            name: 'melody_score_rank',
            title: Formatting.getCharacterImageHtml('melody'),
            type: 'num-fmt'
        },
        {
            name: 'dorian_score_rank',
            title: Formatting.getCharacterImageHtml('dorian'),
            type: 'num-fmt'
        },
        {
            name: 'coda_score_rank',
            title: Formatting.getCharacterImageHtml('coda'),
            type: 'num-fmt'
        },
        {
            name: 'nocturna_score_rank',
            title: Formatting.getCharacterImageHtml('nocturna'),
            type: 'num-fmt'
        },
        {
            name: 'diamond_score_rank',
            title: Formatting.getCharacterImageHtml('diamond'),
            type: 'num-fmt'
        },
        {
            name: 'all_score_rank',
            title: 'All',
            type: 'num-fmt'
        },
        {
            name: 'story_score_rank',
            title: 'Story',
            type: 'num-fmt'
        }
    ]);
    
    table.enableSort('date', 'desc');
    
    table.setDataProcessCallback(window, 'process_score_ranking_data');
    
    table.render();
};

function process_ranking_speed_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [];
                
                processed_data.push([
                    null,
                    row_data.date,
                    row_data.mode.display_name,
                    'Ranks',
                    row_data.speed.rank,
                    row_data.cadence.speed.rank,
                    row_data.bard.speed.rank,
                    row_data.monk.speed.rank,
                    row_data.aria.speed.rank,
                    row_data.bolt.speed.rank,
                    row_data.dove.speed.rank,
                    row_data.eli.speed.rank,
                    row_data.melody.speed.rank,
                    row_data.dorian.speed.rank,
                    row_data.coda.speed.rank,
                    row_data.nocturna.speed.rank,
                    row_data.diamond.speed.rank,
                    row_data.all.speed.rank,
                    row_data.story.speed.rank
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    'Points',
                    Formatting.roundNumber(row_data.speed.rank_points),
                    Formatting.roundNumber(row_data.cadence.speed.rank_points),
                    Formatting.roundNumber(row_data.bard.speed.rank_points),
                    Formatting.roundNumber(row_data.monk.speed.rank_points),
                    Formatting.roundNumber(row_data.aria.speed.rank_points),
                    Formatting.roundNumber(row_data.bolt.speed.rank_points),
                    Formatting.roundNumber(row_data.dove.speed.rank_points),
                    Formatting.roundNumber(row_data.eli.speed.rank_points),
                    Formatting.roundNumber(row_data.melody.speed.rank_points),
                    Formatting.roundNumber(row_data.dorian.speed.rank_points),
                    Formatting.roundNumber(row_data.coda.speed.rank_points),
                    Formatting.roundNumber(row_data.nocturna.speed.rank_points),
                    Formatting.roundNumber(row_data.diamond.speed.rank_points),
                    Formatting.roundNumber(row_data.all.speed.rank_points),
                    Formatting.roundNumber(row_data.story.speed.rank_points)
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    'Times',
                    Formatting.convertSecondsToTime(row_data.speed.total_time),
                    Formatting.convertSecondsToTime(row_data.cadence.speed.time),
                    Formatting.convertSecondsToTime(row_data.bard.speed.time),
                    Formatting.convertSecondsToTime(row_data.monk.speed.time),
                    Formatting.convertSecondsToTime(row_data.aria.speed.time),
                    Formatting.convertSecondsToTime(row_data.bolt.speed.time),
                    Formatting.convertSecondsToTime(row_data.dove.speed.time),
                    Formatting.convertSecondsToTime(row_data.eli.speed.time),
                    Formatting.convertSecondsToTime(row_data.melody.speed.time),
                    Formatting.convertSecondsToTime(row_data.dorian.speed.time),
                    Formatting.convertSecondsToTime(row_data.coda.speed.time),
                    Formatting.convertSecondsToTime(row_data.nocturna.speed.time),
                    Formatting.convertSecondsToTime(row_data.diamond.speed.time),
                    Formatting.convertSecondsToTime(row_data.all.speed.time),
                    Formatting.convertSecondsToTime(row_data.story.speed.time)
                ]);
            }
        }
    }
    
    return processed_data;
};

function initialize_ranking_speed_table() {
    var table = new NecroTable($('#player_speed_rankings_table'));
    
    table.enableButtons();
    table.enablePaging();
    table.setDefaultLimit(30);
    table.enableReleaseField();
    table.enableDateRangeFields();
    table.enableCollapsibleRows(2);
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/rankings/power/speed/entries'));
    
    table.addRequestParameter('id', 'steamid');
    
    table.addColumns([
        {
            name: 'date',
            title: 'Date',
            type: 'string'
        },
        {
            name: 'mode',
            title: 'Mode',
            type: 'string',
            orderable: false
        },
        {
            name: 'type',
            title: '&nbsp;',
            type: 'string',
            orderable: false
        },
        {
            name: 'speed_rank',
            title: 'Overall',
            type: 'num-fmt'
        },
        {
            name: 'cadence_speed_rank',
            title: Formatting.getCharacterImageHtml('cadence'),
            type: 'num-fmt'
        },
        {
            name: 'bard_speed_rank',
            title: Formatting.getCharacterImageHtml('bard'),
            type: 'num-fmt'
        },
        {
            name: 'monk_speed_rank',
            title: Formatting.getCharacterImageHtml('monk'),
            type: 'num-fmt'
        },
        {
            name: 'aria_speed_rank',
            title: Formatting.getCharacterImageHtml('aria'),
            type: 'num-fmt'
        },
        {
            name: 'bolt_speed_rank',
            title: Formatting.getCharacterImageHtml('bolt'),
            type: 'num-fmt'
        },
        {
            name: 'dove_speed_rank',
            title: Formatting.getCharacterImageHtml('dove'),
            type: 'num-fmt'
        },
        {
            name: 'eli_speed_rank',
            title: Formatting.getCharacterImageHtml('eli'),
            type: 'num-fmt'
        },
        {
            name: 'melody_speed_rank',
            title: Formatting.getCharacterImageHtml('melody'),
            type: 'num-fmt'
        },
        {
            name: 'dorian_speed_rank',
            title: Formatting.getCharacterImageHtml('dorian'),
            type: 'num-fmt'
        },
        {
            name: 'coda_speed_rank',
            title: Formatting.getCharacterImageHtml('coda'),
            type: 'num-fmt'
        },
        {
            name: 'nocturna_speed_rank',
            title: Formatting.getCharacterImageHtml('nocturna'),
            type: 'num-fmt'
        },
        {
            name: 'diamond_speed_rank',
            title: Formatting.getCharacterImageHtml('diamond'),
            type: 'num-fmt'
        },
        {
            name: 'all_speed_rank',
            title: 'All',
            type: 'num-fmt'
        },
        {
            name: 'story_speed_rank',
            title: 'Story',
            type: 'num-fmt'
        }
    ]);
    
    table.enableSort('date', 'desc');
    
    table.setDataProcessCallback(window, 'process_ranking_speed_data');
    
    table.render();
};

function process_ranking_deathless_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [];
                
                processed_data.push([
                    null,
                    row_data.date,
                    row_data.mode.display_name,
                    'Ranks',
                    row_data.deathless.rank,
                    row_data.cadence.deathless.rank,
                    row_data.bard.deathless.rank,
                    row_data.monk.deathless.rank,
                    row_data.aria.deathless.rank,
                    row_data.bolt.deathless.rank,
                    row_data.dove.deathless.rank,
                    row_data.eli.deathless.rank,
                    row_data.melody.deathless.rank,
                    row_data.dorian.deathless.rank,
                    row_data.coda.deathless.rank,
                    row_data.nocturna.deathless.rank,
                    row_data.diamond.deathless.rank
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    'Points',
                    Formatting.roundNumber(row_data.deathless.rank_points),
                    Formatting.roundNumber(row_data.cadence.deathless.rank_points),
                    Formatting.roundNumber(row_data.bard.deathless.rank_points),
                    Formatting.roundNumber(row_data.monk.deathless.rank_points),
                    Formatting.roundNumber(row_data.aria.deathless.rank_points),
                    Formatting.roundNumber(row_data.bolt.deathless.rank_points),
                    Formatting.roundNumber(row_data.dove.deathless.rank_points),
                    Formatting.roundNumber(row_data.eli.deathless.rank_points),
                    Formatting.roundNumber(row_data.melody.deathless.rank_points),
                    Formatting.roundNumber(row_data.dorian.deathless.rank_points),
                    Formatting.roundNumber(row_data.coda.deathless.rank_points),
                    Formatting.roundNumber(row_data.nocturna.deathless.rank_points),
                    Formatting.roundNumber(row_data.diamond.deathless.rank_points)
                ]);
                
                processed_data.push([
                    '&nbsp;',
                    null,
                    null,
                    'Wins',
                    row_data.deathless.total_win_count,
                    row_data.cadence.deathless.win_count,
                    row_data.bard.deathless.win_count,
                    row_data.monk.deathless.win_count,
                    row_data.aria.deathless.win_count,
                    row_data.bolt.deathless.win_count,
                    row_data.dove.deathless.win_count,
                    row_data.eli.deathless.win_count,
                    row_data.melody.deathless.win_count,
                    row_data.dorian.deathless.win_count,
                    row_data.coda.deathless.win_count,
                    row_data.nocturna.deathless.win_count,
                    row_data.diamond.deathless.win_count
                ]);
            }
        }
    }
    
    return processed_data;
};

function initialize_ranking_deathless_table() {
    var table = new NecroTable($('#player_deathless_rankings_table'));
    
    table.enableButtons();
    table.enablePaging();
    table.setDefaultLimit(30);
    table.enableReleaseField();
    table.enableDateRangeFields();
    table.enableCollapsibleRows(2);
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/rankings/power/deathless/entries'));
    
    table.addRequestParameter('id', 'steamid');
    
    table.addColumns([
        {
            name: 'date',
            title: 'Date',
            type: 'string'
        },
        {
            name: 'mode',
            title: 'Mode',
            type: 'string',
            orderable: false
        },
        {
            name: 'type',
            title: '&nbsp;',
            type: 'string',
            orderable: false
        },
        {
            name: 'deathless_rank',
            title: 'Overall',
            type: 'num-fmt'
        },
        {
            name: 'cadence_deathless_rank',
            title: Formatting.getCharacterImageHtml('cadence'),
            type: 'num-fmt'
        },
        {
            name: 'bard_deathless_rank',
            title: Formatting.getCharacterImageHtml('bard'),
            type: 'num-fmt'
        },
        {
            name: 'monk_deathless_rank',
            title: Formatting.getCharacterImageHtml('monk'),
            type: 'num-fmt'
        },
        {
            name: 'aria_deathless_rank',
            title: Formatting.getCharacterImageHtml('aria'),
            type: 'num-fmt'
        },
        {
            name: 'bolt_deathless_rank',
            title: Formatting.getCharacterImageHtml('bolt'),
            type: 'num-fmt'
        },
        {
            name: 'dove_deathless_rank',
            title: Formatting.getCharacterImageHtml('dove'),
            type: 'num-fmt'
        },
        {
            name: 'eli_deathless_rank',
            title: Formatting.getCharacterImageHtml('eli'),
            type: 'num-fmt'
        },
        {
            name: 'melody_deathless_rank',
            title: Formatting.getCharacterImageHtml('melody'),
            type: 'num-fmt'
        },
        {
            name: 'dorian_deathless_rank',
            title: Formatting.getCharacterImageHtml('dorian'),
            type: 'num-fmt'
        },
        {
            name: 'coda_deathless_rank',
            title: Formatting.getCharacterImageHtml('coda'),
            type: 'num-fmt'
        },
        {
            name: 'nocturna_deathless_rank',
            title: Formatting.getCharacterImageHtml('nocturna'),
            type: 'num-fmt'
        },
        {
            name: 'diamond_deathless_rank',
            title: Formatting.getCharacterImageHtml('diamond'),
            type: 'num-fmt'
        }
    ]);
    
    table.enableSort('date', 'desc');
    
    table.setDataProcessCallback(window, 'process_ranking_deathless_data');
    
    table.render();
};

function process_ranking_character_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            var character_name = table.getCharacterFieldValue();
            
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var character_rankings = row_data[character_name];
                
                var rank_row = [
                    null,
                    row_data.date,
                    row_data.mode.display_name,
                    'Ranks',
                    row_data.rank,
                    character_rankings.score.rank,
                    character_rankings.speed.rank,
                ];
                
                var points_row = [
                    '&nbsp;',
                    null,
                    null,
                    'Points',
                    Formatting.roundNumber(character_rankings.rank_points),
                    Formatting.roundNumber(character_rankings.score.rank_points),
                    Formatting.roundNumber(character_rankings.speed.rank_points)
                ];
                
                var score_row = [
                    '&nbsp;',
                    null,
                    null,
                    'Score/Time/Wins',
                    null,
                    character_rankings.score.score,
                    Formatting.convertSecondsToTime(character_rankings.speed.time)
                ];
                
                switch(character_name) {
                    case 'all':
                    case 'story':
                        rank_row.push(null);
                        points_row.push(null);
                        score_row.push(null);
                        break;
                    default:
                        rank_row.push(character_rankings.deathless.rank);
                        points_row.push(Formatting.roundNumber(character_rankings.deathless.rank_points));
                        score_row.push(character_rankings.deathless.win_count);
                        break;
                }
                
                processed_data.push(rank_row);
                processed_data.push(points_row);
                processed_data.push(score_row);
            }
        }
    }
    
    return processed_data;
};

function initialize_ranking_character_table() {
    var table = new NecroTable($('#player_character_rankings_table'));
    
    table.enableButtons();
    table.enablePaging();
    table.setDefaultLimit(30);
    table.enableCharacterField();
    table.enableReleaseField();
    table.enableDateRangeFields();
    table.enableCollapsibleRows(2);
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/rankings/power/character/entries'));
    
    table.addRequestParameter('id', 'steamid');
    
    table.addColumns([
        {
            name: 'date',
            title: 'Date',
            type: 'string'
        },
        {
            name: 'mode',
            title: 'Mode',
            type: 'string',
            orderable: false
        },
        {
            name: 'type',
            title: '&nbsp;',
            type: 'string',
            orderable: false
        },
        {
            name: 'rank',
            title: 'Overall',
            type: 'num-fmt'
        },
        {
            name: 'score_rank',
            title: 'Score',
            type: 'num-fmt'
        },
        {
            name: 'speed_rank',
            title: 'Speed',
            type: 'num-fmt'
        },
        {
            name: 'deathless_rank',
            title: 'Deathless',
            type: 'num-fmt'
        }
    ]);
    
    table.setDataProcessCallback(window, 'process_ranking_character_data');
    
    table.render();
};

function process_ranking_daily_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                processed_data.push([
                    row_data.date,
                    row_data.mode.display_name,
                    row_data.rank,
                    row_data.first_place_ranks,
                    row_data.top_5_ranks,
                    row_data.top_10_ranks,
                    row_data.top_20_ranks,
                    row_data.top_50_ranks,
                    row_data.top_100_ranks,
                    row_data.total_score,
                    Formatting.roundNumber(row_data.total_points),
                    Formatting.roundNumber(row_data.points_per_day),
                    row_data.total_dailies,
                    row_data.total_wins,
                    Formatting.roundNumber(row_data.average_rank),
                    row_data.sum_of_ranks
                ]);
            }
        }
    }
    
    return processed_data;
};

function initialize_ranking_daily_table() {
    var table = new NecroTable($('#player_daily_rankings_table'));
    
    table.enableButtons();
    table.enablePaging();
    table.setDefaultLimit(30);
    table.enableReleaseField();
    table.enableDateRangeFields();
    table.enableNumberOfDaysField();
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/rankings/daily/entries'));
    
    table.addRequestParameter('id', 'steamid');
    
    table.addColumns([
        {
            name: 'date',
            title: 'Date',
            type: 'string'
        },
        {
            name: 'mode',
            title: 'Mode',
            type: 'string',
            orderable: false
        },
        {
            name: 'rank',
            title: 'Rank',
            type: 'num-fmt'
        },
        {
            name: 'first_place_ranks',
            title: '1st<br />Place',
            type: 'num-fmt'
        },
        {
            name: 'top_5_ranks',
            title: 'Top<br />5',
            type: 'num-fmt'
        },
        {
            name: 'top_10_ranks',
            title: 'Top<br />10',
            type: 'num-fmt'
        },
        {
            name: 'top_20_ranks',
            title: 'Top<br />20',
            type: 'num-fmt'
        },
        {
            name: 'top_50_ranks',
            title: 'Top<br />50',
            type: 'num-fmt'
        },
        {
            name: 'top_100_ranks',
            title: 'Top<br />100',
            type: 'num-fmt'
        },
        {
            name: 'total_score',
            title: 'Total<br />Score',
            type: 'num-fmt'
        },
        {
            name: 'total_points',
            title: 'Points',
            type: 'num-fmt'
        },
        {
            name: 'points_per_day',
            title: 'Points<br />Per<br />Day',
            type: 'num-fmt'
        },
        {
            name: 'total_dailies',
            title: 'Attempts',
            type: 'num-fmt'
        },
        {
            name: 'total_wins',
            title: 'Wins',
            type: 'num-fmt'
        },
        {
            name: 'average_rank',
            title: 'Average<br />Rank',
            type: 'num-fmt'
        }
    ]);
    
    table.enableSort('date', 'desc');
    
    table.setDataProcessCallback(window, 'process_ranking_daily_data');
    
    table.render();
};

function process_leaderboard_score_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [
                    Formatting.getLeaderboardEntriesTitle(row_data.leaderboard),
                    row_data.entry.rank,
                    row_data.entry.score,
                    row_data.entry.zone,
                    row_data.entry.level,
                    row_data.entry.win,
                    row_data.entry.replay.run_result,
                    row_data.entry.replay.seed,
                    Formatting.getReplayFileHtml(row_data.entry.replay.file_url)
                ];
                
                processed_data.push(processed_row);
            }
        }
    }
    
    return processed_data;
};

function initialize_leaderboard_score_table() {
    var table = new NecroTable($('#player_score_leaderboards_table'));

    table.enableButtons();
    table.enablePaging();
    table.setDefaultLimit(30);
    table.enableSort('leaderboard_name', 'asc');
    table.enableReleaseField();
    table.enableDateField();
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/leaderboards/score/entries'));
    
    table.addRequestParameter('id', 'steamid');

    table.addColumns([
        {
            name: 'leaderboard_name',
            title: 'Leaderboard',
            type: 'string'
        },
        {
            name: 'rank',
            title: 'Rank',
            type: 'num-fmt'
        },
        {
            name: 'score',
            title: 'Score',
            type: 'num-fmt'
        },
        {
            name: 'zone',
            title: 'Zone',
            type: 'num-fmt'
        },
        {
            name: 'level',
            title: 'Level',
            type: 'num-fmt'
        },
        {
            name: 'win',
            title: 'Win',
            type: 'string',
            orderable: false
        },
        {
            name: 'run_result',
            title: 'Killed By',
            type: 'string'
        },
        {
            name: 'seed',
            title: 'Seed',
            type: 'string'
        },
        {
            name: 'replay',
            title: 'Replay',
            type: 'string',
            orderable: false
        }
    ]);
    
    table.setDataProcessCallback(window, 'process_leaderboard_score_data');
    
    table.render();
};

function process_leaderboard_speed_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [
                    Formatting.getLeaderboardEntriesTitle(row_data.leaderboard),
                    row_data.entry.rank,
                    Formatting.convertSecondsToTime(row_data.entry.time),
                    row_data.entry.replay.seed,
                    Formatting.getReplayFileHtml(row_data.entry.replay.file_url)
                ];
                
                processed_data.push(processed_row);
            }
        }
    }
    
    return processed_data;
};

function initialize_leaderboard_speed_table() {
    var table = new NecroTable($('#player_speed_leaderboards_table'));

    table.enableButtons();
    table.enablePaging();
    table.setDefaultLimit(30);
    table.enableSort('leaderboard_name', 'asc');
    table.enableReleaseField();
    table.enableDateField();
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/leaderboards/speed/entries'));
    
    table.addRequestParameter('id', 'steamid');

    table.addColumns([
        {
            name: 'leaderboard_name',
            title: 'Leaderboard',
            type: 'string'
        },
        {
            name: 'rank',
            title: 'Rank',
            type: 'num-fmt'
        },
        {
            name: 'time',
            title: 'Time',
            type: 'num-fmt'
        },
        {
            name: 'seed',
            title: 'Seed',
            type: 'string'
        },
        {
            name: 'replay',
            title: 'Replay',
            type: 'string',
            orderable: false
        }
    ]);
    
    table.setDataProcessCallback(window, 'process_leaderboard_speed_data');
    
    table.render();
};

function process_leaderboard_deathless_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [
                    Formatting.getLeaderboardEntriesTitle(row_data.leaderboard),
                    row_data.entry.rank,
                    row_data.entry.win_count,
                    row_data.entry.zone,
                    row_data.entry.level,
                    row_data.entry.win,
                    row_data.entry.replay.run_result
                ];
                
                processed_data.push(processed_row);
            }
        }
    }
    
    return processed_data;
};

function initialize_leaderboard_deathless_table() {
    var table = new NecroTable($('#player_deathless_leaderboards_table'));

    table.enableButtons();
    table.enablePaging();
    table.setDefaultLimit(30);
    table.enableSort('leaderboard_name', 'asc');
    table.enableReleaseField();
    table.enableDateField();
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/leaderboards/deathless/entries'));
    
    table.addRequestParameter('id', 'steamid');

    table.addColumns([
        {
            name: 'leaderboard_name',
            title: 'Leaderboard',
            type: 'string'
        },
        {
            name: 'rank',
            title: 'Rank',
            type: 'num-fmt'
        },
        {
            name: 'win_count',
            title: 'Wins',
            type: 'num-fmt'
        },
        {
            name: 'zone',
            title: 'Zone',
            type: 'num-fmt'
        },
        {
            name: 'level',
            title: 'Level',
            type: 'num-fmt'
        },
        {
            name: 'win',
            title: 'Win',
            type: 'string',
            orderable: false
        },
        {
            name: 'run_result',
            title: 'Killed By',
            type: 'string'
        }
    ]);
    
    table.setDataProcessCallback(window, 'process_leaderboard_deathless_data');
    
    table.render();
};

function process_leaderboard_daily_data(data, table) {
    var processed_data = [];
    
    if(data.length != null) {
        var data_length = data.length;
        
        if(data_length > 0) {
            for(var index = 0; index < data_length; index++) {
                var row_data = data[index];
                
                var processed_row = [
                    row_data.leaderboard.daily_date,
                    row_data.entry.rank,
                    row_data.entry.score,
                    row_data.entry.zone,
                    row_data.entry.level,
                    row_data.entry.win,
                    row_data.entry.replay.run_result,
                    row_data.entry.replay.seed,
                    Formatting.getReplayFileHtml(row_data.entry.replay.file_url)
                ];
                
                processed_data.push(processed_row);
            }
        }
    }
    
    return processed_data;
};

function initialize_leaderboard_daily_table() {
    var table = new NecroTable($('#player_daily_leaderboards_table'));

    table.enableButtons();
    table.enablePaging();
    table.setDefaultLimit(30);
    table.enableSort('daily_date', 'desc');
    table.enableReleaseField();
    table.enableDateRangeFields();
    
    table.setAjaxUrl(Formatting.getNecrolabApiUrl('/players/player/leaderboards/daily/entries'));
    
    table.addRequestParameter('id', 'steamid');
    
    table.addColumns([
        {
            name: 'daily_date',
            title: 'Date',
            type: 'string'
        },
        {
            name: 'rank',
            title: 'Rank',
            type: 'num-fmt'
        },
        {
            name: 'score',
            title: 'Score',
            type: 'num-fmt'
        },
        {
            name: 'zone',
            title: 'Zone',
            type: 'num-fmt'
        },
        {
            name: 'level',
            title: 'Level',
            type: 'num-fmt'
        },
        {
            name: 'win',
            title: 'Win',
            type: 'string',
            orderable: false
        },
        {
            name: 'run_result',
            title: 'Killed By',
            type: 'string'
        },
        {
            name: 'seed',
            title: 'Seed',
            type: 'string'
        },
        {
            name: 'replay',
            title: 'Replay',
            type: 'string',
            orderable: false
        }
    ]);
    
    table.setDataProcessCallback(window, 'process_leaderboard_daily_data');
    
    table.render();
};

$(document).ready(function() {
    initialize_info_table();
    
    initialize_power_table();
    
    initialize_ranking_score_table();
    
    initialize_ranking_speed_table();
    
    initialize_ranking_deathless_table();
    
    initialize_ranking_character_table();
    
    initialize_ranking_daily_table();
    
    initialize_leaderboard_score_table();
    
    initialize_leaderboard_speed_table();
    
    initialize_leaderboard_deathless_table();
    
    initialize_leaderboard_daily_table();
});