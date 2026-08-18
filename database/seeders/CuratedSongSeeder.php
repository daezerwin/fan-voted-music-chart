<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CuratedSongSeeder extends Seeder
{
    /**
     * Official music videos for the artists in CuratedArtistSeeder. Every
     * `youtube` id below was checked against YouTube's oEmbed endpoint to
     * confirm it's a genuine official-channel upload — not a lyric video,
     * fan reupload, or audio-only track. `date` defaults to January 1st
     * where only a release year (not a full date) could be confirmed. No
     * Faker dependency, so this is safe to run in production.
     *
     * @var array<string, list<array{title: string, date: string, youtube: string, genre: string}>>
     */
    public const SONGS = [
        'Hale' => [
            ['title' => 'The Day You Said Goodnight', 'date' => '2005-01-01', 'youtube' => 'DbIY9NSho8Y', 'genre' => 'Rock'],
            ['title' => 'Broken Sonnet', 'date' => '2005-01-01', 'youtube' => 'WurY8WdS8Fw', 'genre' => 'Rock'],
            ['title' => 'Kung Wala Ka', 'date' => '2005-01-01', 'youtube' => '7yZu1o-heb0', 'genre' => 'Rock'],
            ['title' => 'Kahit Pa', 'date' => '2005-01-01', 'youtube' => '5i-3aFRBVhE', 'genre' => 'Rock'],
            ['title' => 'Blue Sky', 'date' => '2006-01-01', 'youtube' => 'wk0_Twxy_14', 'genre' => 'Rock'],
            ['title' => 'Bahay Kubo', 'date' => '2009-01-01', 'youtube' => 'Wq0wT-Lmes8', 'genre' => 'Rock'],
            ['title' => 'Sandali Na Lang', 'date' => '2009-01-01', 'youtube' => 'G1q0aVtf-Lg', 'genre' => 'Rock'],
            ['title' => 'Kalesa', 'date' => '2009-11-24', 'youtube' => 'urmZSYWhgp8', 'genre' => 'Rock'],
            ['title' => 'See You', 'date' => '2015-01-27', 'youtube' => 'ZVzFj3XT2z8', 'genre' => 'Rock'],
            ['title' => 'Saint or Sinner', 'date' => '2015-01-01', 'youtube' => 'p14LYZJkXDA', 'genre' => 'Rock'],
            ['title' => 'You or Nothing', 'date' => '2016-01-01', 'youtube' => 'OxTfxR-HAKo', 'genre' => 'Rock'],
            ['title' => 'Alon', 'date' => '2018-01-01', 'youtube' => '1XY8lzbgtPM', 'genre' => 'Rock'],
        ],
        'Cueshe' => [
            ['title' => 'Stay', 'date' => '2005-01-01', 'youtube' => '17Sv2FAZnkw', 'genre' => 'Rock'],
            ['title' => 'Ulan', 'date' => '2005-01-01', 'youtube' => 'HP12yvlVNs8', 'genre' => 'Rock'],
            ['title' => 'Sorry', 'date' => '2005-01-01', 'youtube' => 'K3V2_BxrT7c', 'genre' => 'Rock'],
            ['title' => "Can't Let You Go", 'date' => '2006-01-01', 'youtube' => '9E2CRgMb6sg', 'genre' => 'Rock'],
            ['title' => '24 Hours', 'date' => '2006-01-01', 'youtube' => 'XxEnkvBs2fQ', 'genre' => 'Rock'],
            ['title' => 'Back to Me', 'date' => '2006-01-01', 'youtube' => 'o3SJqONz5qs', 'genre' => 'Rock'],
            ['title' => 'Borrowed Time', 'date' => '2006-01-01', 'youtube' => 'riz0pWM80ns', 'genre' => 'Rock'],
            ['title' => 'Bakit?', 'date' => '2007-01-01', 'youtube' => '60u2NNs-u14', 'genre' => 'Rock'],
            ['title' => 'Pasensiya Na', 'date' => '2007-01-01', 'youtube' => 'aDlc1zFK_ZY', 'genre' => 'Rock'],
        ],
        'Shamrock' => [
            ['title' => 'Alipin', 'date' => '2005-01-01', 'youtube' => 'uV8_00Pn_H8', 'genre' => 'Rock'],
            ['title' => 'Okay Lang', 'date' => '2005-01-01', 'youtube' => '-02_dXMXChY', 'genre' => 'Rock'],
            ['title' => 'Haplos', 'date' => '2007-01-01', 'youtube' => 'E4eeZSaNWCE', 'genre' => 'Rock'],
            ['title' => 'Paano', 'date' => '2007-01-01', 'youtube' => 'q8vxOfiy-fo', 'genre' => 'Rock'],
            ['title' => 'Pagkakataon', 'date' => '2010-01-01', 'youtube' => 'E2PYDp4e8IE', 'genre' => 'Rock'],
        ],
        'Maroon 5' => [
            ['title' => 'Harder to Breathe', 'date' => '2002-05-22', 'youtube' => 'rV8NHsmVMPE', 'genre' => 'Rock'],
            ['title' => 'This Love', 'date' => '2004-01-12', 'youtube' => 'XPpTgCho5ZA', 'genre' => 'Rock'],
            ['title' => 'She Will Be Loved', 'date' => '2004-07-27', 'youtube' => 'nIjVuRTm-dc', 'genre' => 'Rock'],
            ['title' => 'Makes Me Wonder', 'date' => '2007-03-27', 'youtube' => 'sAebYQgy4n4', 'genre' => 'Rock'],
            ['title' => 'Wake Up Call', 'date' => '2007-07-17', 'youtube' => 'dkQ0OJ5Byls', 'genre' => 'Rock'],
            ['title' => 'Misery', 'date' => '2010-06-22', 'youtube' => '6g6g2mvItp4', 'genre' => 'Rock'],
            ['title' => 'Moves Like Jagger', 'date' => '2011-06-21', 'youtube' => 'iEPTlhBmwRg', 'genre' => 'Pop'],
            ['title' => 'Payphone', 'date' => '2012-04-16', 'youtube' => 'KRaWnd3LJfs', 'genre' => 'Pop'],
            ['title' => 'One More Night', 'date' => '2012-06-19', 'youtube' => 'fwK7ggA3-bU', 'genre' => 'Pop'],
            ['title' => 'Daylight', 'date' => '2012-11-27', 'youtube' => 'N17FXwRWEZs', 'genre' => 'Pop'],
            ['title' => 'Love Somebody', 'date' => '2013-05-14', 'youtube' => 'MU8B4XDI3Uw', 'genre' => 'Pop'],
            ['title' => 'Maps', 'date' => '2014-06-16', 'youtube' => 'NmugSMBh_iI', 'genre' => 'Pop'],
            ['title' => 'Animals', 'date' => '2014-08-25', 'youtube' => 'qpgTC9MDx1o', 'genre' => 'Pop'],
            ['title' => 'Sugar', 'date' => '2015-01-13', 'youtube' => '09R8_2nJtjg', 'genre' => 'Pop'],
            ['title' => "Don't Wanna Know", 'date' => '2016-10-11', 'youtube' => 'ANS9sSJA9Yc', 'genre' => 'Pop'],
            ['title' => 'Cold', 'date' => '2017-02-14', 'youtube' => 'XatXy6ZhKZw', 'genre' => 'Pop'],
            ['title' => 'What Lovers Do', 'date' => '2017-08-30', 'youtube' => '5Wiio4KoGe8', 'genre' => 'Pop'],
            ['title' => 'Girls Like You', 'date' => '2018-05-31', 'youtube' => 'aJOTlE1K90k', 'genre' => 'Pop'],
            ['title' => 'Memories', 'date' => '2019-09-20', 'youtube' => 'SlPhMPnQ58k', 'genre' => 'Pop'],
            ['title' => 'Beautiful Mistakes', 'date' => '2021-03-03', 'youtube' => 'BSzSn-PRdtI', 'genre' => 'Pop'],
        ],
        'Backstreet Boys' => [
            ['title' => "We've Got It Goin' On", 'date' => '1995-09-11', 'youtube' => 'kHBXPoJhnHQ', 'genre' => 'Pop'],
            ['title' => "Get Down (You're the One for Me)", 'date' => '1996-04-30', 'youtube' => '0LSYuZRSGcY', 'genre' => 'Pop'],
            ['title' => 'Quit Playing Games (with My Heart)', 'date' => '1996-10-14', 'youtube' => 'Ug88HO2mg44', 'genre' => 'Pop'],
            ['title' => 'Anywhere for You', 'date' => '1997-02-24', 'youtube' => 'ci8MvjAxx3Y', 'genre' => 'Pop'],
            ['title' => "Everybody (Backstreet's Back)", 'date' => '1997-07-14', 'youtube' => '6M6samPEMpM', 'genre' => 'Pop'],
            ['title' => 'As Long As You Love Me', 'date' => '1997-10-07', 'youtube' => '0Gl2QnHNpkA', 'genre' => 'Pop'],
            ['title' => 'All I Have to Give', 'date' => '1998-01-13', 'youtube' => 'pj6FCKm8dhM', 'genre' => 'Pop'],
            ['title' => 'I Want It That Way', 'date' => '1999-04-12', 'youtube' => '4fndeDfaWCg', 'genre' => 'Pop'],
            ['title' => 'Larger Than Life', 'date' => '1999-09-07', 'youtube' => 'MEb2CecR11I', 'genre' => 'Pop'],
            ['title' => 'Show Me the Meaning of Being Lonely', 'date' => '1999-12-14', 'youtube' => 'aBt8fN7mJNg', 'genre' => 'Pop'],
            ['title' => 'Shape of My Heart', 'date' => '2000-10-02', 'youtube' => 'OT5msu-dap8', 'genre' => 'Pop'],
            ['title' => 'The Call', 'date' => '2001-02-06', 'youtube' => 'wMOkm57vu0k', 'genre' => 'Pop'],
            ['title' => 'More Than That', 'date' => '2001-04-17', 'youtube' => '1OwfYjemrYw', 'genre' => 'Pop'],
            ['title' => 'Drowning', 'date' => '2001-09-25', 'youtube' => 'GZXHBgjQjNM', 'genre' => 'Pop'],
            ['title' => 'Incomplete', 'date' => '2005-04-11', 'youtube' => 'WVe80iZtlYU', 'genre' => 'Pop'],
            ['title' => 'In a World Like This', 'date' => '2013-06-25', 'youtube' => 'ynBplqio1R4', 'genre' => 'Pop'],
            ['title' => "Don't Go Breaking My Heart", 'date' => '2018-05-17', 'youtube' => '6SuMbFuKDf8', 'genre' => 'Pop'],
            ['title' => 'Chances', 'date' => '2018-11-09', 'youtube' => 'W5kM5wAwRug', 'genre' => 'Pop'],
        ],
        'Westlife' => [
            ['title' => 'Swear It Again', 'date' => '1999-04-19', 'youtube' => '0UaMsIJ4bR0', 'genre' => 'Pop'],
            ['title' => 'If I Let You Go', 'date' => '1999-08-09', 'youtube' => '7NrQei36fJk', 'genre' => 'Pop'],
            ['title' => 'Flying Without Wings', 'date' => '1999-10-18', 'youtube' => 'vKPGxGCFgTs', 'genre' => 'Pop'],
            ['title' => 'Fool Again', 'date' => '2000-03-27', 'youtube' => 'H4BB9eGUEaE', 'genre' => 'Pop'],
            ['title' => 'My Love', 'date' => '2000-10-30', 'youtube' => 'ddSnl5PXVJI', 'genre' => 'Pop'],
            ['title' => 'Uptown Girl', 'date' => '2001-03-05', 'youtube' => '0HTexqxo1og', 'genre' => 'Pop'],
            ['title' => "When You're Looking Like That", 'date' => '2001-07-30', 'youtube' => '5rmKy8H62BU', 'genre' => 'Pop'],
            ['title' => 'Queen of My Heart', 'date' => '2001-11-05', 'youtube' => '6c64kUiqknY', 'genre' => 'Pop'],
            ['title' => 'World of Our Own', 'date' => '2002-02-18', 'youtube' => 'Jal-vzO8bVE', 'genre' => 'Pop'],
            ['title' => 'Unbreakable', 'date' => '2002-11-04', 'youtube' => 'azdesKO7r4Q', 'genre' => 'Pop'],
            ['title' => 'Mandy', 'date' => '2003-11-10', 'youtube' => '8ShlE-xobyw', 'genre' => 'Pop'],
            ['title' => 'You Raise Me Up', 'date' => '2005-10-24', 'youtube' => '9bxc9hbwkkw', 'genre' => 'Pop'],
            ['title' => 'Home', 'date' => '2007-10-29', 'youtube' => 'DuFHaVJpcr4', 'genre' => 'Pop'],
            ['title' => 'What About Now', 'date' => '2009-10-23', 'youtube' => 'Vc7Fn4Hk088', 'genre' => 'Pop'],
        ],
        'Katy Perry' => [
            ['title' => 'I Kissed a Girl', 'date' => '2008-04-28', 'youtube' => 'tAp9BKosZXs', 'genre' => 'Pop'],
            ['title' => 'Hot n Cold', 'date' => '2008-09-09', 'youtube' => 'kTHNpusq654', 'genre' => 'Pop'],
            ['title' => 'California Gurls', 'date' => '2010-05-07', 'youtube' => 'F57P9C4SAW4', 'genre' => 'Pop'],
            ['title' => 'Teenage Dream', 'date' => '2010-07-23', 'youtube' => '98WtmW-lfeE', 'genre' => 'Pop'],
            ['title' => 'Firework', 'date' => '2010-10-26', 'youtube' => 'QGJuMBdaqIw', 'genre' => 'Pop'],
            ['title' => 'E.T.', 'date' => '2011-02-11', 'youtube' => 't5Sd5c4o9UM', 'genre' => 'Pop'],
            ['title' => 'Last Friday Night (T.G.I.F.)', 'date' => '2011-06-06', 'youtube' => 'KlyXNRrsk4A', 'genre' => 'Pop'],
            ['title' => 'Wide Awake', 'date' => '2012-05-22', 'youtube' => 'k0BWlvnBmIE', 'genre' => 'Pop'],
            ['title' => 'Roar', 'date' => '2013-08-10', 'youtube' => 'CevxZvSJLk8', 'genre' => 'Pop'],
            ['title' => 'Dark Horse', 'date' => '2013-12-17', 'youtube' => '0KSOMA3QBU0', 'genre' => 'Pop'],
            ['title' => 'Birthday', 'date' => '2014-04-21', 'youtube' => 'CEUg7OplvIQ', 'genre' => 'Pop'],
            ['title' => 'This Is How We Do', 'date' => '2014-07-31', 'youtube' => '7RMQksXpQSk', 'genre' => 'Pop'],
            ['title' => 'Chained to the Rhythm', 'date' => '2017-02-21', 'youtube' => 'Um7pMggPnug', 'genre' => 'Pop'],
            ['title' => 'Bon Appétit', 'date' => '2017-04-28', 'youtube' => 'dPI-mRFEIH0', 'genre' => 'Pop'],
            ['title' => 'Swish Swish', 'date' => '2017-05-19', 'youtube' => 'iGk5fR-t5AU', 'genre' => 'Pop'],
            ['title' => 'Never Really Over', 'date' => '2019-05-31', 'youtube' => 'aEb5gNsmGJ8', 'genre' => 'Pop'],
        ],
        'Taylor Swift' => [
            ['title' => 'Love Story', 'date' => '2008-09-15', 'youtube' => '8xg3vE8Ie_E', 'genre' => 'Country'],
            ['title' => 'You Belong with Me', 'date' => '2009-04-20', 'youtube' => 'VuNIsY6JdUw', 'genre' => 'Country'],
            ['title' => 'Mean', 'date' => '2011-03-07', 'youtube' => 'jYa1eI1hpDE', 'genre' => 'Country'],
            ['title' => 'I Knew You Were Trouble', 'date' => '2012-10-09', 'youtube' => 'vNoKguSdy4Y', 'genre' => 'Pop'],
            ['title' => '22', 'date' => '2013-03-12', 'youtube' => 'AgFeZr5ptV8', 'genre' => 'Pop'],
            ['title' => 'Style', 'date' => '2015-02-09', 'youtube' => '-CmadmM5cOk', 'genre' => 'Pop'],
            ['title' => 'Shake It Off', 'date' => '2014-08-18', 'youtube' => 'nfWlot6h_JM', 'genre' => 'Pop'],
            ['title' => 'Blank Space', 'date' => '2014-11-10', 'youtube' => 'e-ORhEE9VVg', 'genre' => 'Pop'],
            ['title' => 'Bad Blood', 'date' => '2015-05-17', 'youtube' => 'QcIy9NiNbmo', 'genre' => 'Pop'],
            ['title' => 'Wildest Dreams', 'date' => '2015-08-31', 'youtube' => 'IdneKLhsWOQ', 'genre' => 'Pop'],
            ['title' => 'Look What You Made Me Do', 'date' => '2017-08-24', 'youtube' => '3tmd-ClpJxA', 'genre' => 'Pop'],
            ['title' => 'Delicate', 'date' => '2018-03-12', 'youtube' => 'tCXGJQYZ9JA', 'genre' => 'Pop'],
            ['title' => 'ME!', 'date' => '2019-04-26', 'youtube' => 'FuXNumBwDOM', 'genre' => 'Pop'],
            ['title' => 'Lover', 'date' => '2019-08-16', 'youtube' => '-BjZmE2gtdo', 'genre' => 'Pop'],
            ['title' => 'cardigan', 'date' => '2020-07-24', 'youtube' => 'K-a8s8OLBSE', 'genre' => 'Indie'],
            ['title' => 'willow', 'date' => '2020-12-11', 'youtube' => 'RsEZmictANA', 'genre' => 'Indie'],
            ['title' => 'Anti-Hero', 'date' => '2022-10-21', 'youtube' => 'b1kbLwvqugk', 'genre' => 'Pop'],
        ],
        'The Red Jumpsuit Apparatus' => [
            ['title' => 'Face Down', 'date' => '2006-01-01', 'youtube' => '6Ux6SlOE9Qk', 'genre' => 'Rock'],
            ['title' => 'Your Guardian Angel', 'date' => '2007-01-01', 'youtube' => 'Q7Em4fUOrZo', 'genre' => 'Rock'],
            ['title' => 'False Pretense', 'date' => '2007-01-01', 'youtube' => 'MiCZdvQXULE', 'genre' => 'Rock'],
            ['title' => 'Damn Regret', 'date' => '2008-01-01', 'youtube' => 'a0jepTxxWMs', 'genre' => 'Rock'],
        ],
        'David Cook' => [
            ['title' => 'Light On', 'date' => '2008-01-01', 'youtube' => '4i8ZCp3-n7w', 'genre' => 'Rock'],
            ['title' => 'Come Back to Me', 'date' => '2009-01-01', 'youtube' => 'XBF6IV8W-80', 'genre' => 'Rock'],
            ['title' => 'Fade Into Me', 'date' => '2011-01-01', 'youtube' => 'EJp8NZFwCEU', 'genre' => 'Rock'],
            ['title' => 'The Last Goodbye', 'date' => '2011-01-01', 'youtube' => 'F4VyubKgRG8', 'genre' => 'Rock'],
            ['title' => 'Criminals', 'date' => '2015-01-01', 'youtube' => 'LnxOnXvF6JM', 'genre' => 'Rock'],
        ],
        'Daughtry' => [
            ['title' => "It's Not Over", 'date' => '2006-01-01', 'youtube' => 'UQ92eyxnxmQ', 'genre' => 'Rock'],
            ['title' => 'Home', 'date' => '2007-01-01', 'youtube' => '7bnX-6sJZBw', 'genre' => 'Rock'],
            ['title' => 'Over You', 'date' => '2007-01-01', 'youtube' => 'm02-RHN_hQE', 'genre' => 'Rock'],
            ['title' => 'What About Now', 'date' => '2008-01-01', 'youtube' => 'roDXSHSEuoo', 'genre' => 'Rock'],
            ['title' => 'No Surprise', 'date' => '2009-01-01', 'youtube' => 'jNwENsRgC0o', 'genre' => 'Rock'],
            ['title' => 'Start of Something Good', 'date' => '2012-01-01', 'youtube' => 'WKsyxZWQ_g0', 'genre' => 'Rock'],
            ['title' => 'Waiting for Superman', 'date' => '2013-01-01', 'youtube' => 'SXjXKT98esw', 'genre' => 'Rock'],
            ['title' => 'Separate Ways (Worlds Apart)', 'date' => '2023-01-01', 'youtube' => 'I9iXHAgjXhA', 'genre' => 'Rock'],
            ['title' => 'Pieces', 'date' => '2024-01-01', 'youtube' => 'ORbAJF2nhKI', 'genre' => 'Rock'],
        ],
        'The Script' => [
            ['title' => "The Man Who Can't Be Moved", 'date' => '2008-01-01', 'youtube' => 'gS9o1FAszdk', 'genre' => 'Pop'],
            ['title' => 'Breakeven', 'date' => '2008-01-01', 'youtube' => 'MzCLLHscMOw', 'genre' => 'Pop'],
            ['title' => 'For the First Time', 'date' => '2010-01-01', 'youtube' => '3DTQsJ6ZaOQ', 'genre' => 'Pop'],
            ['title' => 'Six Degrees of Separation', 'date' => '2012-01-01', 'youtube' => 'FCT6Mu-pOeE', 'genre' => 'Pop'],
            ['title' => 'Hall of Fame', 'date' => '2012-01-01', 'youtube' => 'mk48xRzuNvA', 'genre' => 'Pop'],
            ['title' => 'Millionaires', 'date' => '2013-01-01', 'youtube' => 'gV1-M4uRiiw', 'genre' => 'Pop'],
            ['title' => 'If You Could See Me Now', 'date' => '2013-01-01', 'youtube' => 'SGlkwKA-t_4', 'genre' => 'Pop'],
            ['title' => 'Superheroes', 'date' => '2014-01-01', 'youtube' => 'WIm1GgfRz6M', 'genre' => 'Pop'],
            ['title' => 'No Good in Goodbye', 'date' => '2014-01-01', 'youtube' => 'ho9xM9n2USA', 'genre' => 'Pop'],
        ],
        'Whitney Houston' => [
            ['title' => 'I Will Always Love You', 'date' => '1992-11-02', 'youtube' => '3JWTaaS7LdU', 'genre' => 'R&B'],
        ],
        'Celine Dion' => [
            ['title' => 'My Heart Will Go On', 'date' => '1997-11-24', 'youtube' => '9bFHsd3o1w0', 'genre' => 'Pop'],
        ],
        'Mariah Carey' => [
            ['title' => 'Hero', 'date' => '1993-10-18', 'youtube' => '0IA3ZvCkRkQ', 'genre' => 'Pop'],
        ],
        'Boyz II Men' => [
            ['title' => 'End of the Road', 'date' => '1992-06-30', 'youtube' => 'zDKO6XYXioc', 'genre' => 'R&B'],
        ],
        'Toni Braxton' => [
            ['title' => 'Un-Break My Heart', 'date' => '1996-10-07', 'youtube' => 'p2Rch6WvPJE', 'genre' => 'R&B'],
        ],
        'Bryan Adams' => [
            ['title' => '(Everything I Do) I Do It for You', 'date' => '1991-06-17', 'youtube' => 'Y0pdQU87dc8', 'genre' => 'Rock'],
        ],
        'Extreme' => [
            ['title' => 'More Than Words', 'date' => '1991-03-12', 'youtube' => 'UrIiLvg58SY', 'genre' => 'Rock'],
        ],
        'Roxette' => [
            ['title' => 'It Must Have Been Love', 'date' => '1990-01-01', 'youtube' => 'k2C5TjS2sh4', 'genre' => 'Pop'],
        ],
        'Berlin' => [
            ['title' => 'Take My Breath Away', 'date' => '1986-01-01', 'youtube' => 'Bx51eegLTY8', 'genre' => 'Pop'],
        ],
        'Foreigner' => [
            ['title' => 'I Want to Know What Love Is', 'date' => '1984-11-21', 'youtube' => 'r3Pr1_v7hsw', 'genre' => 'Rock'],
        ],
        'Richard Marx' => [
            ['title' => 'Right Here Waiting', 'date' => '1989-06-21', 'youtube' => 'S_E2EHVxNAE', 'genre' => 'Pop'],
        ],
        'Bonnie Tyler' => [
            ['title' => 'Total Eclipse of the Heart', 'date' => '1983-01-01', 'youtube' => 'lcOxhH8N3Bo', 'genre' => 'Rock'],
        ],
        'Sade' => [
            ['title' => 'No Ordinary Love', 'date' => '1992-09-01', 'youtube' => '_WcWHZc8s2I', 'genre' => 'R&B'],
        ],
        'Shania Twain' => [
            ['title' => "You're Still the One", 'date' => '1998-01-13', 'youtube' => 'KNZH-emehxA', 'genre' => 'Country'],
        ],
        'Vanessa Williams' => [
            ['title' => 'Save the Best for Last', 'date' => '1992-01-14', 'youtube' => '5EdmHSTwmWY', 'genre' => 'R&B'],
        ],
    ];

    public function run(): void
    {
        foreach (self::SONGS as $artistName => $songs) {
            $artist = Artist::query()->where('slug', Str::slug($artistName))->first();

            if (! $artist) {
                continue;
            }

            foreach ($songs as $song) {
                $genre = Genre::query()->where('slug', Str::slug($song['genre']))->first();

                if (! $genre) {
                    continue;
                }

                Song::query()->firstOrCreate(
                    ['youtube_video_id' => $song['youtube']],
                    [
                        'artist_id' => $artist->id,
                        'genre_id' => $genre->id,
                        'title' => $song['title'],
                        // Artist-prefixed to avoid colliding with a same-titled
                        // song by a different artist (e.g. "Home" exists for
                        // both Westlife and Daughtry). Existing rows keep
                        // whatever slug they were created with, since this
                        // only affects new inserts.
                        'slug' => Str::slug($artistName.' '.$song['title']),
                        'release_date' => $song['date'],
                        'is_active' => true,
                        'voting_enabled' => true,
                    ],
                );
            }
        }
    }
}
