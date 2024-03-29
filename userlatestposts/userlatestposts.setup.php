<?php
/* ====================
[BEGIN_COT_EXT]
Name=Recent user posts in forums
Category=misc-ext
Description=Will display the newest post in forums from the user
Version=0.0.4
Date=2023-04-01
Author=Neocrome & Cotonti Team, Dayver, esclkm
Copyright=Partial copyright (c) 2014 Cotonti Team
Notes=BSD License
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
Requires_modules=forums
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
countonpage=01:select:3,5,10,15,20,25,30,35,40:5:Displayed posts on page
ajax=02:radio::1:Turn on AJAX navigation
timeback=03:select:2,3,5,7,10,15,30:7:Period for stats count (in days)
[END_COT_EXT_CONFIG]
==================== */
defined('COT_CODE') or die('Wrong URL.');
